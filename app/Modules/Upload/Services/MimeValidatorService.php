<?php

namespace App\Modules\Upload\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class MimeValidatorService
{
    /**
     * Tipos MIME permitidos com seus respectivos tamanhos máximos (em bytes)
     */
    private const ALLOWED_TYPES = [
        // Imagens
        'image/jpeg' => 10 * 1024 * 1024, // 10MB
        'image/jpg' => 10 * 1024 * 1024,  // 10MB
        'image/png' => 10 * 1024 * 1024,  // 10MB
        'image/webp' => 10 * 1024 * 1024, // 10MB
        'image/gif' => 5 * 1024 * 1024,   // 5MB
        
        // Vídeos
        'video/mp4' => 100 * 1024 * 1024,  // 100MB
        'video/webm' => 100 * 1024 * 1024, // 100MB
        'video/quicktime' => 100 * 1024 * 1024, // 100MB (MOV)
        
        // Documentos (para futuras expansões)
        'application/pdf' => 20 * 1024 * 1024, // 20MB
    ];

    /**
     * Extensões perigosas que devem ser sempre rejeitadas
     */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8',
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js',
        'jar', 'asp', 'aspx', 'jsp', 'pl', 'py', 'rb', 'sh',
        'htaccess', 'htpasswd', 'ini', 'conf', 'config'
    ];

    /**
     * Validar arquivo enviado
     */
    public function validate(UploadedFile $file): array
    {
        try {
            // Verificar se o arquivo foi enviado corretamente
            if (!$file->isValid()) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo corrompido ou não foi enviado corretamente.',
                    'code' => 'INVALID_UPLOAD'
                ];
            }

            // Verificar extensão perigosa
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, self::DANGEROUS_EXTENSIONS)) {
                Log::warning('Tentativa de upload de arquivo perigoso', [
                    'filename' => $file->getClientOriginalName(),
                    'extension' => $extension,
                    'ip' => request()->ip()
                ]);

                return [
                    'valid' => false,
                    'error' => 'Tipo de arquivo não permitido por motivos de segurança.',
                    'code' => 'DANGEROUS_EXTENSION'
                ];
            }

            // Obter MIME type real do arquivo
            $realMimeType = $this->getRealMimeType($file);
            
            if (!$realMimeType) {
                return [
                    'valid' => false,
                    'error' => 'Não foi possível determinar o tipo do arquivo.',
                    'code' => 'UNKNOWN_MIME'
                ];
            }

            // Verificar se o MIME type é permitido
            if (!array_key_exists($realMimeType, self::ALLOWED_TYPES)) {
                return [
                    'valid' => false,
                    'error' => "Tipo de arquivo não permitido: {$realMimeType}",
                    'code' => 'MIME_NOT_ALLOWED'
                ];
            }

            // Verificar tamanho do arquivo
            $maxSize = self::ALLOWED_TYPES[$realMimeType];
            if ($file->getSize() > $maxSize) {
                $maxSizeMB = round($maxSize / (1024 * 1024), 1);
                return [
                    'valid' => false,
                    'error' => "Arquivo muito grande. Tamanho máximo: {$maxSizeMB}MB",
                    'code' => 'FILE_TOO_LARGE'
                ];
            }

            // Verificações específicas por tipo
            $specificValidation = $this->validateSpecificType($file, $realMimeType);
            if (!$specificValidation['valid']) {
                return $specificValidation;
            }

            return [
                'valid' => true,
                'mime_type' => $realMimeType,
                'size' => $file->getSize(),
                'extension' => $extension
            ];

        } catch (\Exception $e) {
            Log::error('Erro na validação de arquivo', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            return [
                'valid' => false,
                'error' => 'Erro interno na validação do arquivo.',
                'code' => 'VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Obter MIME type real do arquivo usando finfo
     */
    private function getRealMimeType(UploadedFile $file): ?string
    {
        try {
            // Usar finfo para leitura real do MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (!$finfo) {
                return null;
            }

            $mimeType = finfo_file($finfo, $file->getRealPath());
            finfo_close($finfo);

            // Normalizar alguns MIME types comuns
            $normalizedMimes = [
                'image/jpg' => 'image/jpeg',
                'video/x-msvideo' => 'video/mp4', // Alguns AVIs são detectados assim
            ];

            return $normalizedMimes[$mimeType] ?? $mimeType;

        } catch (\Exception $e) {
            Log::warning('Erro ao detectar MIME type', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            // Fallback para o MIME type reportado pelo cliente (menos seguro)
            return $file->getMimeType();
        }
    }

    /**
     * Validações específicas por tipo de arquivo
     */
    private function validateSpecificType(UploadedFile $file, string $mimeType): array
    {
        switch (true) {
            case str_starts_with($mimeType, 'image/'):
                return $this->validateImage($file);
                
            case str_starts_with($mimeType, 'video/'):
                return $this->validateVideo($file);
                
            default:
                return ['valid' => true];
        }
    }

    /**
     * Validar imagem
     */
    private function validateImage(UploadedFile $file): array
    {
        try {
            // Tentar obter informações da imagem
            $imageInfo = getimagesize($file->getRealPath());
            
            if (!$imageInfo) {
                return [
                    'valid' => false,
                    'error' => 'Arquivo não é uma imagem válida.',
                    'code' => 'INVALID_IMAGE'
                ];
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Verificar dimensões mínimas
            if ($width < 50 || $height < 50) {
                return [
                    'valid' => false,
                    'error' => 'Imagem muito pequena. Mínimo: 50x50 pixels.',
                    'code' => 'IMAGE_TOO_SMALL'
                ];
            }

            // Verificar dimensões máximas
            if ($width > 8000 || $height > 8000) {
                return [
                    'valid' => false,
                    'error' => 'Imagem muito grande. Máximo: 8000x8000 pixels.',
                    'code' => 'IMAGE_TOO_LARGE'
                ];
            }

            return ['valid' => true];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'Erro ao validar imagem.',
                'code' => 'IMAGE_VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Validar vídeo
     */
    private function validateVideo(UploadedFile $file): array
    {
        // Validações básicas de vídeo
        // Aqui poderia ser implementada validação mais avançada com FFmpeg
        
        return ['valid' => true];
    }

    /**
     * Obter lista de tipos permitidos para exibição
     */
    public static function getAllowedTypes(): array
    {
        return array_keys(self::ALLOWED_TYPES);
    }

    /**
     * Obter tamanho máximo para um tipo específico
     */
    public static function getMaxSizeForType(string $mimeType): ?int
    {
        return self::ALLOWED_TYPES[$mimeType] ?? null;
    }

    /**
     * Verificar se um tipo MIME é permitido
     */
    public static function isTypeAllowed(string $mimeType): bool
    {
        return array_key_exists($mimeType, self::ALLOWED_TYPES);
    }

    /**
     * Obter informações de tipos permitidos para JavaScript
     */
    public static function getClientConfig(): array
    {
        $config = [];
        
        foreach (self::ALLOWED_TYPES as $mimeType => $maxSize) {
            $category = str_starts_with($mimeType, 'image/') ? 'image' : 
                       (str_starts_with($mimeType, 'video/') ? 'video' : 'document');
            
            $config[] = [
                'mime_type' => $mimeType,
                'category' => $category,
                'max_size' => $maxSize,
                'max_size_mb' => round($maxSize / (1024 * 1024), 1)
            ];
        }
        
        return $config;
    }
}