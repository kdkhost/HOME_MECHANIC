<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends Notification
{
    use Queueable;

    /**
     * Cria uma nova instância da notificação.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        $siteName = Setting::get('site_name', 'HomeMechanic');
        $slug = 'welcome';

        $defaultSubject = 'Verifique seu e-mail — ' . $siteName;
        $defaultBody = $this->getDefaultBody($siteName);

        $subject = Setting::get("email_tpl_{$slug}_subject", $defaultSubject);
        $body = Setting::get("email_tpl_{$slug}_body", $defaultBody);

        $vars = [
            '{{nome}}'      => $notifiable->name,
            '{{email}}'     => $notifiable->email,
            '{{site_name}}' => $siteName,
            '{{verify_url}}' => $verifyUrl,
        ];

        $subjectParsed = str_replace(array_keys($vars), array_values($vars), $subject);
        $bodyParsed = str_replace(array_keys($vars), array_values($vars), $body);

        // Substituir {{login_url}} pela URL de verificação se existir no template
        $bodyParsed = str_replace('{{login_url}}', $verifyUrl, $bodyParsed);

        $html = $this->wrapInLayout($subjectParsed, $bodyParsed, $siteName);

        return (new MailMessage)
            ->subject($subjectParsed)
            ->view('emails.custom', ['content' => $html]);
    }

    /**
     * Gerar URL de verificação assinada temporária
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Conteúdo padrão HTML (fallback)
     */
    protected function getDefaultBody($siteName)
    {
        return <<<HTML
<p>Olá, <strong>{{nome}}</strong>!</p>
<p>Seja muito bem-vindo(a) à <strong>{$siteName}</strong>! Para ativar sua conta e acessar o sistema, confirme seu endereço de e-mail:</p>
<p style="text-align:center;margin:28px 0;">
  <a href="{{verify_url}}" style="display:inline-block;background:#FF6B00;color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:700;font-size:0.95rem;">
    Verificar Meu E-mail
  </a>
</p>
<p style="background:#fff8f5;border:1px solid #ffe0cc;border-radius:6px;padding:12px 16px;font-size:0.88rem;color:#555;">
  ⏱️ Este link é válido por <strong>60 minutos</strong>. Após esse prazo, solicite um novo link no painel administrativo.
</p>
<p style="font-size:0.85rem;color:#888;">Se você não criou esta conta, ignore este e-mail.</p>
<p>Atenciosamente,<br><strong>Equipe {$siteName}</strong></p>
HTML;
    }

    /**
     * Envolve o conteúdo no layout padrão do sistema
     */
    protected function wrapInLayout($subject, $body, $siteName)
    {
        $primary = '#FF6B00';
        $year = date('Y');

        $isHtml = strip_tags($body) !== $body;
        $bodyContent = $isHtml ? $body : '<p>' . nl2br(e($body)) . '</p>';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{$subject}</title>
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
    .email-footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
    .email-footer p { color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.6; }
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
          &copy; {$year} <strong>{$siteName}</strong>. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </div>
</body>
</html>
HTML;
    }
}
