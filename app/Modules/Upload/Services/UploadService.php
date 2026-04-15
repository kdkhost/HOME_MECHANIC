<?php

namespace App\Modules\Upload\Services;

use App\Models\User;
use App\Modules\Upload\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Armazenar arquivo enviado
     */
    public function store(UploadedFile $file, ?User $user = null): Upload
    {
        try {
            // Gerar UUID único para o arquivo
            $uuid = Str::uuid()->toString();
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = $uuid . '.' . $extension;
            
            // Determinar diretório baseado no tipo
            $mimeType = $file->getMimeType();
            $directory = $this->getDirectoryForMimeType($mimeType);
            
            // Caminho completo
            $path = $directory . '/' . $filename;
            
            // Armazenar arquivo
            $storedPath = $file->storeAs($directory, $filename, 'public');
            
            if (!$storedPath) {
                throw new \Exception('Falha ao armazenar arquivo no disco');
            }

            // Criar thumbnail se for imagem
            $thumbnailPath = null;
            if (str_starts_with($mimeType, 'image/')) {
                $thumbnailPath = $this->createThumbnail($storedPath, $uuid, $extension);
            }

            // Registrar no banco de dados
            $upload = Upload::create([
                'user_id' => $user?->id,
                'uuid' => $uuid,
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $storedPath,
                'thumbnail_path' => $thumbnailPath
            ]);

            Log::info('Arquivo enviado com sucesso', [
                'uuid' => $uuid,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'user_id' => $user?->id
            ]);

            return $upload;

        } catch (\Exception $e) {
            Log::error('Erro ao armazenar arquivo', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'user_id' => $user?->id
            ]);

            throw $e;
        }
    }

    /**
     * Criar thumbnail para imagem
     */
    private function createThumbnail(string $originalPath, string $uuid, string $extension): ?string
    {
        try {
            $thumbnailFilename = $uuid . '_thumb.' . $extension;
            $thumbnailPath = 'uploads/thumbnails/' . $thumbnailFilename;
            $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);

            // Criar diretório se não existir
            $thumbnailDir = dirname($fullThumbnailPath);
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Carregar imagem original
            $originalFullPath = storage_path('app/public/' . $originalPath);
            $image = $this->imageManager->read($originalFullPath);

            // Redimensionar mantendo proporção (400x300 máximo)
            $image->scaleDown(width: 400, height: 300);

            // Salvar thumbnail
            $image->save($fullThumbnailPath, quality: 85);

            return $thumbnailPath;

        } catch (\Exception $e) {
            Log::warning('Erro ao criar thumbnail', [
                'error' => $e->getMessage(),
                'original_path' => $originalPath,
                'uuid' => $uuid
            ]);

            return null;
        }
    }

    /**
     * Obter diretório baseado no tipo MIME
     */
    private function getDirectoryForMimeType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'uploads/images',
            str_starts_with($mimeType, 'video/') => 'uploads/videos',
            str_starts_with($mimeType, 'application/pdf') => 'uploads/documents',
            default => 'uploads/others'
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
        return Storage::disk('public')->url($upload->path);
    }

    /**
     * Obter URL do thumbnail
     */
    public function getThumbnailUrl(Upload $upload): ?string
    {
        if (!$upload->thumbnail_path) {
            return null;
        }

        return Storage::disk('public')->url($upload->thumbnail_path);
    }

    /**
     * Excluir arquivo
     */
    public function delete(Upload $upload): bool
    {
        try {
            // Excluir arquivo principal
            if (Storage::disk('public')->exists($upload->path)) {
                Storage::disk('public')->delete($upload->path);
            }

            // Excluir thumbnail se existir
            if ($upload->thumbnail_path && Storage::disk('public')->exists($upload->thumbnail_path)) {
                Storage::disk('public')->delete($upload->thumbnail_path);
            }

            // Remover registro do banco
            $upload->delete();

            Log::info('Arquivo excluído com sucesso', [
                'uuid' => $upload->uuid,
                'filename' => $upload->filename
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao excluir arquivo', [
                'error' => $e->getMessage(),
                'uuid' => $upload->uuid
            ]);

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