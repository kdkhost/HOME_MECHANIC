<?php

namespace App\Modules\Frontend\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Contact\Models\ContactMessage;
use App\Modules\Settings\Controllers\RecaptchaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function home()
    {
        try {
            $services = \App\Modules\Services\Models\Service::active()->featured()->ordered()->take(4)->get();
            $testimonials = \App\Modules\Testimonials\Models\Testimonial::active()->ordered()->take(3)->get();
            $galleryPhotos = \App\Modules\Gallery\Models\GalleryPhoto::active()->latest()->take(6)->get();
            $sponsors = \App\Modules\Sponsors\Models\Sponsor::active()->ordered()->get();
        } catch (\Exception $e) {
            $services = collect();
            $testimonials = collect();
            $galleryPhotos = collect();
            $sponsors = collect();
        }
        return view('modules.frontend.home', compact('services', 'testimonials', 'galleryPhotos', 'sponsors'));
    }

    public function services()
    {
        try {
            $services = \App\Modules\Services\Models\Service::active()->ordered()->get();
        } catch (\Exception $e) {
            $services = collect();
        }
        return view('modules.frontend.services', compact('services'));
    }

    public function gallery()
    {
        try {
            $categories = \App\Modules\Gallery\Models\GalleryCategory::ordered()->get();
            $photos = \App\Modules\Gallery\Models\GalleryPhoto::with('category')->active()->latest()->get();
        } catch (\Exception $e) {
            $categories = collect();
            $photos = collect();
        }
        return view('modules.frontend.gallery', compact('categories', 'photos'));
    }

    public function blog()
    {
        try {
            $featured = \App\Modules\Blog\Models\Post::published()->featured()->latest('published_at')->first();
            $posts    = \App\Modules\Blog\Models\Post::published()
                ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
                ->latest('published_at')->get();
        } catch (\Exception $e) {
            $featured = null;
            $posts    = collect();
        }
        return view('modules.frontend.blog', compact('featured', 'posts'));
    }

    public function blogPost(string $slug)
    {
        try {
            $post = \App\Modules\Blog\Models\Post::published()->where('slug', $slug)->firstOrFail();
            $related = \App\Modules\Blog\Models\Post::published()
                ->where('id', '!=', $post->id)
                ->latest('published_at')->limit(3)->get();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            abort(404);
        }
        return view('modules.frontend.blog-post', compact('post', 'related'));
    }

    public function contact()  { return view('modules.frontend.contact'); }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        // ── reCAPTCHA v3 ──────────────────────────────────────
        $recaptchaEnabled = Setting::get('recaptcha_enabled', '0') === '1';

        if ($recaptchaEnabled) {
            $token  = $request->input('recaptcha_token', '');
            $result = RecaptchaController::verify($token, 'contact');

            if (!$result['success'] && !($result['skipped'] ?? false)) {
                Log::warning('reCAPTCHA bloqueou envio de contato', [
                    'ip'    => $request->ip(),
                    'email' => $request->input('email'),
                    'score' => $result['score'] ?? 0,
                    'error' => $result['error'] ?? '',
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['recaptcha' => 'Verificação de segurança falhou. Tente novamente.']);
            }
        }

        // ── Salvar no banco ───────────────────────────────────
        try {
            ContactMessage::create([
                'name'    => $request->input('name'),
                'email'   => $request->input('email'),
                'phone'   => $request->input('phone'),
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
                'ip'      => $request->ip(),
                'read'    => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar mensagem de contato', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Erro ao enviar mensagem. Tente novamente.');
        }

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
