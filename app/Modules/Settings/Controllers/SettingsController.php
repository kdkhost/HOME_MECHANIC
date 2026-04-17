<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    // ── Defaults ───────────────────────────────────────────

    private array $generalDefaults = [
        'site_name'        => 'HomeMechanic',
        'site_description' => 'Oficina mecânica especializada em carros de luxo e tuning',
        'contact_email'    => 'contato@homemechanic.com.br',
        'contact_phone'    => '(11) 99999-9999',
        'whatsapp'         => '',
        // Redes sociais
        'social_instagram' => '',
        'social_facebook'  => '',
        'social_youtube'   => '',
        'social_twitter'   => '',
        'social_tiktok'    => '',
        'social_linkedin'  => '',
        // Endereço separado
        'address_cep'      => '',
        'address_street'   => '',
        'address_number'   => '',
        'address_complement'=> '',
        'address_district' => '',
        'address_city'     => '',
        'address_state'    => '',
        // Campo legado (mantido para compatibilidade)
        'address'          => '',
        'maintenance_mode' => '0',
        'analytics_enabled'=> '1',
        'timezone'         => 'America/Sao_Paulo',
        'language'         => 'pt_BR',
    ];

    private array $emailDefaults = [
        'mail_driver'        => 'smtp',
        'mail_host'          => 'smtp.gmail.com',
        'mail_port'          => '587',
        'mail_username'      => '',
        'mail_password'      => '',
        'mail_encryption'    => 'tls',
        'mail_from_address'  => 'noreply@homemechanic.com.br',
        'mail_from_name'     => 'HomeMechanic',
    ];

    private array $seoDefaults = [
        'meta_title'         => 'HomeMechanic — Tuning & Performance de Luxo',
        'meta_description'   => 'Especialistas em tuning, performance e manutenção de carros de luxo.',
        'meta_keywords'      => 'tuning, carros de luxo, oficina, performance, lamborghini, ferrari',
        'google_analytics'   => '',
        'google_tag_manager' => '',
        'facebook_pixel'     => '',
    ];

    // ── Helpers ────────────────────────────────────────────

    /**
     * Ler configurações do banco, preenchendo com defaults onde não existir
     */
    private function readSettings(array $defaults, string $group): array
    {
        try {
            $saved = Setting::group($group);
            return array_merge($defaults, array_intersect_key($saved, $defaults));
        } catch (\Exception $e) {
            return $defaults;
        }
    }

    // ── Pages ──────────────────────────────────────────────

    public function index()
    {
        $settings = $this->readSettings($this->generalDefaults, 'general');
        return view('modules.settings.index', compact('settings'));
    }

    public function general()
    {
        $settings = $this->readSettings($this->generalDefaults, 'general');
        return view('modules.settings.index', compact('settings'));
    }

    public function seo()
    {
        $settings = $this->readSettings($this->seoDefaults, 'seo');
        return view('modules.settings.seo', compact('settings'));
    }

    public function email()
    {
        $settings = $this->readSettings($this->emailDefaults, 'email');
        // Nunca exibir a senha real — apenas indicar se está preenchida
        if (!empty($settings['mail_password'])) {
            $settings['mail_password_set'] = true;
            $settings['mail_password']     = '';
        }
        return view('modules.settings.email', compact('settings'));
    }

    public function backup()
    {
        return view('modules.settings.backup');
    }

    // ── Update ─────────────────────────────────────────────

    public function update(Request $request)
    {
        $section = $request->input('section', 'general');

        try {
            switch ($section) {
                case 'general':
                    $this->updateGeneral($request);
                    break;
                case 'email':
                    $this->updateEmail($request);
                    break;
                case 'seo':
                    $this->updateSeo($request);
                    break;
                case 'maintenance':
                    $this->updateMaintenance($request);
                    break;
                default:
                    return back()->with('error', 'Seção inválida.');
            }

            return back()->with('success', 'Configurações salvas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações', [
                'section' => $section,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Erro ao salvar: ' . $e->getMessage())->withInput();
        }
    }

    private function updateGeneral(Request $request): void
    {
        $request->validate([
            'site_name'     => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'address_cep'   => 'nullable|string|max:10',
        ]);

        // Montar endereço completo para campo legado
        $parts = array_filter([
            $request->input('address_street'),
            $request->input('address_number') ? ', ' . $request->input('address_number') : '',
            $request->input('address_complement') ? ' - ' . $request->input('address_complement') : '',
            $request->input('address_district') ? ', ' . $request->input('address_district') : '',
            $request->input('address_city') ? ' — ' . $request->input('address_city') : '',
            $request->input('address_state') ? '/' . $request->input('address_state') : '',
        ]);

        Setting::setMany([
            'site_name'          => $request->input('site_name'),
            'site_description'   => $request->input('site_description'),
            'contact_email'      => $request->input('contact_email'),
            'contact_phone'      => $request->input('contact_phone'),
            'whatsapp'           => $request->input('whatsapp'),
            // Redes sociais
            'social_instagram'   => $request->input('social_instagram', ''),
            'social_facebook'    => $request->input('social_facebook', ''),
            'social_youtube'     => $request->input('social_youtube', ''),
            'social_twitter'     => $request->input('social_twitter', ''),
            'social_tiktok'      => $request->input('social_tiktok', ''),
            'social_linkedin'    => $request->input('social_linkedin', ''),
            'address_cep'        => preg_replace('/\D/', '', $request->input('address_cep', '')),
            'address_street'     => $request->input('address_street'),
            'address_number'     => $request->input('address_number'),
            'address_complement' => $request->input('address_complement'),
            'address_district'   => $request->input('address_district'),
            'address_city'       => $request->input('address_city'),
            'address_state'      => $request->input('address_state'),
            'address'            => implode('', $parts),
            'maintenance_mode'   => $request->boolean('maintenance_mode') ? '1' : '0',
            'analytics_enabled'  => $request->boolean('analytics_enabled') ? '1' : '0',
            'timezone'           => $request->input('timezone', 'America/Sao_Paulo'),
            'language'           => $request->input('language', 'pt_BR'),
        ], 'general');
    }

    private function updateEmail(Request $request): void
    {
        $request->validate([
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_from_address' => 'nullable|email|max:255',
        ]);

        $data = [
            'mail_driver'       => $request->input('mail_driver', 'smtp'),
            'mail_host'         => $request->input('mail_host'),
            'mail_port'         => $request->input('mail_port'),
            'mail_username'     => $request->input('mail_username'),
            'mail_encryption'   => $request->input('mail_encryption'),
            'mail_from_address' => $request->input('mail_from_address'),
            'mail_from_name'    => $request->input('mail_from_name'),
        ];

        // Só atualiza a senha se foi preenchida
        if ($request->filled('mail_password')) {
            $data['mail_password'] = $request->input('mail_password');
        }

        Setting::setMany($data, 'email');
    }

    private function updateSeo(Request $request): void
    {
        Setting::setMany([
            'meta_title'         => $request->input('meta_title'),
            'meta_description'   => $request->input('meta_description'),
            'meta_keywords'      => $request->input('meta_keywords'),
            'google_analytics'   => $request->input('google_analytics'),
            'google_tag_manager' => $request->input('google_tag_manager'),
            'facebook_pixel'     => $request->input('facebook_pixel'),
        ], 'seo');
    }

    private function updateMaintenance(Request $request): void
    {
        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'general');
    }

    // ── Test SMTP ──────────────────────────────────────────

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            $host       = $request->input('mail_host',         Setting::get('mail_host',         'smtp.gmail.com'));
            $port       = $request->input('mail_port',         Setting::get('mail_port',         '587'));
            $username   = $request->input('mail_username',     Setting::get('mail_username',     ''));
            $password   = $request->input('mail_password')     ?: Setting::get('mail_password',  '');
            $encryption = $request->input('mail_encryption',   Setting::get('mail_encryption',   'tls'));
            $fromAddr   = $request->input('mail_from_address', Setting::get('mail_from_address', 'noreply@homemechanic.com.br'));
            $fromName   = $request->input('mail_from_name',    Setting::get('mail_from_name',    'HomeMechanic'));

            // Suporte a subject/body customizados (enviado da página de templates)
            $customSubject = $request->input('mail_subject');
            $customBody    = $request->input('mail_body');

            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $host,
                'mail.mailers.smtp.port'       => (int) $port,
                'mail.mailers.smtp.username'   => $username,
                'mail.mailers.smtp.password'   => $password,
                'mail.mailers.smtp.encryption' => $encryption ?: null,
                'mail.from.address'            => $fromAddr,
                'mail.from.name'               => $fromName,
            ]);

            $subject = $customSubject ?: '✅ Teste SMTP — HomeMechanic';
            $isHtml  = $customBody && strip_tags($customBody) !== $customBody;

            if ($isHtml && $customBody) {
                \Illuminate\Support\Facades\Mail::html(
                    $customBody,
                    fn($m) => $m->to($request->input('test_email'))
                                ->from($fromAddr, $fromName)
                                ->subject($subject)
                );
            } else {
                $text = $customBody ?: (
                    "✅ Teste de configuração SMTP — HomeMechanic\n\n" .
                    "Se você recebeu este e-mail, as configurações SMTP estão corretas!\n\n" .
                    "Servidor: {$host}:{$port}\n" .
                    "Criptografia: " . ($encryption ?: 'Nenhuma') . "\n" .
                    "Remetente: {$fromName} <{$fromAddr}>\n\n" .
                    "Enviado em: " . now()->format('d/m/Y H:i:s')
                );
                \Illuminate\Support\Facades\Mail::raw(
                    $text,
                    fn($m) => $m->to($request->input('test_email'))
                                ->from($fromAddr, $fromName)
                                ->subject($subject)
                );
            }

            return response()->json([
                'success' => true,
                'message' => "E-mail enviado para {$request->input('test_email')} com sucesso!",
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no teste SMTP', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Falha: ' . $e->getMessage(),
            ], 422);
        }
    }
}
