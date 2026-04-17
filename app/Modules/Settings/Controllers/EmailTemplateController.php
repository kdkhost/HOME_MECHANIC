<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    private array $templates = [
        'contact_reply' => [
            'name' => 'Resposta de Contato',
            'desc' => 'Enviado ao cliente quando você responde uma mensagem de contato.',
            'icon' => 'fas fa-reply',
            'vars' => ['{{nome}}', '{{assunto}}', '{{mensagem_original}}', '{{resposta}}', '{{site_name}}'],
            'subject' => 'Re: {{assunto}} — {{site_name}}',
        ],
        'welcome' => [
            'name' => 'Boas-vindas',
            'desc' => 'Enviado ao novo usuário após o cadastro.',
            'icon' => 'fas fa-hand-wave',
            'vars' => ['{{nome}}', '{{email}}', '{{site_name}}', '{{login_url}}'],
            'subject' => 'Bem-vindo à {{site_name}}!',
        ],
        'password_reset' => [
            'name' => 'Redefinição de Senha',
            'desc' => 'Enviado quando o usuário solicita redefinição de senha.',
            'icon' => 'fas fa-key',
            'vars' => ['{{nome}}', '{{reset_url}}', '{{expiry}}', '{{site_name}}'],
            'subject' => 'Redefinição de senha — {{site_name}}',
        ],
        'notification' => [
            'name' => 'Notificação Geral',
            'desc' => 'Template genérico para notificações do sistema.',
            'icon' => 'fas fa-bell',
            'vars' => ['{{titulo}}', '{{mensagem}}', '{{acao_url}}', '{{acao_texto}}', '{{site_name}}'],
            'subject' => '{{titulo}} — {{site_name}}',
        ],
    ];

    /** Corpo padrão HTML responsivo para cada template */
    private function defaultBody(string $slug, string $siteName, string $primary = '#FF6B00'): string
    {
        $bodies = [
            'contact_reply' => <<<HTML
<p>Olá, <strong>{{nome}}</strong>!</p>
<p>Obrigado por entrar em contato conosco. Estamos respondendo à sua mensagem sobre: <em>{{assunto}}</em>.</p>
<blockquote style="border-left:4px solid {$primary};margin:16px 0;padding:12px 16px;background:#fff8f5;color:#555;font-style:italic;">
  {{mensagem_original}}
</blockquote>
<p>{{resposta}}</p>
<p>Se tiver mais dúvidas, não hesite em nos contatar novamente.</p>
<p>Atenciosamente,<br><strong>Equipe {{site_name}}</strong></p>
HTML,
            'welcome' => <<<HTML
<p>Olá, <strong>{{nome}}</strong>! 🎉</p>
<p>Seja muito bem-vindo(a) à <strong>{{site_name}}</strong>! Sua conta foi criada com sucesso.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
  <tr>
    <td style="padding:4px 0;color:#555;"><strong>E-mail:</strong></td>
    <td style="padding:4px 0;color:#555;">{{email}}</td>
  </tr>
</table>
<p style="text-align:center;margin:28px 0;">
  <a href="{{login_url}}" style="display:inline-block;background:{$primary};color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:700;font-size:0.95rem;">
    Acessar o Painel
  </a>
</p>
<p style="font-size:0.85rem;color:#888;">Se você não criou esta conta, ignore este e-mail.</p>
<p>Atenciosamente,<br><strong>Equipe {{site_name}}</strong></p>
HTML,
            'password_reset' => <<<HTML
<p>Olá, <strong>{{nome}}</strong>!</p>
<p>Recebemos uma solicitação para redefinir a senha da sua conta na <strong>{{site_name}}</strong>.</p>
<p style="text-align:center;margin:28px 0;">
  <a href="{{reset_url}}" style="display:inline-block;background:{$primary};color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:700;font-size:0.95rem;">
    Redefinir Minha Senha
  </a>
</p>
<p style="background:#fff8f5;border:1px solid #ffe0cc;border-radius:6px;padding:12px 16px;font-size:0.88rem;color:#555;">
  ⏱️ Este link é válido por <strong>{{expiry}}</strong>. Após esse prazo, você precisará solicitar um novo link.
</p>
<p style="font-size:0.85rem;color:#888;">Se você não solicitou a redefinição de senha, ignore este e-mail. Sua senha permanece a mesma.</p>
<p>Atenciosamente,<br><strong>Equipe {{site_name}}</strong></p>
HTML,
            'notification' => <<<HTML
<p><strong>{{titulo}}</strong></p>
<p>{{mensagem}}</p>
<p style="text-align:center;margin:28px 0;">
  <a href="{{acao_url}}" style="display:inline-block;background:{$primary};color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:700;font-size:0.95rem;">
    {{acao_texto}}
  </a>
</p>
<p>Atenciosamente,<br><strong>Equipe {{site_name}}</strong></p>
HTML,
        ];

        return $bodies[$slug] ?? '<p>Conteúdo do e-mail aqui.</p>';
    }

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

        $siteName = Setting::get('site_name', 'HomeMechanic');
        $meta     = $this->templates[$slug];
        $subject  = Setting::get("email_tpl_{$slug}_subject", $meta['subject']);
        $body     = Setting::get("email_tpl_{$slug}_body",    $this->defaultBody($slug, $siteName));

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
        $year     = date('Y');

        $vars = [
            '{{nome}}'              => 'João Silva',
            '{{email}}'             => 'joao@email.com',
            '{{assunto}}'           => 'Orçamento para revisão',
            '{{mensagem_original}}' => 'Gostaria de um orçamento para revisão do meu veículo.',
            '{{resposta}}'          => 'Ficamos felizes em atendê-lo! Nosso orçamento para revisão completa é de R$ 350,00.',
            '{{site_name}}'         => $siteName,
            '{{login_url}}'         => url('/admin/login'),
            '{{reset_url}}'         => url('/admin/login') . '?reset=exemplo',
            '{{expiry}}'            => '2 horas',
            '{{titulo}}'            => 'Nova mensagem recebida',
            '{{mensagem}}'          => 'Você recebeu uma nova mensagem de contato no painel.',
            '{{acao_url}}'          => url('/admin/contact'),
            '{{acao_texto}}'        => 'Ver Mensagem',
        ];

        $subjectParsed = str_replace(array_keys($vars), array_values($vars), $subject);
        $bodyParsed    = str_replace(array_keys($vars), array_values($vars), $body);

        $isHtml      = strip_tags($bodyParsed) !== $bodyParsed;
        $bodyContent = $isHtml ? $bodyParsed : '<p>' . nl2br(e($bodyParsed)) . '</p>';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{$subjectParsed}</title>
  <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; max-width: 100%; }
    a { color: {$primary}; }
    .email-wrapper { width: 100%; background-color: #f0f2f5; padding: 24px 16px; }
    .email-container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .email-header { background: {$primary}; padding: 28px 32px; text-align: center; }
    .email-header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
    .email-body { padding: 32px; color: #374151; font-size: 15px; line-height: 1.7; }
    .email-body p { margin: 0 0 16px; }
    .email-body p:last-child { margin-bottom: 0; }
    .email-body a { color: {$primary}; text-decoration: underline; }
    .email-body blockquote { border-left: 4px solid {$primary}; margin: 16px 0; padding: 12px 16px; background: #fff8f5; color: #555; font-style: italic; border-radius: 0 6px 6px 0; }
    .email-body img { max-width: 100%; height: auto; border-radius: 6px; }
    .email-body table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    .email-body td, .email-body th { padding: 10px 12px; border: 1px solid #e2e8f0; font-size: 14px; }
    .email-body th { background: #f8fafc; font-weight: 600; }
    .email-body ul, .email-body ol { padding-left: 24px; margin: 0 0 16px; }
    .email-body li { margin-bottom: 6px; }
    .email-footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
    .email-footer p { color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.6; }
    .email-footer a { color: {$primary}; text-decoration: none; }
    @media only screen and (max-width: 600px) {
      .email-wrapper { padding: 12px 8px; }
      .email-header { padding: 20px 20px; }
      .email-header h1 { font-size: 18px; }
      .email-body { padding: 20px; font-size: 14px; }
      .email-footer { padding: 16px 20px; }
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="email-container">
      <div class="email-header">
        <h1>{$siteName}</h1>
      </div>
      <div class="email-body">
        {$bodyContent}
      </div>
      <div class="email-footer">
        <p>
          &copy; {$year} <strong>{$siteName}</strong>. Todos os direitos reservados.<br>
          <a href="#">Cancelar inscrição</a> &bull; <a href="#">Política de Privacidade</a>
        </p>
      </div>
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
