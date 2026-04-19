<?php

namespace App\Modules\Upload\Services;

use App\Models\User;
use App\Modules\Upload\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class UploadService
{
    private ?ImageManager $imageManager = null;

    public function __construct()
    {
        // Construtor vazio para evitar falhas de inicialização de driver no DI do Laravel
    }

    /**
     * Obter instância do ImageManager com detecção automática de driver
     */
    private function getManager(): ImageManager
    {
        if ($this->imageManager) return $this->imageManager;

        try {
            // Tenta Imagick primeiro (mais robusto)
            if (extension_loaded('imagick')) {
                return $this->imageManager = new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
            }
            // Fallback para GD
            return $this->imageManager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        } catch (\Exception $e) {
            Log::warning('Falha ao inicializar driver de imagem: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Armazenar arquivo enviado
     * Salva diretamente em public/uploads/ — compatível com CloudLinux/CageFS
     */
    public function store(UploadedFile $file, ?User $user = null): Upload
    {
        try {
            $uuid      = Str::uuid()->toString();
            $extension = strtolower($file->getClientOriginalExtension()) ?: 'bin';
            $filename  = $uuid . '.' . $extension;
            $mimeType  = $file->getMimeType();
            $subdir    = 'uploads/' . $this->getSubdirForMimeType($mimeType);
            $dir       = public_path($subdir);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Mover arquivo diretamente para public/uploads/...
            $file->move($dir, $filename);
            $storedPath = $subdir . '/' . $filename; // relativo a public/

            // Criar thumbnail se for imagem
            $thumbnailPath = null;
            if (str_starts_with($mimeType, 'image/')) {
                $thumbnailPath = $this->createThumbnail($storedPath, $uuid, $extension);
            }

            $upload = Upload::create([
                'user_id'        => $user?->id,
                'uuid'           => $uuid,
                'original_name'  => $file->getClientOriginalName(),
                'filename'       => $filename,
                'mime_type'      => $mimeType,
                'size'           => filesize(public_path($storedPath)),
                'disk'           => 'public_direct',
                'path'           => $storedPath,
                'thumbnail_path' => $thumbnailPath,
            ]);

            Log::info('Arquivo enviado com sucesso', [
                'uuid' => $uuid, 'original_name' => $file->getClientOriginalName(),
                'path' => $storedPath, 'user_id' => $user?->id,
            ]);

            return $upload;

        } catch (\Exception $e) {
            Log::error('Erro ao armazenar arquivo', [
                'error' => $e->getMessage(), 'file' => $file->getClientOriginalName(),
            ]);
            throw $e;
        }
    }

    /**
     * Criar thumbnail — salvo em public/uploads/thumbnails/
     */
    private function createThumbnail(string $storedPath, string $uuid, string $extension): ?string
    {
        try {
            $thumbnailFilename = $uuid . '_thumb.' . $extension;
            $thumbnailSubdir   = 'uploads/thumbnails';
            $thumbnailDir      = public_path($thumbnailSubdir);
            $thumbnailPath     = $thumbnailSubdir . '/' . $thumbnailFilename;

            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $originalFullPath  = public_path($storedPath);
            $thumbnailFullPath = public_path($thumbnailPath);

            $manager = $this->getManager();
            $image = $manager->read($originalFullPath);
            $image->scaleDown(width: 400, height: 300);
            $image->save($thumbnailFullPath, quality: 85);

            return $thumbnailPath;

        } catch (\Exception $e) {
            Log::warning('Erro ao criar thumbnail', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Subdiretório baseado no tipo MIME
     */
    private function getSubdirForMimeType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'images',
            str_starts_with($mimeType, 'video/') => 'videos',
            str_starts_with($mimeType, 'application/pdf') => 'documents',
            default => 'others',
        };
    }

    /**
     * Obter upload por UUID
     */
    public function getByUuid(string $uuid): ?Upload
    {
        return Upload::where('uuid', $uuid)->first();
    }

    /**
     * Obter URL pública do arquivo
     */
    public function getPublicUrl(Upload $upload): string
    {
        return asset($upload->path);
    }

    /**
     * Obter URL do thumbnail
     */
    public function getThumbnailUrl(Upload $upload): ?string
    {
        if (!$upload->thumbnail_path) return null;
        return asset($upload->thumbnail_path);
    }

    public function delete(Upload $upload): bool
    {
        try {
            $main = public_path($upload->path);
            if (file_exists($main)) @unlink($main);

            if ($upload->thumbnail_path) {
                $thumb = public_path($upload->thumbnail_path);
                if (file_exists($thumb)) @unlink($thumb);
            }

            $upload->delete();
            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao excluir arquivo', ['error' => $e->getMessage(), 'uuid' => $upload->uuid]);
            return false;
        }
    }

    /**
     * Associar upload a um modelo
     */
    public function attachToModel(Upload $upload, string $modelType, int $modelId): bool
    {
        try {
            $upload->update([
                'model_type' => $modelType,
                'model_id' => $modelId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao associar upload ao modelo', [
                'error' => $e->getMessage(),
                'uuid' => $upload->uuid,
                'model_type' => $modelType,
                'model_id' => $modelId
            ]);

            return false;
        }
    }

    /**
     * Obter uploads por modelo
     */
    public function getByModel(string $modelType, int $modelId): \Illuminate\Database\Eloquent\Collection
    {
        return Upload::where('model_type', $modelType)
                    ->where('model_id', $modelId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Limpar uploads órfãos (sem modelo associado e antigos)
     */
    public function cleanupOrphanedUploads(int $daysOld = 7): int
    {
        try {
            $cutoffDate = now()->subDays($daysOld);
            
            $orphanedUploads = Upload::whereNull('model_type')
                                   ->whereNull('model_id')
                                   ->where('created_at', '<', $cutoffDate)
                                   ->get();

            $deletedCount = 0;
            foreach ($orphanedUploads as $upload) {
                if ($this->delete($upload)) {
                    $deletedCount++;
                }
            }

            Log::info('Limpeza de uploads órfãos concluída', [
                'deleted_count' => $deletedCount,
                'days_old' => $daysOld
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Erro na limpeza de uploads órfãos', [
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Obter estatísticas de uploads
     */
    public function getStatistics(): array
    {
        try {
            $stats = [
                'total_uploads' => Upload::count(),
                'total_size' => Upload::sum('size'),
                'by_type' => Upload::selectRaw('
                    CASE 
                        WHEN mime_type LIKE "image/%" THEN "images"
                        WHEN mime_type LIKE "video/%" THEN "videos"
                        WHEN mime_type LIKE "application/pdf%" THEN "documents"
                        ELSE "others"
                    END as type,
                    COUNT(*) as count,
                    SUM(size) as total_size
                ')
                ->groupBy('type')
                ->get()
                ->keyBy('type')
                ->toArray(),
                
                'recent_uploads' => Upload::with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->toArray()
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Erro ao obter estatísticas de uploads', [
                'error' => $e->getMessage()
            ]);

            return [
                'total_uploads' => 0,
                'total_size' => 0,
                'by_type' => [],
                'recent_uploads' => []
            ];
        }
    }

    /**
     * Validar e processar múltiplos arquivos
     */
    public function storeMultiple(array $files, ?User $user = null): array
    {
        $results = [];
        $mimeValidator = new MimeValidatorService();

        foreach ($files as $file) {
            try {
                // Validar arquivo
                $validation = $mimeValidator->validate($file);
                
                if (!$validation['valid']) {
                    $results[] = [
                        'success' => false,
                        'filename' => $file->getClientOriginalName(),
                        'error' => $validation['error']
                    ];
                    continue;
                }

                // Armazenar arquivo
                $upload = $this->store($file, $user);
                
                $results[] = [
                    'success' => true,
                    'filename' => $file->getClientOriginalName(),
                    'upload' => $upload,
                    'url' => $this->getPublicUrl($upload),
                    'thumbnail_url' => $this->getThumbnailUrl($upload)
                ];

            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'filename' => $file->getClientOriginalName(),
                    'error' => 'Erro interno: ' . $e->getMessage()
                ];
            }
        }

        return $results;
    }
}