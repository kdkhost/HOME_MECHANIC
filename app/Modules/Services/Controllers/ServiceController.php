<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\FileUploadHelper;
use App\Modules\Services\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // Requisição AJAX → retorna JSON
        if ($request->wantsJson() || $request->ajax()) {
            try {
                $query = Service::query();

                if ($request->filled('search')) {
                    $query->search($request->search);
                }
                if ($request->filled('active') && $request->active !== '') {
                    $query->where('active', (bool) $request->active);
                }
                if ($request->filled('featured') && $request->featured !== '') {
                    $query->where('featured', (bool) $request->featured);
                }

                $sortBy = in_array($request->sort_by, ['sort_order','title','created_at','updated_at'])
                    ? $request->sort_by : 'sort_order';
                $query->orderBy($sortBy, $sortBy === 'title' ? 'asc' : 'asc');

                $perPage = min((int) ($request->per_page ?? 15), 50);
                $services = $query->paginate($perPage);

                return response()->json([
                    'success'    => true,
                    'data'       => $services->items(),
                    'pagination' => [
                        'current_page' => $services->currentPage(),
                        'last_page'    => $services->lastPage(),
                        'total'        => $services->total(),
                        'per_page'     => $services->perPage(),
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        // Requisição normal → view
        return view('modules.services.index');
    }

    public function create()
    {
        return view('modules.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content'     => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        try {
            $data = $request->only(['title','description','content','icon','sort_order']);
            $data['featured']   = $request->boolean('featured');
            $data['active']     = $request->boolean('active', true);
            $data['sort_order'] = $data['sort_order'] ?? (Service::max('sort_order') + 1);

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = FileUploadHelper::save($request->file('cover_image'), 'uploads/services');
            }

            $service = Service::create($data);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Serviço criado com sucesso!', 'data' => $service]);
            }
            return redirect()->route('admin.services.index')->with('success', 'Serviço criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar serviço', ['error' => $e->getMessage()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Erro ao criar serviço: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $service]);
            }
            return view('modules.services.show', compact('service'));
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Serviço não encontrado.'], 404);
            }
            return redirect()->route('admin.services.index')->with('error', 'Serviço não encontrado.');
        }
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('modules.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content'     => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        try {
            $service = Service::findOrFail($id);
            $data = $request->only(['title','description','content','icon','sort_order']);
            $data['featured'] = $request->boolean('featured');
            $data['active']   = $request->boolean('active', true);

            if ($request->hasFile('cover_image')) {
                // Remove imagem antiga
                if ($service->cover_image) FileUploadHelper::delete($service->cover_image);
                $data['cover_image'] = FileUploadHelper::save($request->file('cover_image'), 'uploads/services');
            }

            // Remover imagem
            if ($request->input('remove_image') === '1' && $service->cover_image) {
                FileUploadHelper::delete($service->cover_image);
                $data['cover_image'] = null;
            }

            $service->update($data);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Serviço atualizado com sucesso!', 'data' => $service->fresh()]);
            }
            return redirect()->route('admin.services.index')->with('success', 'Serviço atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar serviço', ['error' => $e->getMessage()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            if ($service->cover_image) FileUploadHelper::delete($service->cover_image);
            $service->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Serviço excluído com sucesso!']);
            }
            return redirect()->route('admin.services.index')->with('success', 'Serviço excluído!');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('admin.services.index')->with('error', 'Erro ao excluir.');
        }
    }

    public function toggleActive(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->update(['active' => !$service->active]);
            $status = $service->active ? 'ativado' : 'desativado';
            return response()->json(['success' => true, 'message' => "Serviço {$status}!", 'active' => $service->active]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleFeatured(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->update(['featured' => !$service->featured]);
            $status = $service->featured ? 'adicionado ao destaque' : 'removido do destaque';
            return response()->json(['success' => true, 'message' => "Serviço {$status}!", 'featured' => $service->featured]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reorder(Request $request)
    {
        try {
            foreach ($request->input('services', []) as $item) {
                Service::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
            return response()->json(['success' => true, 'message' => 'Ordem atualizada!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
