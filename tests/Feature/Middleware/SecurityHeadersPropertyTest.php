<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\SecurityHeaders;
use Eris\Generator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\PropertyTestCase;

class SecurityHeadersPropertyTest extends PropertyTestCase
{
    /**
     * Propriedade 7: Cabeçalhos de Segurança Presentes em Toda Resposta HTTP
     * Valida: Requisito 2.9
     * 
     * Para qualquer requisição HTTP, a resposta deve conter todos os 4 cabeçalhos de segurança
     */
    public function testProperty7_SecurityHeadersPresentInAllHttpResponses()
    {
        $this->forAll(
            // Gerar método HTTP aleatório
            Generator\elements(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS']),
            // Gerar rota aleatória
            Generator\elements([
                '/',
                '/admin',
                '/admin/dashboard',
                '/admin/services',
                '/admin/gallery',
                '/blog',
                '/contato',
                '/sobre',
                '/api/test',
                '/install'
            ]),
            // Gerar status code de resposta aleatório
            Generator\choose(200, 599)
        )->then(function ($method, $route, $statusCode) {
            // Criar request
            $request = Request::create($route, $method);
            
            // Executar middleware
            $middleware = new SecurityHeaders();
            $response = $middleware->handle($request, function ($req) use ($statusCode) {
                return new Response('Test content', $statusCode);
            });
            
            // Verificar que todos os 4 cabeçalhos de segurança estão presentes
            $requiredHeaders = [
                'X-Frame-Options' => 'DENY',
                'X-Content-Type-Options' => 'nosniff',
                'X-XSS-Protection' => '1; mode=block',
                'Referrer-Policy' => 'strict-origin-when-cross-origin'
            ];
            
            foreach ($requiredHeaders as $headerName => $expectedValue) {
                $this->assertTrue(
                    $response->headers->has($headerName),
                    "Header '{$headerName}' should be present in response for {$method} {$route}"
                );
                
                $this->assertEquals(
                    $expectedValue,
                    $response->headers->get($headerName),
                    "Header '{$headerName}' should have correct value for {$method} {$route}"
                );
            }
            
            // Verificar que o status code original é preservado
            $this->assertEquals($statusCode, $response->getStatusCode());
        });
    }

    /**
     * Propriedade complementar: Middleware preserva conteúdo e outros cabeçalhos da resposta
     */
    public function testProperty7_Complement_PreservesOriginalResponseContent()
    {
        $this->forAll(
            // Gerar conteúdo aleatório
            Generator\string()->map(function ($str) {
                return base64_encode($str); // Garantir conteúdo válido
            }),
            // Gerar cabeçalhos customizados
            Generator\associative([
                'Content-Type' => Generator\elements(['text/html', 'application/json', 'text/plain']),
                'Cache-Control' => Generator\elements(['no-cache', 'max-age=3600', 'public']),
                'Custom-Header' => Generator\string()
            ])
        )->then(function ($content, $customHeaders) {
            // Criar request
            $request = Request::create('/', 'GET');
            
            // Executar middleware
            $middleware = new SecurityHeaders();
            $response = $middleware->handle($request, function ($req) use ($content, $customHeaders) {
                $response = new Response($content);
                foreach ($customHeaders as $name => $value) {
                    $response->headers->set($name, $value);
                }
                return $response;
            });
            
            // Verificar que conteúdo original é preservado
            $this->assertEquals($content, $response->getContent());
            
            // Verificar que cabeçalhos customizados são preservados
            foreach ($customHeaders as $name => $value) {
                $this->assertEquals($value, $response->headers->get($name));
            }
            
            // Verificar que cabeçalhos de segurança foram adicionados
            $this->assertTrue($response->headers->has('X-Frame-Options'));
            $this->assertTrue($response->headers->has('X-Content-Type-Options'));
            $this->assertTrue($response->headers->has('X-XSS-Protection'));
            $this->assertTrue($response->headers->has('Referrer-Policy'));
        });
    }

    /**
     * Propriedade complementar: Middleware funciona com diferentes tipos de resposta
     */
    public function testProperty7_Complement_WorksWithDifferentResponseTypes()
    {
        $this->forAll(
            // Gerar diferentes tipos de resposta
            Generator\elements([
                'html' => ['content' => '<html><body>Test</body></html>', 'type' => 'text/html'],
                'json' => ['content' => '{"test": "data"}', 'type' => 'application/json'],
                'xml' => ['content' => '<?xml version="1.0"?><root>test</root>', 'type' => 'application/xml'],
                'plain' => ['content' => 'Plain text content', 'type' => 'text/plain'],
                'empty' => ['content' => '', 'type' => 'text/html']
            ])
        )->then(function ($responseData) {
            // Criar request
            $request = Request::create('/', 'GET');
            
            // Executar middleware
            $middleware = new SecurityHeaders();
            $response = $middleware->handle($request, function ($req) use ($responseData) {
                $response = new Response($responseData['content']);
                $response->headers->set('Content-Type', $responseData['type']);
                return $response;
            });
            
            // Verificar que cabeçalhos de segurança estão presentes independente do tipo
            $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
            $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
            $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
            $this->assertEquals('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
            
            // Verificar que conteúdo e tipo são preservados
            $this->assertEquals($responseData['content'], $response->getContent());
            $this->assertEquals($responseData['type'], $response->headers->get('Content-Type'));
        });
    }

    /**
     * Propriedade complementar: Middleware não duplica cabeçalhos se já existirem
     */
    public function testProperty7_Complement_DoesNotDuplicateExistingHeaders()
    {
        $this->forAll(
            // Gerar valores customizados para cabeçalhos de segurança
            Generator\associative([
                'X-Frame-Options' => Generator\elements(['SAMEORIGIN', 'ALLOWALL']),
                'X-Content-Type-Options' => Generator\elements(['custom-value']),
                'X-XSS-Protection' => Generator\elements(['0', '1']),
                'Referrer-Policy' => Generator\elements(['no-referrer', 'same-origin'])
            ])
        )->then(function ($existingHeaders) {
            // Criar request
            $request = Request::create('/', 'GET');
            
            // Executar middleware
            $middleware = new SecurityHeaders();
            $response = $middleware->handle($request, function ($req) use ($existingHeaders) {
                $response = new Response('Test content');
                // Definir cabeçalhos existentes
                foreach ($existingHeaders as $name => $value) {
                    $response->headers->set($name, $value);
                }
                return $response;
            });
            
            // Verificar que middleware sobrescreve com valores seguros
            $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
            $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
            $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
            $this->assertEquals('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
            
            // Verificar que não há duplicatas (apenas um valor por cabeçalho)
            foreach (['X-Frame-Options', 'X-Content-Type-Options', 'X-XSS-Protection', 'Referrer-Policy'] as $header) {
                $headerValues = $response->headers->all($header);
                $this->assertCount(1, $headerValues, "Header '{$header}' should have exactly one value");
            }
        });
    }
}