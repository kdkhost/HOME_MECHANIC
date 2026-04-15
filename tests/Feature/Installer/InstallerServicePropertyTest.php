<?php

namespace Tests\Feature\Installer;

use App\Modules\Installer\Services\InstallerService;
use Eris\Generator;
use Tests\PropertyTestCase;

class InstallerServicePropertyTest extends PropertyTestCase
{
    private InstallerService $installerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installerService = new InstallerService();
    }

    /**
     * Propriedade 1: Verificação de Requisitos Reflete Estado Real
     * Valida: Requisito 1.2
     * 
     * Para qualquer subconjunto de extensões PHP, checkRequirements() deve retornar
     * exatamente as extensões ausentes no sistema
     */
    public function testProperty1_RequirementsCheckReflectsActualState()
    {
        $this->forAll(
            // Gerar número de extensões para testar
            Generator\choose(1, 5)
        )->then(function ($count) {
            $allExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd'];
            
            // Selecionar extensões aleatórias para verificar
            $selectedExtensions = [];
            for ($i = 0; $i < $count; $i++) {
                $randomIndex = array_rand($allExtensions);
                $extension = $allExtensions[$randomIndex];
                if (!in_array($extension, $selectedExtensions)) {
                    $selectedExtensions[] = $extension;
                }
            }
            
            $requirements = $this->installerService->checkRequirements();
            
            // Verificar que cada extensão testada tem status correto
            foreach ($selectedExtensions as $extension) {
                if (isset($requirements['extensions'][$extension])) {
                    $expectedStatus = extension_loaded($extension);
                    $actualStatus = $requirements['extensions'][$extension]['status'];
                    
                    $this->assertEquals(
                        $expectedStatus,
                        $actualStatus,
                        "Extension '{$extension}' status should match actual system state"
                    );
                }
            }
            
            // Verificar que PHP version check é consistente
            $expectedPhpStatus = version_compare(PHP_VERSION, '8.4.0', '>=');
            $actualPhpStatus = $requirements['php_version']['status'];
            
            $this->assertEquals(
                $expectedPhpStatus,
                $actualPhpStatus,
                "PHP version check should reflect actual PHP version"
            );
            
            // Verificar que current version é reportada corretamente
            $this->assertEquals(
                PHP_VERSION,
                $requirements['php_version']['current'],
                "Current PHP version should be reported correctly"
            );
        });
    }

    /**
     * Propriedade 2: Mensagem de Erro de Instalação Não Expõe Credenciais
     * Valida: Requisito 1.5
     * 
     * Para qualquer conjunto de credenciais de banco (incluindo senhas),
     * mensagens de erro não devem conter informações sensíveis
     */
    public function testProperty2_DatabaseErrorMessageDoesNotExposeCredentials()
    {
        $this->forAll(
            // Gerar configurações de banco aleatórias
            Generator\associative([
                'host' => Generator\elements(['localhost', '127.0.0.1', 'invalid-host', 'db.example.com']),
                'port' => Generator\choose(1000, 9999),
                'database' => Generator\elements(['test_db', 'invalid_db', 'db_test', 'random_database']),
                'username' => Generator\elements(['root', 'admin', 'user123', 'test_user', 'invalid_user']),
                'password' => Generator\elements(['password123', 'secret_pass', 'admin123', 'test_password', 'super_secret'])
            ])
        )->then(function ($dbConfig) {
            // Testar conexão (que deve falhar com dados aleatórios)
            $result = $this->installerService->testDatabaseConnection($dbConfig);
            
            // Verificar que a resposta tem estrutura esperada
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('message', $result);
            
            // Se falhou (esperado), verificar que credenciais não estão expostas
            if (!$result['success']) {
                $message = $result['message'];
                
                // Verificar que senha não aparece na mensagem
                if (!empty($dbConfig['password'])) {
                    $this->assertStringNotContainsString(
                        $dbConfig['password'],
                        $message,
                        "Password should not be exposed in error message"
                    );
                }
                
                // Verificar que usuário não aparece na mensagem (pode ser sensível)
                $this->assertStringNotContainsString(
                    $dbConfig['username'],
                    $message,
                    "Username should not be exposed in error message"
                );
                
                // Verificar que mensagem é genérica e segura
                $this->assertStringContainsString(
                    'banco de dados',
                    strtolower($message),
                    "Error message should be generic and mention database"
                );
                
                // Verificar que não contém detalhes técnicos sensíveis
                $sensitiveTerms = ['PDOException', 'Connection refused', 'Access denied', 'Unknown database'];
                foreach ($sensitiveTerms as $term) {
                    $this->assertStringNotContainsString(
                        $term,
                        $message,
                        "Error message should not contain sensitive technical details: {$term}"
                    );
                }
            }
        });
    }

    /**
     * Propriedade complementar: checkRequirements retorna estrutura consistente
     */
    public function testProperty1_Complement_RequirementsStructureIsConsistent()
    {
        $this->forAll(
            Generator\choose(1, 10) // Executar múltiplas vezes
        )->then(function ($iteration) {
            $requirements = $this->installerService->checkRequirements();
            
            // Verificar estrutura básica
            $this->assertIsArray($requirements);
            $this->assertArrayHasKey('php_version', $requirements);
            $this->assertArrayHasKey('extensions', $requirements);
            $this->assertArrayHasKey('mod_rewrite', $requirements);
            
            // Verificar estrutura de php_version
            $phpVersion = $requirements['php_version'];
            $this->assertArrayHasKey('name', $phpVersion);
            $this->assertArrayHasKey('required', $phpVersion);
            $this->assertArrayHasKey('status', $phpVersion);
            $this->assertArrayHasKey('current', $phpVersion);
            $this->assertIsBool($phpVersion['status']);
            $this->assertIsBool($phpVersion['required']);
            
            // Verificar estrutura de extensions
            $this->assertIsArray($requirements['extensions']);
            foreach ($requirements['extensions'] as $extension => $info) {
                $this->assertArrayHasKey('name', $info);
                $this->assertArrayHasKey('required', $info);
                $this->assertArrayHasKey('status', $info);
                $this->assertIsBool($info['status']);
                $this->assertIsBool($info['required']);
            }
            
            // Verificar estrutura de mod_rewrite
            $modRewrite = $requirements['mod_rewrite'];
            $this->assertArrayHasKey('name', $modRewrite);
            $this->assertArrayHasKey('required', $modRewrite);
            $this->assertArrayHasKey('status', $modRewrite);
            $this->assertIsBool($modRewrite['status']);
            $this->assertIsBool($modRewrite['required']);
        });
    }

    /**
     * Propriedade complementar: testDatabaseConnection sempre retorna estrutura válida
     */
    public function testProperty2_Complement_DatabaseTestReturnsValidStructure()
    {
        $this->forAll(
            // Gerar configurações válidas e inválidas
            Generator\associative([
                'host' => Generator\elements(['localhost', '127.0.0.1', '', 'invalid']),
                'port' => Generator\choose(1, 65535),
                'database' => Generator\oneOf(
                    Generator\string(),
                    Generator\constant(''),
                    Generator\constant('test_db')
                ),
                'username' => Generator\oneOf(
                    Generator\string(),
                    Generator\constant(''),
                    Generator\constant('root')
                ),
                'password' => Generator\oneOf(
                    Generator\string(),
                    Generator\constant(''),
                    Generator\constant('password123')
                )
            ])
        )->then(function ($dbConfig) {
            $result = $this->installerService->testDatabaseConnection($dbConfig);
            
            // Verificar estrutura de retorno
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('message', $result);
            
            // Verificar tipos
            $this->assertIsBool($result['success']);
            $this->assertIsString($result['message']);
            
            // Verificar que mensagem não está vazia
            $this->assertNotEmpty($result['message']);
            
            // Verificar que mensagem está em português
            $this->assertTrue(
                str_contains(strtolower($result['message']), 'banco') ||
                str_contains(strtolower($result['message']), 'conexão') ||
                str_contains(strtolower($result['message']), 'sucesso') ||
                str_contains(strtolower($result['message']), 'erro'),
                "Message should be in Portuguese"
            );
        });
    }
}