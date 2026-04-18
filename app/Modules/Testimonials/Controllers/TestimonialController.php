<?php

namespace App\Modules\Testimonials\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::ordered()->get();
        return view('modules.testimonials.index', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'content'   => 'required|string',
            'rating'    => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            $testimonial = Testimonial::create([
                'name'      => $request->name,
                'role'      => $request->role,
                'content'   => $request->content,
                'rating'    => $request->rating,
                'is_active' => $request->boolean('is_active', true),
                'sort_order'=> Testimonial::max('sort_order') + 1,
            ]);
            AuditLog::record('testimonial_created', $testimonial, [], $testimonial->toArray());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Depoimento adicionado com sucesso!',
                'reload'  => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar depoimento', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao salvar.'], 500);
        }
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'content'   => 'required|string',
            'rating'    => 'required|integer|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();
            $oldData = $testimonial->toArray();
            $testimonial->update([
                'name'    => $request->name,
                'role'    => $request->role,
                'content' => $request->content,
                'rating'  => $request->rating,
            ]);
            AuditLog::record('testimonial_updated', $testimonial, $oldData, $testimonial->toArray());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Depoimento atualizado com sucesso!',
                'reload'  => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar depoimento', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao salvar.'], 500);
        }
    }

    public function destroy(Testimonial $testimonial)
    {
        try {
            $oldData = $testimonial->toArray();
            $testimonial->delete();
            AuditLog::record('testimonial_deleted', $testimonial, $oldData, []);
            return redirect()->route('admin.testimonials.index')->with('success', 'Depoimento excluído!');
        } catch (\Exception $e) {
            return redirect()->route('admin.testimonials.index')->with('error', 'Erro ao excluir.');
        }
    }

    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return response()->json(['success' => true, 'message' => 'Status alterado.']);
    }

    public function reorder(Request $request)
    {
        $orderedIds = $request->input('order', []);
        foreach ($orderedIds as $index => $id) {
            Testimonial::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true, 'message' => 'Ordem atualizada']);
    }
}
