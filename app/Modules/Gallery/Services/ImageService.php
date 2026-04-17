<?php

namespace App\Modules\Gallery\Services;

use App\Modules\Upload\Services\UploadService;
use App\Modules\Upload\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private UploadService $uploadService;
    private ImageManager $imageManager;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Processar upload de imagem para galeria
     */
    public function processGalleryImage(UploadedFile $file, ?int $userId = null): array
    {
        try {
            // Fazer upload da imagem original
            $upload = $this->uploadService->store($file, $userId ? \App\Models\User::find($userId) : null);

            // Gerar thumbnail personalizado para galeria (se necessário)
            $thumbnailUpload = $this->createGalleryThumbnail($upload);

            return [
                'success' => true,
                'main_upload' => $upload,
                'thumbnail_upload' => $thumbnailUpload,
                'main_uuid' => $upload->uuid,
                'thumbnail_uuid' => $thumbnailUpload ? $thumbnailUpload->uuid : $upload->uuid
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao processar imagem da galeria', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function createGalleryThumbnail(Upload $upload): ?Upload
    {
        try {
            if (!$upload->is_image) return null;

            if ($upload->thumbnail_path) return $upload;

            $thumbnailUuid     = \Illuminate\Support\Str::uuid()->toString();
            $extension         = pathinfo($upload->filename, PATHINFO_EXTENSION);
            $thumbnailFilename = $thumbnailUuid . '_gallery_thumb.' . $extension;
            $thumbnailSubdir   = 'uploads/gallery/thumbnails';
            $thumbnailPath     = $thumbnailSubdir . '/' . $thumbnailFilename;
            $thumbnailDir      = public_path($thumbnailSubdir);

            if (!is_dir($thumbnailDir)) mkdir($thumbnailDir, 0755, true);

            $image = $this->imageManager->read(public_path($upload->path));
            $image->cover(300, 300);
            $image->save(public_path($thumbnailPath), quality: 85);

            return Upload::create([
                'user_id'        => $upload->user_id,
                'uuid'           => $thumbnailUuid,
                'original_name'  => 'thumb_' . $upload->original_name,
                'filename'       => $thumbnailFilename,
                'mime_type'      => $upload->mime_type,
                'size'           => filesize(public_path($thumbnailPath)),
                'disk'           => 'public_direct',
                'path'           => $thumbnailPath,
                'thumbnail_path' => null,
            ]);

        } catch (\Exception $e) {
            Log::warning('Erro ao criar thumbnail da galeria', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Redimensionar imagem para diferentes tamanhos
     */
    public function createMultipleSizes(Upload $upload, array $sizes = []): array
    {
        $defaultSizes = [
            'small' => ['width' => 400, 'height' => 300],
            'medium' => ['width' => 800, 'height' => 600],
            'large' => ['width' => 1200, 'height' => 900]
        ];

        $sizes = array_merge($defaultSizes, $sizes);
        $results = [];

        try {
            if (!$upload->is_image || !$upload->exists()) {
                throw new \Exception('Upload não é uma imagem válida');
            }

            $originalPath = $upload->getFullPath();
            $image = $this->imageManager->read($originalPath);

            foreach ($sizes as $sizeName => $dimensions) {
                try {
                    $resizedUuid = \Illuminate\Support\Str::uuid()->toString();
                    $extension = pathinfo($upload->filename, PATHINFO_EXTENSION);
                    $resizedFilename = $resizedUuid . "_{$sizeName}." . $extension;
                    $resizedPath = "uploads/gallery/sizes/{$resizedFilename}";
                    $fullResizedPath = storage_path('app/public/' . $resizedPath);

                    // Criar diretório se não existir
                    $resizedDir = dirname($fullResizedPath);
                    if (!is_dir($resizedDir)) {
                        mkdir($resizedDir, 0755, true);
                    }

                    // Redimensionar mantendo proporção
                    $resizedImage = clone $image;
                    $resizedImage->scaleDown(
                        width: $dimensions['width'], 
                        height: $dimensions['height']
                    );

                    // Salvar imagem redimensionada
                    $resizedImage->save($fullResizedPath, quality: 90);

                    // Criar registro no banco
                    $resizedUpload = Upload::create([
                        'user_id' => $upload->user_id,
                        'uuid' => $resizedUuid,
                        'original_name' => "{$sizeName}_{$upload->original_name}",
                        'filename' => $resizedFilename,
                        'mime_type' => $upload->mime_type,
                        'size' => filesize($fullResizedPath),
                        'disk' => 'public',
                        'path' => $resizedPath,
                        'thumbnail_path' => null
                    ]);

                    $results[$sizeName] = $resizedUpload;

                } catch (\Exception $e) {
                    Log::warning("Erro ao criar tamanho {$sizeName}", [
                        'error' => $e->getMessage(),
                        'upload_uuid' => $upload->uuid
                    ]);
                }
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Erro ao criar múltiplos tamanhos', [
                'error' => $e->getMessage(),
                'upload_uuid' => $upload->uuid
            ]);

            return [];
        }
    }

    /**
     * Otimizar imagem para web
     */
    public function optimizeForWeb(Upload $upload, int $maxWidth = 1920, int $quality = 85): ?Upload
    {
        try {
            if (!$upload->is_image || !$upload->exists()) {
                return null;
            }

            $originalPath = $upload->getFullPath();
            $image = $this->imageManager->read($originalPath);

            // Verificar se precisa otimizar
            $currentWidth = $image->width();
            if ($currentWidth <= $maxWidth && $upload->size <= 500000) { // 500KB
                return $upload; // Já está otimizada
            }

            // Criar versão otimizada
            $optimizedUuid = \Illuminate\Support\Str::uuid()->toString();
            $extension = pathinfo($upload->filename, PATHINFO_EXTENSION);
            $optimizedFilename = $optimizedUuid . '_optimized.' . $extension;
            $optimizedPath = 'uploads/gallery/optimized/' . $optimizedFilename;
            $fullOptimizedPath = storage_path('app/public/' . $optimizedPath);

            // Criar diretório se não existir
            $optimizedDir = dirname($fullOptimizedPath);
            if (!is_dir($optimizedDir)) {
                mkdir($optimizedDir, 0755, true);
            }

            // Redimensionar se necessário
            if ($currentWidth > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            // Salvar com qualidade otimizada
            $image->save($fullOptimizedPath, quality: $quality);

            // Criar registro da versão otimizada
            $optimizedUpload = Upload::create([
                'user_id' => $upload->user_id,
                'uuid' => $optimizedUuid,
                'original_name' => 'optimized_' . $upload->original_name,
                'filename' => $optimizedFilename,
                'mime_type' => $upload->mime_type,
                'size' => filesize($fullOptimizedPath),
                'disk' => 'public',
                'path' => $optimizedPath,
                'thumbnail_path' => null
            ]);

            Log::info('Imagem otimizada para web', [
                'original_uuid' => $upload->uuid,
                'optimized_uuid' => $optimizedUuid,
                'original_size' => $upload->size,
                'optimized_size' => $optimizedUpload->size,
                'compression_ratio' => round((1 - $optimizedUpload->size / $upload->size) * 100, 2) . '%'
            ]);

            return $optimizedUpload;

        } catch (\Exception $e) {
            Log::error('Erro ao otimizar imagem para web', [
                'error' => $e->getMessage(),
                'upload_uuid' => $upload->uuid
            ]);

            return null;
        }
    }

    /**
     * Obter informações detalhadas da imagem
     */
    public function getImageInfo(Upload $upload): array
    {
        try {
            if (!$upload->is_image || !$upload->exists()) {
                return [];
            }

            $imagePath = $upload->getFullPath();
            $imageInfo = getimagesize($imagePath);
            
            if (!$imageInfo) {
                return [];
            }

            $exifData = [];
            if (function_exists('exif_read_data') && in_array($upload->mime_type, ['image/jpeg', 'image/tiff'])) {
                try {
                    $exifData = exif_read_data($imagePath) ?: [];
                } catch (\Exception $e) {
                    // EXIF pode falhar, ignorar erro
                }
            }

            return [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'bits' => $imageInfo['bits'] ?? null,
                'channels' => $imageInfo['channels'] ?? null,
                'ratio' => round($imageInfo[0] / $imageInfo[1], 2),
                'megapixels' => round(($imageInfo[0] * $imageInfo[1]) / 1000000, 1),
                'exif' => $exifData,
                'file_size' => $upload->size,
                'formatted_size' => $upload->formatted_size
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao obter informações da imagem', [
                'error' => $e->getMessage(),
                'upload_uuid' => $upload->uuid
            ]);

            return [];
        }
    }
}