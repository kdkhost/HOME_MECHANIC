<?php

namespace App\Modules\Frontend\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('modules.frontend.home');
    }

    public function services()
    {
        return view('modules.frontend.services');
    }

    public function gallery()
    {
        return view('modules.frontend.gallery');
    }

    public function blog()
    {
        return view('modules.frontend.blog');
    }

    public function contact()
    {
        return view('modules.frontend.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        // TODO: salvar no banco / enviar email
        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
