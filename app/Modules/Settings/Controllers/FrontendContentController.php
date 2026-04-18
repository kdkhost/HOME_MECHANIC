<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendContentController extends Controller
{
    public function edit()
    {
        $settings = Setting::group('frontend');
        return view('modules.settings.frontend', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        Setting::setMany('frontend', $data);
        Cache::forget('settings_frontend');

        // Se a requisição for AJAX (para compatibilidade com sua estrutura)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Conteúdo do site atualizado com sucesso!'
            ]);
        }

        return redirect()->back()->with('success', 'Conteúdo atualizado com sucesso!');
    }
}
