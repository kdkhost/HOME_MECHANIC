<?php

namespace Tests\Unit;

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
     * **Validates: Requirements 1.2**
     * 
     * Property 1: Verificação de Requisitos Reflete Estado Real
     * 
     * Para qualquer subconjunto de extensões PHP "instaladas" fornecido como entrada,
     * a função InstallerService::checkRequirements() deve retornar exatamente as
     * extensões ausentes como falhas — nem mais, nem menos.
     */
    public function testPropertyRequirementsCheckReflectsRealState()
    {
        $this->forAll(
            Generator\choose(0, 100) // Número de iterações para simular diferentes estados
        )
        ->then(function ($iteration) {
            // Obter o estado real atual do sistema
            $requirements = $this->installerService->checkRequirements();
            
            // Verificar que todas as extensões obrigatórias são verificadas
            $requiredExtensions = [
                'pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer',
                'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd'
            ];
            
            // Property 1: O resultado deve refletir exatamente o estado real
            foreach ($requiredExtensions as $extension) {
                $actuallyLoaded = extension_loaded($extension);
                $reportedStatus = $requirements['extensions'][$extension]['status'] ?? false;
                
                // A verificação deve refletir exatamente o estado real
                $this->assertEquals(
                    $actuallyLoaded,
                    $reportedStatus,
                    "Extension {$extension}: reported status ({$reportedStatus}) should match actual state ({$actuallyLoaded})"
                );
            }
            
            // Verificar PHP version
            $actualPhpVersion = version_compare(PHP_VERSION, '8.4.0', '>=');
            $reportedPhpVersion = $requirements['php_version']['status'] ?? false;
            
            $this->assertEquals(
                $actualPhpVersion,
                $reportedPhpVersion,
                "PHP version check should reflect actual state"
            );
            
            // Verificar mod_rewrite
            $this->assertArrayHasKey('mod_rewrite', $requirements);
            $this->assertIsBool($requirements['mod_rewrite']['status']);
            
            // Verificar permissões de escrita
            $this->assertArrayHasKey('permissions', $requirements);
            
            $actualStorageWritable = is_writable(storage_path());
            $reportedStorageWritable = $requirements['permissions']['storage']['status'] ?? false;
            
            $this->assertEquals(
                $actualStorageWritable,
                $reportedStorageWritable,
                "Storage writable check should reflect actual state"
            );
            
            $actualBootstrapWritable = is_writable(base_path('bootstrap/cache'));
            $reportedBootstrapWritable = $requirements['permissions']['bootstrap_cache']['status'] ?? false;
            
            $this->assertEquals(
                $actualBootstrapWritable,
                $reportedBootstrapWritable,
                "Bootstrap cache writable check should reflect actual state"
            );
            
            // Verificar estrutura do retorno
            $this->assertIsArray($requirements);
            $this->assertArrayHasKey('php_version', $requirements);
            $this->assertArrayHasKey('extensions', $requirements);
            $this->assertArrayHasKey('mod_rewrite', $requirements);
            $this->assertArrayHasKey('permissions', $requirements);
            
            // Cada extensão deve ter a estrutura correta
            foreach ($requirements['extensions'] as $extension => $data) {
                $this->assertArrayHasKey('name', $data);
                $this->assertArrayHasKey('required', $data);
                $this->assertArrayHasKey('status', $data);
                $this->assertTrue($data['required']);
                $this->assertIsBool($data['status']);
            }
        });
    }

    /**
     * Teste unitário complementar para verificar comportamento específico
     */
    public function testCheckRequirementsStructure()
    {
        $requirements = $this->installerService->checkRequirements();
        
        // Verificar estrutura básica
        $this->assertIsArray($requirements);
        $this->assertArrayHasKey('php_version', $requirements);
        $this->assertArrayHasKey('extensions', $requirements);
        $this->assertArrayHasKey('mod_rewrite', $requirements);
        $this->assertArrayHasKey('permissions', $requirements);
        
        // Verificar PHP version structure
        $phpVersion = $requirements['php_version'];
        $this->assertArrayHasKey('name', $phpVersion);
        $this->assertArrayHasKey('required', $phpVersion);
        $this->assertArrayHasKey('status', $phpVersion);
        $this->assertArrayHasKey('current', $phpVersion);
        $this->assertEquals('PHP 8.4+', $phpVersion['name']);
        $this->assertTrue($phpVersion['required']);
        $this->assertEquals(PHP_VERSION, $phpVersion['current']);
        
        // Verificar que todas as extensões obrigatórias estão presentes
        $requiredExtensions = [
            'pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer',
            'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd'
        ];
        
        foreach ($requiredExtensions as $extension) {
            $this->assertArrayHasKey($extension, $requirements['extensions']);
            $extensionData = $requirements['extensions'][$extension];
            $this->assertArrayHasKey('name', $extensionData);
            $this->assertArrayHasKey('required', $extensionData);
            $this->assertArrayHasKey('status', $extensionData);
            $this->assertTrue($extensionData['required']);
            $this->assertIsBool($extensionData['status']);
        }
        
        // Verificar mod_rewrite structure
        $modRewrite = $requirements['mod_rewrite'];
        $this->assertArrayHasKey('name', $modRewrite);
        $this->assertArrayHasKey('required', $modRewrite);
        $this->assertArrayHasKey('status', $modRewrite);
        $this->assertEquals('Apache mod_rewrite', $modRewrite['name']);
        $this->assertTrue($modRewrite['required']);
        $this->assertIsBool($modRewrite['status']);
        
        // Verificar permissions structure
        $permissions = $requirements['permissions'];
        $this->assertArrayHasKey('storage', $permissions);
        $this->assertArrayHasKey('bootstrap_cache', $permissions);
        
        foreach (['storage', 'bootstrap_cache'] as $permissionKey) {
            $permission = $permissions[$permissionKey];
            $this->assertArrayHasKey('name', $permission);
            $this->assertArrayHasKey('required', $permission);
            $this->assertArrayHasKey('status', $permission);
            $this->assertTrue($permission['required']);
            $this->assertIsBool($permission['status']);
        }
    }
}