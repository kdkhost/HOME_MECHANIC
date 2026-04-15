<?php

namespace Tests\Feature\Upload;

use App\Modules\Upload\Services\MimeValidatorService;
use Eris\Generator;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Tests\PropertyTestCase;

class MimeValidatorPropertyTest extends PropertyTestCase
{
    private MimeValidatorService $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new MimeValidatorService();
    }

    /**
     * Propriedade 8: Validação de Upload Rejeita Arquivos Inválidos por MIME e Tamanho
     * Valida: Requisitos 5.3, 5.4, 5.5, 14.4
     * 
     * Para qualquer arquivo com MIME inválido ou tamanho acima do limite,
     * a validação deve retornar false com código de erro apropriado
     */
    public function testProperty8_ValidationRejectsInvalidFilesByMimeAndSize()
    {
        $this->forAll(
            // Gerar configurações de arquivo inválidas
            Generator\associative([
                'mime_type' => Generator\elements([
                    // MIME types perigosos
                    'application/x-php',
                    'application/x-httpd-php',
                    'text/x-php',
                    'application/php',
                    'application/x-executable',
                    'application/x-msdownload',
                    'application/x-bat',
                    'text/x-shellscript',
                    'application/javascript',
                    'text/html',
                    // MIME types não permitidos
                    'application/zip',
                    'application/x-rar-compressed',
                    'text/plain',
                    'audio/mpeg',
                    'application/octet-stream'
                ]),
                'size' => Generator\choose(1, 200 * 1024 * 1024), // 1 byte a 200MB
                'extension' => Generator\elements([
                    'php', 'phtml', 'php3', 'php4', 'php5', 'exe', 'bat', 
                    'cmd', 'scr', 'vbs', 'js', 'zip', 'rar', 'txt', 'mp3'
                ])
            ])
        )->then(function ($fileConfig) {
            // Criar arquivo de teste simulado
            $tempFile = tmpfile();
            $tempPath = stream_get_meta_data($tempFile)['uri'];
            
            // Escrever dados aleatórios
            fwrite($tempFile, str_repeat('A', min($fileConfig['size'], 1024)));
            
            // Criar UploadedFile mock
            $uploadedFile = new UploadedFile(
                $tempPath,
                'test.' . $fileConfig['extension'],
                $fileConfig['mime_type'],
                null,
                true // test mode
            );
            
            // Validar arquivo
            $result = $this->validator->validate($uploadedFile);
            
            // Verificar estrutura da resposta
            $this->assertIsArray($result);
            $this->assertArrayHasKey('valid', $result);
            
            // Arquivo deve ser rejeitado
            $this->assertFalse(
                $result['valid'],
                "File with MIME '{$fileConfig['mime_type']}' and extension '{$fileConfig['extension']}' should be rejected"
            );
            
            // Deve ter mensagem de erro
            $this->assertArrayHasKey('error', $result);
            $this->assertIsString($result['error']);
            $this->assertNotEmpty($result['error']);
            
            // Deve ter código de erro
            $this->assertArrayHasKey('code', $result);
            $this->assertIsString($result['code']);
            
            // Verificar códigos de erro apropriados
            $validErrorCodes = [
                'DANGEROUS_EXTENSION',
                'MIME_NOT_ALLOWED',
                'FILE_TOO_LARGE',
                'INVALID_IMAGE',
                'IMAGE_TOO_SMALL',
                'IMAGE_TOO_LARGE',
                'VALIDATION_ERROR'
            ];
            
            $this->assertContains(
                $result['code'],
                $validErrorCodes,
                "Error code '{$result['code']}' should be one of the valid error codes"
            );
            
            // Limpar arquivo temporário
            fclose($tempFile);
        });
    }

    /**
     * Propriedade complementar: Validação aceita arquivos válidos
     */
    public function testProperty8_Complement_ValidationAcceptsValidFiles()
    {
        $this->forAll(
            // Gerar configurações de arquivo válidas
            Generator\associative([
                'type' => Generator\elements(['image', 'video']),
                'size' => Generator\choose(1024, 5 * 1024 * 1024) // 1KB a 5MB
            ])
        )->then(function ($fileConfig) {
            // Configurar MIME type e extensão baseado no tipo
            $mimeTypes = [
                'image' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                'video' => ['video/mp4', 'video/webm']
            ];
            
            $extensions = [
                'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                'video' => ['mp4', 'webm']
            ];
            
            $mimeType = Generator\elements($mimeTypes[$fileConfig['type']])->realize();
            $extension = Generator\elements($extensions[$fileConfig['type']])->realize();
            
            // Criar arquivo de teste
            if ($fileConfig['type'] === 'image') {
                // Criar imagem válida
                $image = imagecreate(100, 100);
                $tempPath = tempnam(sys_get_temp_dir(), 'test_image');
                
                switch ($mimeType) {
                    case 'image/jpeg':
                        imagejpeg($image, $tempPath);
                        break;
                    case 'image/png':
                        imagepng($image, $tempPath);
                        break;
                    case 'image/gif':
                        imagegif($image, $tempPath);
                        break;
                    default:
                        imagejpeg($image, $tempPath); // fallback
                }
                
                imagedestroy($image);
            } else {
                // Para vídeos, criar arquivo com header válido
                $tempPath = tempnam(sys_get_temp_dir(), 'test_video');
                file_put_contents($tempPath, str_repeat('V', $fileConfig['size']));
            }
            
            // Criar UploadedFile
            $uploadedFile = new UploadedFile(
                $tempPath,
                'test.' . $extension,
                $mimeType,
                null,
                true // test mode
            );
            
            // Validar arquivo
            $result = $this->validator->validate($uploadedFile);
            
            // Verificar estrutura da resposta
            $this->assertIsArray($result);
            $this->assertArrayHasKey('valid', $result);
            
            // Para arquivos pequenos e válidos, deve aceitar
            if ($fileConfig['size'] <= 10 * 1024 * 1024) { // <= 10MB
                $this->assertTrue(
                    $result['valid'],
                    "Valid file with MIME '{$mimeType}' and size {$fileConfig['size']} should be accepted. Error: " . 
                    ($result['error'] ?? 'none')
                );
                
                // Deve retornar informações do arquivo
                $this->assertArrayHasKey('mime_type', $result);
                $this->assertArrayHasKey('size', $result);
                $this->assertArrayHasKey('extension', $result);
            }
            
            // Limpar arquivo temporário
            unlink($tempPath);
        });
    }

    /**
     * Propriedade complementar: Validação rejeita extensões perigosas independente do MIME
     */
    public function testProperty8_Complement_ValidationRejectsDangerousExtensions()
    {
        $this->forAll(
            // Gerar extensões perigosas
            Generator\elements([
                'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8',
                'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js',
                'jar', 'asp', 'aspx', 'jsp', 'pl', 'py', 'rb', 'sh',
                'htaccess', 'htpasswd', 'ini', 'conf', 'config'
            ]),
            // Gerar MIME type que poderia ser válido
            Generator\elements(['image/jpeg', 'image/png', 'text/plain', 'application/octet-stream'])
        )->then(function ($dangerousExtension, $mimeType) {
            // Criar arquivo temporário
            $tempFile = tmpfile();
            $tempPath = stream_get_meta_data($tempFile)['uri'];
            fwrite($tempFile, 'test content');
            
            // Criar UploadedFile com extensão perigosa
            $uploadedFile = new UploadedFile(
                $tempPath,
                'test.' . $dangerousExtension,
                $mimeType,
                null,
                true // test mode
            );
            
            // Validar arquivo
            $result = $this->validator->validate($uploadedFile);
            
            // Deve ser rejeitado por extensão perigosa
            $this->assertFalse(
                $result['valid'],
                "File with dangerous extension '{$dangerousExtension}' should always be rejected"
            );
            
            // Deve ter código de erro específico
            $this->assertEquals(
                'DANGEROUS_EXTENSION',
                $result['code'],
                "Dangerous extension should return DANGEROUS_EXTENSION error code"
            );
            
            // Mensagem deve mencionar segurança
            $this->assertStringContainsString(
                'segurança',
                strtolower($result['error']),
                "Error message should mention security"
            );
            
            // Limpar arquivo temporário
            fclose($tempFile);
        });
    }

    /**
     * Propriedade complementar: Validação verifica tamanhos máximos por tipo
     */
    public function testProperty8_Complement_ValidationChecksMaxSizePerType()
    {
        $this->forAll(
            // Gerar tipos válidos com tamanhos excessivos
            Generator\associative([
                'mime_type' => Generator\elements(['image/jpeg', 'image/png', 'video/mp4']),
                'oversized_multiplier' => Generator\choose(2, 10) // 2x a 10x o limite
            ])
        )->then(function ($config) {
            // Obter tamanho máximo para o tipo
            $maxSize = MimeValidatorService::getMaxSizeForType($config['mime_type']);
            $this->assertNotNull($maxSize, "Max size should be defined for {$config['mime_type']}");
            
            // Criar arquivo maior que o limite
            $oversizedFile = $maxSize * $config['oversized_multiplier'];
            
            // Criar arquivo temporário
            $tempFile = tmpfile();
            $tempPath = stream_get_meta_data($tempFile)['uri'];
            
            // Simular tamanho grande (não escrever realmente para economizar memória)
            $uploadedFile = $this->createMock(UploadedFile::class);
            $uploadedFile->method('isValid')->willReturn(true);
            $uploadedFile->method('getSize')->willReturn($oversizedFile);
            $uploadedFile->method('getMimeType')->willReturn($config['mime_type']);
            $uploadedFile->method('getClientOriginalExtension')->willReturn('jpg');
            $uploadedFile->method('getRealPath')->willReturn($tempPath);
            
            // Validar arquivo
            $result = $this->validator->validate($uploadedFile);
            
            // Deve ser rejeitado por tamanho
            $this->assertFalse(
                $result['valid'],
                "Oversized file ({$oversizedFile} bytes) should be rejected for type {$config['mime_type']}"
            );
            
            // Deve ter código de erro apropriado
            $this->assertEquals(
                'FILE_TOO_LARGE',
                $result['code'],
                "Oversized file should return FILE_TOO_LARGE error code"
            );
            
            // Mensagem deve mencionar tamanho
            $this->assertStringContainsString(
                'grande',
                strtolower($result['error']),
                "Error message should mention file size"
            );
            
            // Limpar arquivo temporário
            fclose($tempFile);
        });
    }

    /**
     * Propriedade complementar: Métodos estáticos retornam dados consistentes
     */
    public function testProperty8_Complement_StaticMethodsReturnConsistentData()
    {
        $this->forAll(
            Generator\choose(1, 10) // Executar múltiplas vezes
        )->then(function ($iteration) {
            // Testar getAllowedTypes
            $allowedTypes = MimeValidatorService::getAllowedTypes();
            $this->assertIsArray($allowedTypes);
            $this->assertNotEmpty($allowedTypes);
            
            // Todos devem ser strings válidas de MIME type
            foreach ($allowedTypes as $mimeType) {
                $this->assertIsString($mimeType);
                $this->assertStringContainsString('/', $mimeType);
                
                // Deve ter tamanho máximo definido
                $maxSize = MimeValidatorService::getMaxSizeForType($mimeType);
                $this->assertIsInt($maxSize);
                $this->assertGreaterThan(0, $maxSize);
                
                // Deve ser reconhecido como permitido
                $this->assertTrue(MimeValidatorService::isTypeAllowed($mimeType));
            }
            
            // Testar getClientConfig
            $clientConfig = MimeValidatorService::getClientConfig();
            $this->assertIsArray($clientConfig);
            $this->assertNotEmpty($clientConfig);
            
            foreach ($clientConfig as $config) {
                $this->assertArrayHasKey('mime_type', $config);
                $this->assertArrayHasKey('category', $config);
                $this->assertArrayHasKey('max_size', $config);
                $this->assertArrayHasKey('max_size_mb', $config);
                
                // Verificar tipos
                $this->assertIsString($config['mime_type']);
                $this->assertIsString($config['category']);
                $this->assertIsInt($config['max_size']);
                $this->assertIsFloat($config['max_size_mb']);
                
                // Verificar categorias válidas
                $this->assertContains($config['category'], ['image', 'video', 'document']);
            }
        });
    }
}