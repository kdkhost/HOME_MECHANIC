<?php

namespace App\Modules\Upload\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Upload\Models\Upload;
use App\Modules\Upload\Services\MimeValidatorService;
use App\Modules\Upload\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    private MimeValidatorService $mimeValidator;
    private UploadService $uploadService;

    public function __construct(
        MimeValidatorService $mimeValidator,
        UploadService $uploadService
    ) {
        $this->middleware('auth');
        $this->mimeValidator = $mimeValidator;
        $this->uploadService = $uploadService;
    }

    /**
     * Processar upload de arquivo único
     */
    public function store(Request $request)
    {
        try {
            // Validar se arquivo foi enviado
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum arquivo foi enviado.'
                ], 400);
            }

            $file = $request->file('file');

            // Validar MIME type e tamanho
            $validation = $this->mimeValidator->validate($file);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['error'],
                    'code' => $validation['code'] ?? 'VALIDATION_ERROR'
                ], 422);
            }

            // Armazenar arquivo
            $upload = $this->uploadService->store($file, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Arquivo enviado com sucesso!',
                'data' => [
                    'uuid' => $upload->uuid,
                    'url' => $upload->url,
                    'thumbnail_url' => $upload->thumbnail_url,
                    'filename' => $upload->filename,
                    'original_name' => $upload->original_name,
                    'size' => $upload->size,
                    'formatted_size' => $upload->formatted_size,
                    'mime_type' => $upload->mime_type,
                    'file_type' => $upload->file_type,
                    'icon' => $upload->icon,
                    'is_image' => $upload->is_image,
                    'is_video' => $upload->is_video
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no upload de arquivo', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $request->file('file')?->getClientOriginalName()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor. Tente novamente.',
                'code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * Processar upload de múltiplos arquivos
     */
    public function storeMultiple(Request $request)
    {
        try {
            // Validar se arquivos foram enviados
            if (!$request->hasFile('files')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum arquivo foi enviado.'
                ], 400);
            }

            $files = $request->file('files');
            
            // Validar se é array
            if (!is_array($files)) {
                $files = [$files];
            }

            // Processar múltiplos arquivos
            $results = $this->uploadService->storeMultiple($files, Auth::user());

            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $totalCount = count($results);

            return response()->json([
                'success' => $successCount > 0,
                'message' => "{$successCount} de {$totalCount} arquivos enviados com sucesso.",
                'results' => $results,
                'summary' => [
                    'total' => $totalCount,
                    'success' => $successCount,
                    'failed' => $totalCount - $successCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no upload múltiplo', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Obter informações de um upload
     */
    public function show(string $uuid)
    {
        try {
            $upload = $this->uploadService->getByUuid($uuid);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $upload->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter upload', [
                'error' => $e->getMessage(),
                'uuid' => $uuid
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.'
            ], 500);
        }
    }

    /**
     * Excluir upload
     */
    public function destroy(string $uuid)
    {
        try {
            $upload = $this->uploadService->getByUuid($uuid);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado.'
                ], 404);
            }

            // Verificar se usuário pode excluir
            if ($upload->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir este arquivo.'
                ], 403);
            }

            $deleted = $this->uploadService->delete($upload);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Arquivo excluído com sucesso.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir arquivo.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao excluir upload', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.'
            ], 500);
        }
    }

    /**
     * Listar uploads do usuário
     */
    public function index(Request $request)
    {
        try {
            $query = Upload::byUser(Auth::id())
                          ->with('user')
                          ->orderBy('created_at', 'desc');

            // Filtros
            if ($request->has('type')) {
                switch ($request->input('type')) {
                    case 'images':
                        $query->images();
                        break;
                    case 'videos':
                        $query->videos();
                        break;
                    case 'documents':
                        $query->documents();
                        break;
                }
            }

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('original_name', 'like', "%{$search}%");
            }

            // Paginação
            $perPage = min($request->input('per_page', 20), 100);
            $uploads = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $uploads->items(),
                'pagination' => [
                    'current_page' => $uploads->currentPage(),
                    'last_page' => $uploads->lastPage(),
                    'per_page' => $uploads->perPage(),
                    'total' => $uploads->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar uploads', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.'
            ], 500);
        }
    }

    /**
     * Obter configuração para o cliente
     */
    public function getConfig()
    {
        return response()->json([
            'success' => true,
            'config' => [
                'allowed_types' => MimeValidatorService::getClientConfig(),
                'max_files' => 10,
                'parallel_uploads' => 3,
                'chunk_size' => 1024 * 1024, // 1MB chunks
                'timeout' => 300000, // 5 minutos
                'retry_attempts' => 3
            ]
        ]);
    }

    /**
     * Associar upload a um modelo
     */
    public function attach(Request $request, string $uuid)
    {
        try {
            $request->validate([
                'model_type' => 'required|string',
                'model_id' => 'required|integer'
            ]);

            $upload = $this->uploadService->getByUuid($uuid);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado.'
                ], 404);
            }

            $attached = $this->uploadService->attachToModel(
                $upload,
                $request->input('model_type'),
                $request->input('model_id')
            );

            if ($attached) {
                return response()->json([
                    'success' => true,
                    'message' => 'Arquivo associado com sucesso.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao associar arquivo.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao associar upload', [
                'error' => $e->getMessage(),
                'uuid' => $uuid
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.'
            ], 500);
        }
    }

    /**
     * Obter estatísticas de uploads (admin)
     */
    public function statistics()
    {
        try {
            // Verificar se é admin
            if (!Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado.'
                ], 403);
            }

            $stats = $this->uploadService->getStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter estatísticas', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.'
            ], 500);
        }
    }
}