<?php

namespace App\Modules\Gallery\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Gallery\Models\GalleryCategory;
use App\Modules\Gallery\Models\GalleryPhoto;
use App\Modules\Gallery\Requests\GalleryCategoryRequest;
use App\Modules\Gallery\Requests\GalleryPhotoRequest;
use App\Modules\Gallery\Resources\GalleryCategoryResource;
use App\Modules\Gallery\Resources\GalleryPhotoResource;
use App\Modules\Gallery\Services\ImageService;
use App\Modules\Upload\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    private ImageService $imageService;
    private UploadService $uploadService;

    public function __construct(ImageService $imageService, UploadService $uploadService)
    {
        $this->imageService = $imageService;
        $this->uploadService = $uploadService;
    }

    /**
     * Listar categorias da galeria
     */
    public function index(Request $request)
    {
        try {
            $query = GalleryCategory::with(['activePhotos' => function ($q) {
                $q->ordered()->limit(4);
            }]);

            // Filtros
            if ($request->has('search') && $request->filled('search')) {
                $query->search($request->input('search'));
            }

            // Ordenação
            $query->ordered();

            // Se for requisição AJAX, retornar JSON
            if ($request->wantsJson() || $request->ajax()) {
                $perPage = min($request->input('per_page', 15), 100);
                $categories = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => GalleryCategoryResource::collection($categories->items()),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                        'from' => $categories->firstItem(),
                        'to' => $categories->lastItem()
                    ]
                ]);
            }

            // Para acesso direto, retornar view
            $categories = $query->paginate(15);
            return view('modules.gallery.index', compact('categories'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar categorias da galeria', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar galeria.');
        }
    }

    /**
     * Criar nova categoria
     */
    public function storeCategory(GalleryCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $category = GalleryCategory::create($data);

            // Registrar no audit log
            AuditLog::record('gallery_category_created', $category, [], $category->toArray());

            DB::commit();

            Log::info('Categoria da galeria criada com sucesso', [
                'category_id' => $category->id,
                'name' => $category->name,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria criada com sucesso!',
                    'data' => new GalleryCategoryResource($category)
                ], 201);
            }

            return redirect()->route('admin.gallery.index')
                           ->with('success', 'Categoria criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar categoria da galeria', [
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

            return back()->withInput()->with('error', 'Erro ao criar categoria.');
        }
    }

    /**
     * Atualizar categoria
     */
    public function updateCategory(GalleryCategoryRequest $request, GalleryCategory $category)
    {
        try {
            DB::beginTransaction();

            $oldData = $category->toArray();
            $data = $request->validated();
            
            $category->update($data);

            // Registrar no audit log
            AuditLog::record('gallery_category_updated', $category, $oldData, $category->fresh()->toArray());

            DB::commit();

            Log::info('Categoria da galeria atualizada com sucesso', [
                'category_id' => $category->id,
                'name' => $category->name,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria atualizada com sucesso!',
                    'data' => new GalleryCategoryResource($category->fresh())
                ]);
            }

            return redirect()->route('admin.gallery.index')
                           ->with('success', 'Categoria atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar categoria da galeria', [
                'error' => $e->getMessage(),
                'category_id' => $category->id,
                'data' => $request->validated(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao atualizar categoria.');
        }
    }

    /**
     * Excluir categoria
     */
    public function destroyCategory(GalleryCategory $category, Request $request)
    {
        try {
            // Verificar se categoria tem fotos
            if ($category->photos()->count() > 0) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir categoria que possui fotos.'
                    ], 422);
                }

                return back()->with('error', 'Não é possível excluir categoria que possui fotos.');
            }

            DB::beginTransaction();

            $oldData = $category->toArray();
            $category->delete();

            // Registrar no audit log
            AuditLog::record('gallery_category_deleted', $category, $oldData, []);

            DB::commit();

            Log::info('Categoria da galeria excluída com sucesso', [
                'category_id' => $category->id,
                'name' => $category->name,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria excluída com sucesso!'
                ]);
            }

            return redirect()->route('admin.gallery.index')
                           ->with('success', 'Categoria excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao excluir categoria da galeria', [
                'error' => $e->getMessage(),
                'category_id' => $category->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao excluir categoria.');
        }
    }

    /**
     * Listar fotos de uma categoria
     */
    public function photos(Request $request, $id = null)
    {
        try {
            $query = GalleryPhoto::with(['category']);
            $category = null;

            // Resolver categoria - Priorizar category_id da request, depois $id da rota
            $categoryId = $request->input('category_id') ?: $id;
            
            if ($categoryId) {
                // Tentar encontrar a categoria primeiro
                $category = GalleryCategory::find($categoryId);
                if ($category) {
                    $query->where('category_id', $category->id);
                } else if ($id && !$request->has('category_id')) {
                    // Se não é categoria e veio apenas via rota, tentar como photo_id
                    $query->where('id', $id);
                }
            }

            // Filtros Adicionais
            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }

            if ($request->has('active') && $request->input('active') !== '') {
                $query->where('active', $request->boolean('active'));
            }

            // Filtro por ID da foto específico (usado pelo editPhoto JS)
            if ($request->filled('photo_id')) {
                $query->where('id', $request->input('photo_id'));
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
                $perPage = min($request->input('per_page', 20), 100);
                $photos = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => GalleryPhotoResource::collection($photos->items()),
                    'pagination' => [
                        'current_page' => $photos->currentPage(),
                        'last_page' => $photos->lastPage(),
                        'per_page' => $photos->perPage(),
                        'total' => $photos->total(),
                        'from' => $photos->firstItem(),
                        'to' => $photos->lastItem()
                    ]
                ]);
            }

            // Para acesso direto, retornar view
            $photos = $query->paginate(20);
            $categories = GalleryCategory::ordered()->get();
            
            return view('modules.gallery.photos', compact('photos', 'categories', 'category'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar fotos da galeria', [
                'error' => $e->getMessage(),
                'category_id' => $category?->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar fotos.');
        }
    }

    /**
     * Criar nova foto
     */
    public function storePhoto(GalleryPhotoRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $photo = GalleryPhoto::create($data);

            // Associar uploads
            if (!empty($data['filename'])) {
                $upload = $this->uploadService->getByUuid($data['filename']);
                if ($upload) {
                    $this->uploadService->attachToModel($upload, GalleryPhoto::class, $photo->id);
                    $photo->update(['filename' => $upload->path]);
                }
            }

            if (!empty($data['thumbnail']) && $data['thumbnail'] !== $data['filename']) {
                $thumbnailUpload = $this->uploadService->getByUuid($data['thumbnail']);
                if ($thumbnailUpload) {
                    $this->uploadService->attachToModel($thumbnailUpload, GalleryPhoto::class, $photo->id);
                    $photo->update(['thumbnail' => $thumbnailUpload->path]);
                }
            }

            // Registrar no audit log
            AuditLog::record('gallery_photo_created', $photo, [], $photo->toArray());

            DB::commit();

            Log::info('Foto da galeria criada com sucesso', [
                'photo_id' => $photo->id,
                'title' => $photo->title,
                'category_id' => $photo->category_id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto adicionada com sucesso!',
                    'data' => new GalleryPhotoResource($photo)
                ], 201);
            }

            return redirect()->route('admin.gallery.photos')
                           ->with('success', 'Foto adicionada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar foto da galeria', [
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

            return back()->withInput()->with('error', 'Erro ao adicionar foto.');
        }
    }

    /**
     * Upload em massa de fotos
     */
    public function massStore(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:gallery_categories,id',
                'mass_photos' => 'required|array',
                'mass_photos.*' => 'required|string|max:36' // UUIDs
            ]);

            DB::beginTransaction();
            $categoryId = $request->input('category_id');
            $uuids      = $request->input('mass_photos');
            $count      = 0;

            foreach ($uuids as $uuid) {
                $upload = $this->uploadService->getByUuid($uuid);
                if ($upload) {
                    $photo = GalleryPhoto::create([
                        'category_id' => $categoryId,
                        'title'       => $upload->original_name ?: 'Foto ' . (now()->timestamp + $count),
                        'filename'    => $upload->path,
                        'active'      => true,
                        'sort_order'  => GalleryPhoto::where('category_id', $categoryId)->max('sort_order') + 1
                    ]);

                    $this->uploadService->attachToModel($upload, GalleryPhoto::class, $photo->id);
                    $count++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} fotos importadas com sucesso!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no upload em massa', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Atualizar foto
     */
    public function updatePhoto(GalleryPhotoRequest $request, GalleryPhoto $photo)
    {
        try {
            DB::beginTransaction();

            $oldData = $photo->toArray();
            $data = $request->validated();
            
            $photo->update($data);

            // Gerenciar uploads
            if (array_key_exists('filename', $data)) {
                // Remover associação anterior se mudou
                if ($photo->filename && $photo->filename !== $data['filename']) {
                    $oldUpload = $this->uploadService->getByUuid($photo->filename);
                    if ($oldUpload) {
                        $this->uploadService->attachToModel($oldUpload, null, null);
                    }
                }

                // Associar novo upload
                if (!empty($data['filename'])) {
                    $upload = $this->uploadService->getByUuid($data['filename']);
                    if ($upload) {
                        $this->uploadService->attachToModel($upload, GalleryPhoto::class, $photo->id);
                        $photo->update(['filename' => $upload->path]);
                    }
                }

                if (!empty($data['thumbnail']) && $data['thumbnail'] !== $data['filename']) {
                    $thumbnailUpload = $this->uploadService->getByUuid($data['thumbnail']);
                    if ($thumbnailUpload) {
                        $this->uploadService->attachToModel($thumbnailUpload, GalleryPhoto::class, $photo->id);
                        $photo->update(['thumbnail' => $thumbnailUpload->path]);
                    }
                }
            }

            // Registrar no audit log
            AuditLog::record('gallery_photo_updated', $photo, $oldData, $photo->fresh()->toArray());

            DB::commit();

            Log::info('Foto da galeria atualizada com sucesso', [
                'photo_id' => $photo->id,
                'title' => $photo->title,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto atualizada com sucesso!',
                    'data' => new GalleryPhotoResource($photo->fresh())
                ]);
            }

            return redirect()->route('admin.gallery.photos')
                           ->with('success', 'Foto atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar foto da galeria', [
                'error' => $e->getMessage(),
                'photo_id' => $photo->id,
                'data' => $request->validated(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao atualizar foto.');
        }
    }

    /**
     * Excluir foto
     */
    public function destroyPhoto(GalleryPhoto $photo, Request $request)
    {
        try {
            DB::beginTransaction();

            $oldData = $photo->toArray();

            // Remover associações de uploads
            $uploads = $this->uploadService->getByModel(GalleryPhoto::class, $photo->id);
            foreach ($uploads as $upload) {
                $this->uploadService->attachToModel($upload, null, null);
            }

            $photo->delete();

            // Registrar no audit log
            AuditLog::record('gallery_photo_deleted', $photo, $oldData, []);

            DB::commit();

            Log::info('Foto da galeria excluída com sucesso', [
                'photo_id' => $photo->id,
                'title' => $photo->title,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto excluída com sucesso!'
                ]);
            }

            return redirect()->route('admin.gallery.photos')
                           ->with('success', 'Foto excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao excluir foto da galeria', [
                'error' => $e->getMessage(),
                'photo_id' => $photo->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao excluir foto.');
        }
    }

    /**
     * Reordenar categorias
     */
    public function reorderCategories(Request $request)
    {
        try {
            $request->validate([
                'categories' => 'required|array',
                'categories.*.id' => 'required|integer|exists:gallery_categories,id',
                'categories.*.sort_order' => 'required|integer|min:0'
            ]);

            DB::beginTransaction();

            foreach ($request->input('categories') as $categoryData) {
                GalleryCategory::where('id', $categoryData['id'])
                              ->update(['sort_order' => $categoryData['sort_order']]);
            }

            DB::commit();

            Log::info('Categorias da galeria reordenadas com sucesso', [
                'categories_count' => count($request->input('categories')),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ordem das categorias atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao reordenar categorias da galeria', [
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
     * Reordenar fotos
     */
    public function reorderPhotos(Request $request)
    {
        try {
            $request->validate([
                'photos' => 'required|array',
                'photos.*.id' => 'required|integer|exists:gallery_photos,id',
                'photos.*.sort_order' => 'required|integer|min:0'
            ]);

            DB::beginTransaction();

            foreach ($request->input('photos') as $photoData) {
                GalleryPhoto::where('id', $photoData['id'])
                           ->update(['sort_order' => $photoData['sort_order']]);
            }

            DB::commit();

            Log::info('Fotos da galeria reordenadas com sucesso', [
                'photos_count' => count($request->input('photos')),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ordem das fotos atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao reordenar fotos da galeria', [
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
     * Alternar status ativo/inativo da foto
     */
    public function togglePhotoActive(GalleryPhoto $photo, Request $request)
    {
        try {
            $oldData = $photo->toArray();
            $photo->update(['active' => !$photo->active]);

            // Registrar no audit log
            AuditLog::record('gallery_photo_toggled', $photo, $oldData, $photo->fresh()->toArray());

            Log::info('Status da foto alterado', [
                'photo_id' => $photo->id,
                'active' => $photo->active,
                'user_id' => Auth::id()
            ]);

            $message = $photo->active ? 'Foto ativada com sucesso!' : 'Foto desativada com sucesso!';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => new GalleryPhotoResource($photo)
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erro ao alterar status da foto', [
                'error' => $e->getMessage(),
                'photo_id' => $photo->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao alterar status da foto.');
        }
    }

    /**
     * Renomear fisicamente o arquivo da imagem
     */
    public function renameFile(GalleryPhoto $photo, Request $request)
    {
        try {
            $request->validate([
                'new_name' => 'required|string|max:255'
            ]);

            $newName = \Illuminate\Support\Str::slug($request->input('new_name'));
            if (empty($newName)) {
                return response()->json(['success' => false, 'message' => 'Nome inválido.'], 400);
            }

            // Achar o Upload associado
            $upload = $photo->getMainUpload();
            if (!$upload) {
                return response()->json(['success' => false, 'message' => 'Upload não encontrado.'], 404);
            }

            $oldPath = public_path($upload->path);
            if (!file_exists($oldPath)) {
                return response()->json(['success' => false, 'message' => 'Arquivo físico não encontrado.'], 404);
            }

            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            // Substituir a barra invertida (do dirname no Windows) por barra normal
            $directory = str_replace('\\', '/', dirname($upload->path));
            
            // Garantir nome único
            $finalName = $newName . '.' . $extension;
            $newPath = $directory . '/' . $finalName;
            
            $counter = 1;
            while (file_exists(public_path($newPath)) && $newPath !== $upload->path) {
                $finalName = $newName . '-' . $counter . '.' . $extension;
                $newPath = $directory . '/' . $finalName;
                $counter++;
            }

            if ($newPath === $upload->path) {
                return response()->json([
                    'success' => true, 
                    'message' => 'O arquivo já possui este nome.',
                    'new_url' => asset($newPath)
                ]);
            }

            // Renomear fisicamente
            rename($oldPath, public_path($newPath));

            // Atualizar banco
            $upload->update([
                'path' => $newPath,
                'filename' => $finalName,
                'url' => url($newPath),
                'original_name' => $finalName
            ]);

            $photo->update([
                'filename' => $newPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arquivo renomeado com sucesso!',
                'new_url' => asset($newPath)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao renomear arquivo', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro interno ao renomear o arquivo.'], 500);
        }
    }
}