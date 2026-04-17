<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Página principal de configurações
     */
    public function index()
    {
        $settings = [
            'site_name' => 'HomeMechanic',
            'site_description' => 'Oficina mecânica especializada',
            'contact_email' => 'contato@homemechanic.com.br',
            'contact_phone' => '(11) 99999-9999',
            'address' => 'Rua das Oficinas, 123 - São Paulo/SP',
            'maintenance_mode' => false,
            'analytics_enabled' => true
        ];

        return view('modules.settings.index', compact('settings'));
    }

    /**
     * Configurações gerais
     */
    public function general()
    {
        $settings = [
            'site_name' => 'HomeMechanic',
            'site_description' => 'Oficina mecânica especializada',
            'contact_email' => 'contato@homemechanic.com.br',
            'contact_phone' => '(11) 99999-9999',
            'address' => 'Rua das Oficinas, 123 - São Paulo/SP',
            'timezone' => 'America/Sao_Paulo',
            'language' => 'pt_BR'
        ];

        return view('modules.settings.general', compact('settings'));
    }

    /**
     * Configurações de SEO
     */
    public function seo()
    {
        $settings = [
            'meta_title' => 'HomeMechanic - Oficina Mecânica',
            'meta_description' => 'Oficina mecânica especializada em manutenção automotiva',
            'meta_keywords' => 'oficina, mecânica, carros, manutenção',
            'google_analytics' => '',
            'google_tag_manager' => '',
            'facebook_pixel' => ''
        ];

        return view('modules.settings.seo', compact('settings'));
    }

    /**
     * Configurações de email
     */
    public function email()
    {
        $settings = [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '587',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@homemechanic.com.br',
            'mail_from_name' => 'HomeMechanic'
        ];

        return view('modules.settings.email', compact('settings'));
    }

    /**
     * Backup e manutenção
     */
    public function backup()
    {
        return view('modules.settings.backup');
    }

    /**
     * Atualizar configurações
     */
    public function update(Request $request)
    {
        // TODO: Implementar salvamento das configurações
        
        return redirect()->back()
            ->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Testar configuração SMTP
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Aplicar configurações temporariamente
            $host       = $request->input('mail_host', config('mail.mailers.smtp.host'));
            $port       = $request->input('mail_port', config('mail.mailers.smtp.port'));
            $username   = $request->input('mail_username', config('mail.mailers.smtp.username'));
            $password   = $request->input('mail_password', config('mail.mailers.smtp.password'));
            $encryption = $request->input('mail_encryption', config('mail.mailers.smtp.encryption'));
            $fromAddr   = $request->input('mail_from_address', config('mail.from.address'));
            $fromName   = $request->input('mail_from_name', config('mail.from.name'));

            // Configurar mailer dinamicamente
            config([
                'mail.mailers.smtp.host'       => $host,
                'mail.mailers.smtp.port'       => $port,
                'mail.mailers.smtp.username'   => $username,
                'mail.mailers.smtp.password'   => $password,
                'mail.mailers.smtp.encryption' => $encryption ?: null,
                'mail.from.address'            => $fromAddr,
                'mail.from.name'               => $fromName,
                'mail.default'                 => 'smtp',
            ]);

            // Enviar e-mail de teste
            \Illuminate\Support\Facades\Mail::raw(
                "✅ Teste de configuração SMTP — HomeMechanic\n\n" .
                "Se você recebeu este e-mail, as configurações SMTP estão corretas!\n\n" .
                "Servidor: {$host}:{$port}\n" .
                "Criptografia: " . ($encryption ?: 'Nenhuma') . "\n" .
                "Remetente: {$fromName} <{$fromAddr}>\n\n" .
                "Enviado em: " . now()->format('d/m/Y H:i:s'),
                function ($message) use ($request, $fromAddr, $fromName) {
                    $message->to($request->input('test_email'))
                            ->from($fromAddr, $fromName)
                            ->subject('✅ Teste SMTP — HomeMechanic');
                }
            );

            return response()->json([
                'success' => true,
                'message' => "E-mail de teste enviado para {$request->input('test_email')} com sucesso!",
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro no teste SMTP', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Falha ao enviar e-mail: ' . $e->getMessage(),
            ], 422);
        }
    }
}