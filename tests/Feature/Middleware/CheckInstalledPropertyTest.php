<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\CheckInstalled;
use Eris\Generator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Tests\PropertyTestCase;

class CheckInstalledPropertyTest extends PropertyTestCase
{
    private string $installedFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installedFilePath = storage_path('installed');
        
        // Garantir que o arquivo não existe no início
        if (File::exists($this->installedFilePath)) {
            File::delete($this->installedFilePath);
        }
    }

    protected function tearDown(): void
    {
        // Limpar arquivo de teste
        if (File::exists($this->installedFilePath)) {
            File::delete($this->installedFilePath);
        }
        
        parent::tearDown();
    }

    /**
     * Propriedade 3: Middleware de Instalação Bloqueia Acesso Após Conclusão
     * Valida: Requisito 1.7
     * 
     * Para qualquer rota /install/* com arquivo storage/installed presente,
     * o middleware deve redirecionar para /
     */
    public function testProperty3_MiddlewareRedirectsInstallRoutesWhenInstalled()
    {
        $this->forAll(
            Generator\choose(1, 10)->map(function ($segments) {
                // Gerar rotas /install/* aleatórias
                $paths = ['install'];
                for ($i = 0; $i < $segments; $i++) {
                    $paths[] = Generator\elements(['step1', 'step2', 'config', 'database', 'admin', 'finish'])
                        ->realize();
                }
                return '/' . implode('/', $paths);
            })
        )->then(function ($installRoute) {
            // Criar arquivo installed
            File::put($this->installedFilePath, 'installed');
            
            // Criar request para rota de instalação
            $request = Request::create($installRoute, 'GET');
            
            // Executar middleware
            $middleware = new CheckInstalled();
            $response = $middleware->handle($request, function () {
                return new Response('Should not reach here');
            });
            
            // Verificar redirecionamento para /
            $this->assertEquals(302, $response->getStatusCode());
            $this->assertEquals('/', $response->headers->get('Location'));
        });
    }

    /**
     * Propriedade complementar: Middleware permite acesso a rotas de instalação quando não instalado
     */
    public function testProperty3_Complement_MiddlewareAllowsInstallRoutesWhenNotInstalled()
    {
        $this->forAll(
            Generator\choose(1, 5)->map(function ($segments) {
                // Gerar rotas /install/* aleatórias
                $paths = ['install'];
                for ($i = 0; $i < $segments; $i++) {
                    $paths[] = Generator\elements(['step1', 'step2', 'config', 'database'])
                        ->realize();
                }
                return '/' . implode('/', $paths);
            })
        )->then(function ($installRoute) {
            // Garantir que arquivo installed NÃO existe
            if (File::exists($this->installedFilePath)) {
                File::delete($this->installedFilePath);
            }
            
            // Criar request para rota de instalação
            $request = Request::create($installRoute, 'GET');
            
            // Executar middleware
            $middleware = new CheckInstalled();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Installation page');
            });
            
            // Verificar que o próximo middleware foi chamado (sem redirecionamento)
            $this->assertTrue($nextCalled, "Next middleware should be called for install routes when not installed");
            $this->assertEquals(200, $response->getStatusCode());
        });
    }

    /**
     * Propriedade complementar: Middleware redireciona rotas não-install para /install quando não instalado
     */
    public function testProperty3_Complement_MiddlewareRedirectsNonInstallRoutesToInstall()
    {
        $this->forAll(
            Generator\elements([
                '/',
                '/admin',
                '/admin/dashboard',
                '/admin/services',
                '/blog',
                '/contato',
                '/sobre'
            ])
        )->then(function ($route) {
            // Garantir que arquivo installed NÃO existe
            if (File::exists($this->installedFilePath)) {
                File::delete($this->installedFilePath);
            }
            
            // Criar request para rota não-install
            $request = Request::create($route, 'GET');
            
            // Executar middleware
            $middleware = new CheckInstalled();
            $response = $middleware->handle($request, function () {
                return new Response('Should not reach here');
            });
            
            // Verificar redirecionamento para /install
            $this->assertEquals(302, $response->getStatusCode());
            $this->assertEquals('/install', $response->headers->get('Location'));
        });
    }

    /**
     * Propriedade complementar: Middleware permite acesso normal quando instalado e rota não é install
     */
    public function testProperty3_Complement_MiddlewareAllowsNormalAccessWhenInstalled()
    {
        $this->forAll(
            Generator\elements([
                '/',
                '/admin',
                '/admin/dashboard',
                '/admin/services',
                '/blog',
                '/contato',
                '/sobre'
            ])
        )->then(function ($route) {
            // Criar arquivo installed
            File::put($this->installedFilePath, 'installed');
            
            // Criar request para rota normal
            $request = Request::create($route, 'GET');
            
            // Executar middleware
            $middleware = new CheckInstalled();
            $nextCalled = false;
            
            $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
                $nextCalled = true;
                return new Response('Normal page');
            });
            
            // Verificar que o próximo middleware foi chamado (sem redirecionamento)
            $this->assertTrue($nextCalled, "Next middleware should be called for normal routes when installed");
            $this->assertEquals(200, $response->getStatusCode());
        });
    }
}