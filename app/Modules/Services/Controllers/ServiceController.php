<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Services\Models\Service;
use App\Modules\Services\Requests\ServiceRequest;
use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Upload\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Listar serviços
     */
    public function index(Request $request)
    {
        try {
            $query = Service::query();

            // Filtros
            if ($request->has('search') && $request->filled('search')) {
                $query->search($request->input('search'));
            }

            if ($request->has('active') && $request->input('active') !== '') {
                $query->where('active', $request->boolean('active'));
            }

            if ($request->has('featured') && $request->input('featured') !== '') {
                $query->where('featured', $request->boolean('featured'));
            }

            // Ordenação
            $sortBy = $request->input('sort_by', 'sort_order');
            $sortDirection = $request->input('sort_direction', 'asc');
            
            if (in_array($sortBy, ['title', 'sort_order', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->ordered();
            }

            // Se for requisição AJAX, retornar JSON
            if ($request->wantsJson() || $request->ajax()) {
                $perPage = min($request->input('per_page', 15), 100);
                $services = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => ServiceResource::collection($services->items()),
                    'pagination' => [
                        'current_page' => $services->currentPage(),
                        'last_page' => $services->lastPage(),
                        'per_page' => $services->perPage(),
                        'total' => $services->total(),
                        'from' => $services->firstItem(),
                        'to' => $services->lastItem()
                    ]
                ]);
            }

            // Para acesso direto, retornar view
            $services = $query->paginate(15);
            return view('modules.services.index', compact('services'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar serviços', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar serviços.');
        }
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        return view('modules.services.create');
    }

    /**
     * Armazenar novo serviço
     */
    public function store(ServiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $service = Service::create($data);

            // Associar imagem de capa se fornecida
            if (!empty($data['cover_image'])) {
                $upload = $this->uploadService->getByUuid($data['cover_image']);
                if ($upload) {
                    $this->uploadService->attachToModel($upload, Service::class, $service->id);
                }
            }

            // Registrar no audit log
            AuditLog::record('service_created', $service, [], $service->toArray());

            DB::commit();

            Log::info('Serviço criado com sucesso', [
                'service_id' => $service->id,
                'title' => $service->title,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Serviço criado com sucesso!',
                    'data' => new ServiceResource($service)
                ], 201);
            }

            return redirect()->route('admin.services.index')
                           ->with('success', 'Serviço criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar serviço', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao criar serviço.');
        }
    }

    /**
     * Exibir serviço específico
     */
    public function show(Service $service, Request $request)
    {
        try {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => new ServiceResource($service)
                ]);
            }

            return view('modules.services.show', compact('service'));

        } catch (\Exception $e) {
            Log::error('Erro ao exibir serviço', [
                'error' => $e->getMessage(),
                'service_id' => $service->id
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar serviço.');
        }
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(Service $service)
    {
        return view('modules.services.edit', compact('service'));
    }

    /**
     * Atualizar serviço
     */
    public function update(ServiceRequest $request, Service $service)
    {
        try {
            DB::beginTransaction();

            $oldData = $service->toArray();
            $data = $request->validated();
            
            $service->update($data);

            // Gerenciar imagem de capa
            if (array_key_exists('cover_image', $data)) {
                // Remover associação anterior se existir
                if ($service->cover_image && $service->cover_image !== $data['cover_image']) {
                    $oldUpload = $this->uploadService->getByUuid($service->cover_image);
                    if ($oldUpload) {
                        $this->uploadService->attachToModel($oldUpload, null, null);
                    }
                }

                // Associar nova imagem se fornecida
                if (!empty($data['cover_image'])) {
                    $upload = $this->uploadService->getByUuid($data['cover_image']);
                    if ($upload) {
                        $this->uploadService->attachToModel($upload, Service::class, $service->id);
                    }
                }
            }

            // Registrar no audit log
            AuditLog::record('service_updated', $service, $oldData, $service->fresh()->toArray());

            DB::commit();

            Log::info('Serviço atualizado com sucesso', [
                'service_id' => $service->id,
                'title' => $service->title,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Serviço atualizado com sucesso!',
                    'data' => new ServiceResource($service->fresh())
                ]);
            }

            return redirect()->route('admin.services.index')
                           ->with('success', 'Serviço atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar serviço', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'data' => $request->validated(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao atualizar serviço.');
        }
    }

    /**
     * Excluir serviço
     */
    public function destroy(Service $service, Request $request)
    {
        try {
            DB::beginTransaction();

            $oldData = $service->toArray();

            // Remover associações de uploads
            $uploads = $this->uploadService->getByModel(Service::class, $service->id);
            foreach ($uploads as $upload) {
                $this->uploadService->attachToModel($upload, null, null);
            }

            $service->delete();

            // Registrar no audit log
            AuditLog::record('service_deleted', $service, $oldData, []);

            DB::commit();

            Log::info('Serviço excluído com sucesso', [
                'service_id' => $service->id,
                'title' => $service->title,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Serviço excluído com sucesso!'
                ]);
            }

            return redirect()->route('admin.services.index')
                           ->with('success', 'Serviço excluído com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao excluir serviço', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao excluir serviço.');
        }
    }

    /**
     * Reordenar serviços
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'services' => 'required|array',
                'services.*.id' => 'required|integer|exists:services,id',
                'services.*.sort_order' => 'required|integer|min:0'
            ]);

            DB::beginTransaction();

            foreach ($request->input('services') as $serviceData) {
                Service::where('id', $serviceData['id'])
                       ->update(['sort_order' => $serviceData['sort_order']]);
            }

            DB::commit();

            Log::info('Serviços reordenados com sucesso', [
                'services_count' => count($request->input('services')),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ordem dos serviços atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao reordenar serviços', [
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
     * Alternar status ativo/inativo
     */
    public function toggleActive(Service $service, Request $request)
    {
        try {
            $oldData = $service->toArray();
            $service->update(['active' => !$service->active]);

            // Registrar no audit log
            AuditLog::record('service_toggled', $service, $oldData, $service->fresh()->toArray());

            Log::info('Status do serviço alterado', [
                'service_id' => $service->id,
                'active' => $service->active,
                'user_id' => Auth::id()
            ]);

            $message = $service->active ? 'Serviço ativado com sucesso!' : 'Serviço desativado com sucesso!';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => new ServiceResource($service)
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao alterar status do serviço', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao alterar status do serviço.');
        }
    }

    /**
     * Alternar destaque
     */
    public function toggleFeatured(Service $service, Request $request)
    {
        try {
            $oldData = $service->toArray();
            $service->update(['featured' => !$service->featured]);

            // Registrar no audit log
            AuditLog::record('service_featured_toggled', $service, $oldData, $service->fresh()->toArray());

            Log::info('Destaque do serviço alterado', [
                'service_id' => $service->id,
                'featured' => $service->featured,
                'user_id' => Auth::id()
            ]);

            $message = $service->featured ? 'Serviço destacado com sucesso!' : 'Destaque removido com sucesso!';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => new ServiceResource($service)
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao alterar destaque do serviço', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao alterar destaque do serviço.');
        }
    }
}