<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /** Templates disponíveis */
    private array $templates = [
        'contact_reply' => [
            'name'    => 'Resposta de Contato',
            'desc'    => 'Enviado ao cliente quando você responde uma mensagem de contato.',
            'vars'    => ['{{nome}}', '{{mensagem_original}}', '{{resposta}}', '{{site_name}}'],
            'subject' => 'Re: {{assunto}} — {{site_name}}',
            'body'    => "Olá, {{nome}}!\n\nObrigado por entrar em contato conosco.\n\n{{resposta}}\n\nAtenciosamente,\nEquipe {{site_name}}",
        ],
        'welcome' => [
            'name'    => 'Boas-vindas',
            'desc'    => 'Enviado ao novo usuário após o cadastro.',
            'vars'    => ['{{nome}}', '{{email}}', '{{site_name}}', '{{login_url}}'],
            'subject' => 'Bem-vindo à {{site_name}}!',
            'body'    => "Olá, {{nome}}!\n\nSua conta foi criada com sucesso.\n\nAcesse o painel em: {{login_url}}\n\nAtenciosamente,\nEquipe {{site_name}}",
        ],
        'password_reset' => [
            'name'    => 'Redefinição de Senha',
            'desc'    => 'Enviado quando o usuário solicita redefinição de senha.',
            'vars'    => ['{{nome}}', '{{reset_url}}', '{{expiry}}', '{{site_name}}'],
            'subject' => 'Redefinição de senha — {{site_name}}',
            'body'    => "Olá, {{nome}}!\n\nRecebemos uma solicitação para redefinir sua senha.\n\nClique no link abaixo (válido por {{expiry}}):\n{{reset_url}}\n\nSe não foi você, ignore este e-mail.\n\nAtenciosamente,\nEquipe {{site_name}}",
        ],
        'notification' => [
            'name'    => 'Notificação Geral',
            'desc'    => 'Template genérico para notificações do sistema.',
            'vars'    => ['{{titulo}}', '{{mensagem}}', '{{acao_url}}', '{{acao_texto}}', '{{site_name}}'],
            'subject' => '{{titulo}} — {{site_name}}',
            'body'    => "{{mensagem}}\n\n{{acao_url}}\n\nAtenciosamente,\nEquipe {{site_name}}",
        ],
    ];

    public function index()
    {
        $templates = $this->templates;
        return view('modules.settings.email-templates', compact('templates'));
    }

    public function edit(string $slug)
    {
        if (!isset($this->templates[$slug])) {
            return redirect()->route('admin.settings.email.templates')->with('error', 'Template não encontrado.');
        }

        $meta     = $this->templates[$slug];
        $subject  = Setting::get("email_tpl_{$slug}_subject", $meta['subject']);
        $body     = Setting::get("email_tpl_{$slug}_body",    $meta['body']);
        $siteName = Setting::get('site_name', 'HomeMechanic');

        return view('modules.settings.email-template-edit', compact('slug', 'meta', 'subject', 'body', 'siteName'));
    }

    public function update(Request $request, string $slug)
    {
        if (!isset($this->templates[$slug])) {
            return back()->with('error', 'Template não encontrado.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        Setting::set("email_tpl_{$slug}_subject", $request->input('subject'), 'email_templates');
        Setting::set("email_tpl_{$slug}_body",    $request->input('body'),    'email_templates');

        return back()->with('success', 'Template "' . $this->templates[$slug]['name'] . '" salvo com sucesso!');
    }

    public function preview(Request $request)
    {
        $subject  = $request->input('subject', '');
        $body     = $request->input('body', '');
        $siteName = Setting::get('site_name', 'HomeMechanic');
        $primary  = '#FF6B00';

        $vars = [
            '{{nome}}'              => 'João Silva',
            '{{email}}'             => 'joao@email.com',
            '{{assunto}}'           => 'Orçamento para revisão',
            '{{mensagem_original}}' => 'Gostaria de um orçamento para revisão do meu veículo.',
            '{{resposta}}'          => 'Olá! Ficamos felizes em atendê-lo. Nosso orçamento para revisão completa é de R$ 350,00.',
            '{{site_name}}'         => $siteName,
            '{{login_url}}'         => url('/admin/login'),
            '{{reset_url}}'         => url('/admin/login') . '?reset=exemplo',
            '{{expiry}}'            => '2 horas',
            '{{titulo}}'            => 'Nova mensagem recebida',
            '{{mensagem}}'          => 'Você recebeu uma nova mensagem de contato.',
            '{{acao_url}}'          => url('/admin/contact'),
            '{{acao_texto}}'        => 'Ver Mensagem',
        ];

        $subjectParsed = str_replace(array_keys($vars), array_values($vars), $subject);
        $bodyParsed    = str_replace(array_keys($vars), array_values($vars), $body);

        // Detectar se é HTML ou texto simples
        $isHtml = strip_tags($bodyParsed) !== $bodyParsed;
        $bodyContent = $isHtml ? $bodyParsed : nl2br(e($bodyParsed));

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$subjectParsed}</title>
<style>
  *{box-sizing:border-box;}
  body{margin:0;padding:0;background:#f0f2f5;font-family:'Segoe UI',Arial,sans-serif;}
  .wrap{max-width:600px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);}
  .header{background:{$primary};padding:24px 32px;text-align:center;}
  .header h1{color:#fff;margin:0;font-size:1.3rem;font-weight:700;letter-spacing:0.5px;}
  .body{padding:28px 32px;}
  .body p{color:#374151;font-size:0.93rem;line-height:1.7;margin:0 0 1rem;}
  .body a{color:{$primary};}
  .body img{max-width:100%;height:auto;}
  .body table{width:100%;border-collapse:collapse;}
  .body td,.body th{padding:8px;border:1px solid #e2e8f0;}
  .footer{background:#f8fafc;padding:16px 32px;text-align:center;border-top:1px solid #e2e8f0;}
  .footer p{color:#94a3b8;font-size:0.78rem;margin:0;}
  .footer a{color:{$primary};text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header"><h1>{$siteName}</h1></div>
  <div class="body">{$bodyContent}</div>
  <div class="footer">
    <p>&copy; {$siteName} &mdash; <a href="#">Cancelar inscrição</a></p>
  </div>
</div>
</body>
</html>
HTML;

        return response()->json([
            'subject' => $subjectParsed,
            'html'    => $html,
        ]);
    }
}
