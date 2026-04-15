<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\MaintenanceMode;
use Eris\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\PropertyTestCase;

class MaintenanceModePropertyTest extends PropertyTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar tabelas necessárias para o teste
        $this->artisan('migrate');
    }

    /**
     * Propriedade 10: Modo de Manutenção Respeita Lista de IPs Permitidos
     * Valida: Requisito 10.3
     * 
     * Para qualquer conjunto de IPs permitidos e IP de requisição,
     * o middleware deve bloquear apenas IPs não permitidos quando modo manutenção ativo
     */
    public function testProperty10_MaintenanceModeRespectsAllowedIpList()
    {
        $this->forAll(
            // Gerar lista de IPs permitidos (1-5 IPs)
            Generator\choose(1, 5)->map(function ($count) {
                $ips = [];
                for ($i = 0; $i < $count; $i++) {
                    $ips[] = Generator\choose(1, 254)->map(function ($a) {
                        return Generator\choose(1, 254)->map(function ($b) use ($a) {
                            return Generator\choose(1, 254)->map(function ($c) use ($a, $b) {
                                return Generator\choose(1, 254)->map(function ($d) use ($a, $b, $c) {
                                    return "{$a}.{$b}.{$c}.{$d}";
                                })->realize();
                            })->realize();
                        })->realize();
                    })->realize();
                }
                return $ips;
            }),
            // Gerar IP de teste
            Generator\choose(1, 254)->map(function ($a) {
                return Generator\choose(1, 254)->map(function ($b) use ($a) {
                    return Generator\choose(1, 254)->map(function ($c) use ($a, $b) {
                        return Generator\choose(1, 254)->map(function ($d) use ($a, $b, $c) {
                            return "{$a}.{$b}.{$c}.{$d}";
                        })->realize();
                    })->realize();
                })->realize();
            }),
            // Gerar tipo de rota (admin ou pública)
            Generator\elements(['admin', 'public'])
        )->then(function ($allowedIps, $testIp, $routeType) {
            // Configurar modo de manutenção ativo
            DB::table('settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                ['value' => '1', 'group' => 'system']
            );
            
            // Inserir IPs permitidos
            foreach ($allowedIps as $ip) {
                DB::table('maintenance_ips')->insert([
                    'ip_address' => $ip,
                    'active' => true,
                    'label' => 'Test IP',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Determinar se IP de teste está na lista permitida
            $isAllowed = in_array($testIp, $allowedIps);
            
            // Criar rota baseada no tipo
            $route = $routeType === 'admin' ? '/admin/dashboard' : '/';
            
            // Criar request com IP de teste
            $request = Request::create($route, 'GET');
            $request->server->set('REMOTE_ADDR', $testIp);
            
            // Executar middleware
            $middleware = new MaintenanceMode();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Normal page');
            });
            
            if ($isAllowed) {
                // IP permitido: deve passar normalmente
                $this->assertTrue($nextCalled, "Allowed IP {$testIp} should pass through middleware");
                $this->assertEquals(200, $response->getStatusCode());
            } else {
                // IP não permitido: deve ser bloqueado com 503
                $this->assertFalse($nextCalled, "Non-allowed IP {$testIp} should be blocked");
                $this->assertEquals(503, $response->getStatusCode());
                $this->assertEquals('3600', $response->headers->get('Retry-After'));
            }
        });
    }

    /**
     * Propriedade complementar: Middleware permite acesso normal quando modo manutenção desativado
     */
    public function testProperty10_Complement_AllowsAccessWhenMaintenanceDisabled()
    {
        $this->forAll(
            // Gerar IP aleatório
            Generator\choose(1, 254)->map(function ($a) {
                return Generator\choose(1, 254)->map(function ($b) use ($a) {
                    return Generator\choose(1, 254)->map(function ($c) use ($a, $b) {
                        return Generator\choose(1, 254)->map(function ($d) use ($a, $b, $c) {
                            return "{$a}.{$b}.{$c}.{$d}";
                        })->realize();
                    })->realize();
                })->realize();
            }),
            // Gerar rota aleatória
            Generator\elements([
                '/',
                '/admin/dashboard',
                '/admin/services',
                '/blog',
                '/contato'
            ])
        )->then(function ($testIp, $route) {
            // Configurar modo de manutenção DESATIVADO
            DB::table('settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                ['value' => '0', 'group' => 'system']
            );
            
            // Criar request
            $request = Request::create($route, 'GET');
            $request->server->set('REMOTE_ADDR', $testIp);
            
            // Executar middleware
            $middleware = new MaintenanceMode();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Normal page');
            });
            
            // Deve sempre permitir acesso quando manutenção desativada
            $this->assertTrue($nextCalled, "Should allow access when maintenance mode is disabled");
            $this->assertEquals(200, $response->getStatusCode());
        });
    }

    /**
     * Propriedade complementar: Middleware bloqueia todos os IPs quando lista está vazia
     */
    public function testProperty10_Complement_BlocksAllIpsWhenListEmpty()
    {
        $this->forAll(
            // Gerar IP aleatório
            Generator\choose(1, 254)->map(function ($a) {
                return Generator\choose(1, 254)->map(function ($b) use ($a) {
                    return Generator\choose(1, 254)->map(function ($c) use ($a, $b) {
                        return Generator\choose(1, 254)->map(function ($d) use ($a, $b, $c) {
                            return "{$a}.{$b}.{$c}.{$d}";
                        })->realize();
                    })->realize();
                })->realize();
            })
        )->then(function ($testIp) {
            // Configurar modo de manutenção ativo
            DB::table('settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                ['value' => '1', 'group' => 'system']
            );
            
            // Garantir que lista de IPs está vazia
            DB::table('maintenance_ips')->delete();
            
            // Criar request
            $request = Request::create('/', 'GET');
            $request->server->set('REMOTE_ADDR', $testIp);
            
            // Executar middleware
            $middleware = new MaintenanceMode();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Normal page');
            });
            
            // Deve bloquear quando lista está vazia
            $this->assertFalse($nextCalled, "Should block all IPs when allowed list is empty");
            $this->assertEquals(503, $response->getStatusCode());
        });
    }

    /**
     * Propriedade complementar: Middleware funciona corretamente com IPs inativos
     */
    public function testProperty10_Complement_RespectsActiveStatusOfIps()
    {
        $this->forAll(
            // Gerar IP de teste
            Generator\choose(1, 254)->map(function ($a) {
                return Generator\choose(1, 254)->map(function ($b) use ($a) {
                    return Generator\choose(1, 254)->map(function ($c) use ($a, $b) {
                        return Generator\choose(1, 254)->map(function ($d) use ($a, $b, $c) {
                            return "{$a}.{$b}.{$c}.{$d}";
                        })->realize();
                    })->realize();
                })->realize();
            }),
            // Gerar status ativo/inativo
            Generator\bool()
        )->then(function ($testIp, $isActive) {
            // Configurar modo de manutenção ativo
            DB::table('settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                ['value' => '1', 'group' => 'system']
            );
            
            // Inserir IP com status específico
            DB::table('maintenance_ips')->insert([
                'ip_address' => $testIp,
                'active' => $isActive,
                'label' => 'Test IP',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Criar request
            $request = Request::create('/', 'GET');
            $request->server->set('REMOTE_ADDR', $testIp);
            
            // Executar middleware
            $middleware = new MaintenanceMode();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Normal page');
            });
            
            if ($isActive) {
                // IP ativo: deve passar
                $this->assertTrue($nextCalled, "Active IP should pass through");
                $this->assertEquals(200, $response->getStatusCode());
            } else {
                // IP inativo: deve ser bloqueado
                $this->assertFalse($nextCalled, "Inactive IP should be blocked");
                $this->assertEquals(503, $response->getStatusCode());
            }
        });
    }
}