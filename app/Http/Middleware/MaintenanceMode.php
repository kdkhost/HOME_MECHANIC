<?php

namespace App\Http\Middleware;

use App\Modules\Upload\Models\Upload;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Verificar se o modo de manutenção está ativo
            $maintenanceMode = \DB::table('settings')
                ->where('key', 'maintenance_mode')
                ->value('value');
                
            if ($maintenanceMode === '1' || $maintenanceMode === 'true') {
                $clientIp = $request->ip();
                
                // Pegar os IPs autorizados e separar por vírgula
                $allowedIpsString = \DB::table('settings')
                    ->where('key', 'maintenance_ips')
                    ->value('value') ?? '';
                
                $allowedIps = array_map('trim', explode(',', $allowedIpsString));
                $allowedIp = in_array($clientIp, $allowedIps);
                
                // Se não está na lista de permitidos
                if (!$allowedIp) {
                    // Permitir acesso às rotas /admin, /maintenance/disable e arquivos estáticos
                    if ($request->is('admin') || $request->is('admin/*') || $request->is('api/*') || $request->is('maintenance/disable')) {
                        return $next($request);
                    }
                    
                    // Ler título e mensagem salvos
                    $settings = \DB::table('settings')->pluck('value', 'key');
                    $mTitle   = $settings['maintenance_title'] ?? 'Site em Manutenção';
                    $mMessage = $settings['maintenance_message'] ?? 'Voltaremos em breve. Estamos realizando atualizações.';
                    $mTimer   = $settings['maintenance_timer'] ?? null;
                    $mTimerAction = $settings['maintenance_timer_action'] ?? 'hide';
                    $mBg      = $settings['maintenance_bg_image'] ?? null;
                    
                    // Resolver UUIDs do FilePond para paths reais
                    $mBg = $this->resolvePath($mBg);
                    $siteLogo    = $this->resolvePath($settings['site_logo'] ?? '');
                    $siteFavicon = $this->resolvePath($settings['site_favicon'] ?? '');
                    
                    // Dados de Empresa
                    $contactData = [
                        'phone'    => $settings['contact_phone'] ?? '',
                        'whatsapp' => $settings['whatsapp'] ?? '',
                        'email'    => $settings['contact_email'] ?? '',
                        'favicon'  => $siteFavicon,
                        'logo'     => $siteLogo,
                    ];
                    
                    // Outras rotas são bloqueadas e recebem a tela de manutenção
                    return response()->view('errors.503', [
                        'title'       => $mTitle,
                        'message'     => $mMessage,
                        'timer'       => $mTimer,
                        'timer_action' => $mTimerAction,
                        'bg'          => $mBg,
                        'contact'     => $contactData
                    ], 503)->header('Retry-After', 3600);
                }
            }
        } catch (\Exception $e) {
            // Se houver erro (ex: tabela não existe), continuar normalmente
            // Isso permite que o sistema funcione durante a instalação
        }
        
        return $next($request);
    }

    /**
     * Resolver path: se for UUID do FilePond, buscar path real na tabela uploads
     */
    private function resolvePath(?string $value): ?string
    {
        if (!$value) return null;

        // URL externa — retorna direto
        if (str_starts_with($value, 'http')) return $value;

        // UUID do FilePond (sem barra) — resolver para path real
        if (!str_contains($value, '/')) {
            $upload = Upload::where('uuid', $value)->first();
            return $upload ? $upload->path : null;
        }

        return $value;
    }
}
