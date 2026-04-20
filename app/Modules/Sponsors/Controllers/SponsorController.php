<?php

namespace App\Modules\Sponsors\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\FileUploadHelper;
use App\Modules\Sponsors\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SponsorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Sponsor::ordered();

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('active') && $request->active !== '') {
                $query->where('is_active', (bool) $request->active);
            }

            $perPage = min((int) ($request->per_page ?? 10), 50);
            $sponsors = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $sponsors->items(),
                'pagination' => [
                    'current_page' => $sponsors->currentPage(),
                    'last_page' => $sponsors->lastPage(),
                    'total' => $sponsors->total(),
                    'per_page' => $sponsors->perPage(),
                ],
            ]);
        }

        return view('modules.sponsors.index');
    }

    public function show(Sponsor $sponsor)
    {
        return response()->json(['success' => true, 'data' => $sponsor]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'animation' => 'required|in:fade,slide,zoom,flip,bounce',
            'speed' => 'required|in:slow,normal,fast',
            'logo' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'website', 'description', 'animation', 'speed']);
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = Sponsor::max('sort_order') + 1;

            $logoResolved = FileUploadHelper::resolveFromRequest($request, 'logo', 'uploads/sponsors');
            if ($logoResolved !== null) {
                $data['logo'] = $logoResolved;
            }

            $sponsor = Sponsor::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Patrocinador criado!', 'data' => $sponsor]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar patrocinador', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao criar.'], 500);
        }
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'animation' => 'required|in:fade,slide,zoom,flip,bounce',
            'speed' => 'required|in:slow,normal,fast',
            'logo' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'website', 'description', 'animation', 'speed']);
            $data['is_active'] = $request->boolean('is_active');

            $logoResolved = FileUploadHelper::resolveFromRequest($request, 'logo', 'uploads/sponsors');
            if ($logoResolved !== null) {
                $data['logo'] = $logoResolved;
            }

            $sponsor->update($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Patrocinador atualizado!', 'data' => $sponsor]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar patrocinador', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar.'], 500);
        }
    }

    public function destroy(Sponsor $sponsor)
    {
        try {
            $sponsor->delete();
            return response()->json(['success' => true, 'message' => 'Patrocinador excluido!']);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir patrocinador', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao excluir.'], 500);
        }
    }

    public function toggleActive(Sponsor $sponsor)
    {
        try {
            $sponsor->update(['is_active' => !$sponsor->is_active]);
            return response()->json(['success' => true, 'message' => 'Status alterado!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro.'], 500);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $order = $request->input('sponsors', []);
            foreach ($order as $item) {
                Sponsor::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
            return response()->json(['success' => true, 'message' => 'Ordem atualizada!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro.'], 500);
        }
    }
}
