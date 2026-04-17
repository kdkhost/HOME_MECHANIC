<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class RecaptchaController extends Controller
{
    public function index()
    {
        $settings = [
            'recaptcha_enabled'   => Setting::get('recaptcha_enabled',   '0'),
            'recaptcha_site_key'  => Setting::get('recaptcha_site_key',  ''),
            'recaptcha_secret'    => Setting::get('recaptcha_secret',    ''),
            'recaptcha_threshold' => Setting::get('recaptcha_threshold', '0.5'),
        ];
        return view('modules.settings.recaptcha', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'recaptcha_site_key'  => 'nullable|string|max:255',
            'recaptcha_secret'    => 'nullable|string|max:255',
            'recaptcha_threshold' => 'nullable|numeric|min:0|max:1',
        ]);

        Setting::setMany([
            'recaptcha_enabled'   => $request->boolean('recaptcha_enabled') ? '1' : '0',
            'recaptcha_site_key'  => $request->input('recaptcha_site_key', ''),
            'recaptcha_secret'    => $request->input('recaptcha_secret', ''),
            'recaptcha_threshold' => $request->input('recaptcha_threshold', '0.5'),
        ], 'security');

        return back()->with('success', 'Configurações do reCAPTCHA salvas com sucesso!');
    }

    /**
     * Verifica token reCAPTCHA v3 — usado pelo ContactController
     */
    public static function verify(string $token, string $action = 'contact'): array
    {
        $secret    = Setting::get('recaptcha_secret', '');
        $threshold = (float) Setting::get('recaptcha_threshold', '0.5');

        if (empty($secret)) {
            return ['success' => true, 'score' => 1.0, 'skipped' => true];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                ['secret' => $secret, 'response' => $token]
            );

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                return ['success' => false, 'score' => 0, 'error' => 'Token inválido.'];
            }

            if (($data['action'] ?? '') !== $action) {
                return ['success' => false, 'score' => 0, 'error' => 'Ação inválida.'];
            }

            $score = (float) ($data['score'] ?? 0);

            if ($score < $threshold) {
                return ['success' => false, 'score' => $score, 'error' => "Score muito baixo ({$score}). Possível bot."];
            }

            return ['success' => true, 'score' => $score];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('reCAPTCHA verify error', ['error' => $e->getMessage()]);
            // Em caso de erro na API, deixa passar para não bloquear usuários reais
            return ['success' => true, 'score' => 1.0, 'skipped' => true];
        }
    }
}
