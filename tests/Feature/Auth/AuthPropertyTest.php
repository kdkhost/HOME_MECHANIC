<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Requests\LoginRequest;
use Eris\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\PropertyTestCase;

class AuthPropertyTest extends PropertyTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar tabelas necessárias
        $this->artisan('migrate');
    }

    /**
     * Propriedade 4: Rate Limiter Bloqueia Após N Tentativas Inválidas
     * Valida: Requisito 2.3
     * 
     * Para qualquer IP, após 5 tentativas inválidas de login,
     * o sistema deve retornar HTTP 429 com tempo restante
     */
    public function testProperty4_RateLimiterBlocksAfterFailedAttempts()
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
            // Gerar credenciais inválidas
            Generator\associative([
                'email' => Generator\string()->map(function($str) { 
                    return substr(md5($str), 0, 8) . '@invalid.com'; 
                }),
                'password' => Generator\string()->map(function($str) { 
                    return 'invalid_' . $str; 
                })
            ])
        )->then(function ($ip, $credentials) {
            // Limpar rate limiting anterior
            $key = 'login.' . $ip;
            RateLimiter::clear($key);
            
            // Simular 5 tentativas falhadas
            for ($i = 1; $i <= 5; $i++) {
                $request = Request::create('/admin/login', 'POST', $credentials);
                $request->server->set('REMOTE_ADDR', $ip);
                
                $controller = new AuthController();
                $loginRequest = LoginRequest::createFrom($request);
                
                $response = $controller->login($loginRequest);
                
                if ($i < 5) {
                    // Primeiras 4 tentativas: deve retornar 401
                    $this->assertEquals(401, $response->getStatusCode());
                    
                    $data = json_decode($response->getContent(), true);
                    $this->assertFalse($data['success']);
                    $this->assertArrayHasKey('attempts_left', $data);
                    $this->assertEquals(5 - $i, $data['attempts_left']);
                } else {
                    // 5ª tentativa: deve retornar 429 (rate limited)
                    $this->assertEquals(429, $response->getStatusCode());
                    
                    $data = json_decode($response->getContent(), true);
                    $this->assertFalse($data['success']);
                    $this->assertArrayHasKey('retry_after', $data);
                    $this->assertGreaterThan(0, $data['retry_after']);
                }
            }
            
            // Tentativa adicional deve continuar bloqueada
            $request = Request::create('/admin/login', 'POST', $credentials);
            $request->server->set('REMOTE_ADDR', $ip);
            
            $controller = new AuthController();
            $loginRequest = LoginRequest::createFrom($request);
            
            $response = $controller->login($loginRequest);
            $this->assertEquals(429, $response->getStatusCode());
        });
    }

    /**
     * Propriedade 5: Sanitização Remove Todas as Tags HTML da Entrada
     * Valida: Requisitos 2.5, 14.1
     * 
     * Para qualquer entrada contendo tags HTML/scripts,
     * a sanitização deve remover completamente as tags
     */
    public function testProperty5_SanitizationRemovesAllHtmlTags()
    {
        $this->forAll(
            // Gerar strings com tags HTML maliciosas
            Generator\string()->map(function ($baseStr) {
                $maliciousTags = [
                    '<script>alert("xss")</script>',
                    '<img src="x" onerror="alert(1)">',
                    '<svg onload="alert(1)">',
                    '<iframe src="javascript:alert(1)">',
                    '<object data="javascript:alert(1)">',
                    '<embed src="javascript:alert(1)">',
                    '<link rel="stylesheet" href="javascript:alert(1)">',
                    '<style>body{background:url("javascript:alert(1)")}</style>',
                    '<div onclick="alert(1)">',
                    '<a href="javascript:alert(1)">',
                    '"><script>alert(1)</script>',
                    '\';alert(1);//',
                    '<META HTTP-EQUIV="refresh" CONTENT="0;url=javascript:alert(1)">',
                ];
                
                $randomTag = Generator\elements($maliciousTags)->realize();
                return $baseStr . $randomTag . '@test.com';
            })
        )->then(function ($maliciousEmail) {
            // Criar request com e-mail malicioso
            $request = Request::create('/admin/login', 'POST', [
                'email' => $maliciousEmail,
                'password' => 'test123'
            ]);
            
            $loginRequest = LoginRequest::createFrom($request);
            
            // Verificar que tags HTML foram removidas
            $sanitizedEmail = $loginRequest->input('email');
            
            // Verificar que não contém tags HTML
            $this->assertEquals(
                strip_tags($maliciousEmail),
                $sanitizedEmail,
                "HTML tags should be completely removed from email input"
            );
            
            // Verificar que não contém caracteres perigosos
            $dangerousPatterns = [
                '<script',
                '</script>',
                'javascript:',
                'onerror=',
                'onload=',
                'onclick=',
                'alert(',
                '<iframe',
                '<object',
                '<embed',
                '<svg',
                '<img',
                'HTTP-EQUIV'
            ];
            
            foreach ($dangerousPatterns as $pattern) {
                $this->assertStringNotContainsString(
                    $pattern,
                    $sanitizedEmail,
                    "Sanitized email should not contain dangerous pattern: {$pattern}"
                );
            }
            
            // Verificar que e-mail ainda é válido se era válido originalmente
            $cleanBase = preg_replace('/<[^>]*>/', '', $maliciousEmail);
            if (filter_var($cleanBase, FILTER_VALIDATE_EMAIL)) {
                $this->assertNotEmpty($sanitizedEmail, "Valid email should not become empty after sanitization");
            }
        });
    }

    /**
     * Propriedade 6: Hash de Senha é Round-Trip Verificável com Custo 12
     * Valida: Requisito 2.7
     * 
     * Para qualquer senha não vazia, o hash bcrypt deve ser verificável
     * e usar custo 12
     */
    public function testProperty6_PasswordHashIsRoundTripVerifiableWithCost12()
    {
        $this->forAll(
            // Gerar senhas aleatórias não vazias
            Generator\string()->suchThat(function ($str) {
                return !empty(trim($str)) && strlen($str) >= 1;
            })
        )->then(function ($password) {
            // Criar usuário com senha
            $user = User::factory()->create([
                'password' => Hash::make($password)
            ]);
            
            // Verificar que hash foi criado
            $this->assertNotEmpty($user->password);
            $this->assertNotEquals($password, $user->password, "Password should be hashed, not stored in plain text");
            
            // Verificar que é um hash bcrypt válido
            $this->assertTrue(
                Hash::needsRehash($user->password) === false || Hash::check($password, $user->password),
                "Password hash should be valid bcrypt hash"
            );
            
            // Verificar round-trip (hash -> verify)
            $this->assertTrue(
                Hash::check($password, $user->password),
                "Original password should verify against its hash"
            );
            
            // Verificar que senha incorreta não passa
            $wrongPassword = $password . '_wrong';
            $this->assertFalse(
                Hash::check($wrongPassword, $user->password),
                "Wrong password should not verify against hash"
            );
            
            // Verificar custo 12 (bcrypt)
            $hashInfo = password_get_info($user->password);
            if ($hashInfo['algo'] === PASSWORD_BCRYPT) {
                $this->assertEquals(
                    12,
                    $hashInfo['options']['cost'] ?? 10,
                    "Bcrypt hash should use cost 12"
                );
            }
            
            // Verificar que hash começa com $2y$ (bcrypt identifier)
            $this->assertStringStartsWith(
                '$2y$',
                $user->password,
                "Hash should be bcrypt format starting with \$2y\$"
            );
        });
    }

    /**
     * Propriedade complementar: Login bem-sucedido limpa rate limiting
     */
    public function testProperty4_Complement_SuccessfulLoginClearsRateLimit()
    {
        $this->forAll(
            // Gerar IP aleatório
            Generator\choose(1, 254)->map(function ($a) {
                return "192.168.1.{$a}";
            })
        )->then(function ($ip) {
            // Criar usuário válido
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password123')
            ]);
            
            $key = 'login.' . $ip;
            
            // Simular algumas tentativas falhadas
            RateLimiter::hit($key, 600);
            RateLimiter::hit($key, 600);
            
            $this->assertEquals(2, RateLimiter::attempts($key));
            
            // Login bem-sucedido
            $request = Request::create('/admin/login', 'POST', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);
            $request->server->set('REMOTE_ADDR', $ip);
            
            $controller = new AuthController();
            $loginRequest = LoginRequest::createFrom($request);
            
            $response = $controller->login($loginRequest);
            
            // Verificar que login foi bem-sucedido
            $this->assertEquals(200, $response->getStatusCode());
            
            $data = json_decode($response->getContent(), true);
            $this->assertTrue($data['success']);
            
            // Verificar que rate limiting foi limpo
            $this->assertEquals(0, RateLimiter::attempts($key));
        });
    }

    /**
     * Propriedade complementar: Sanitização preserva e-mails válidos
     */
    public function testProperty5_Complement_SanitizationPreservesValidEmails()
    {
        $this->forAll(
            // Gerar e-mails válidos
            Generator\string()->map(function ($str) {
                $clean = preg_replace('/[^a-zA-Z0-9]/', '', $str);
                return substr($clean ?: 'test', 0, 10) . '@example.com';
            })
        )->then(function ($validEmail) {
            $request = Request::create('/admin/login', 'POST', [
                'email' => $validEmail,
                'password' => 'test123'
            ]);
            
            $loginRequest = LoginRequest::createFrom($request);
            $sanitizedEmail = $loginRequest->input('email');
            
            // E-mail válido deve ser preservado (apenas convertido para minúsculas)
            $this->assertEquals(
                strtolower($validEmail),
                $sanitizedEmail,
                "Valid email should be preserved after sanitization"
            );
            
            // Deve continuar sendo um e-mail válido
            $this->assertTrue(
                filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL) !== false,
                "Sanitized email should remain valid"
            );
        });
    }
}