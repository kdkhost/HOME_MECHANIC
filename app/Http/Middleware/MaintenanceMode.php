<?php

namespace App\Http\Middleware;

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
                    // Permitir acesso às rotas /admin e arquivos estáticos
                    if ($request->is('admin') || $request->is('admin/*') || $request->is('api/*')) {
                        return $next($request);
                    }
                    
                    // Ler título e mensagem salvos
                    $settings = \DB::table('settings')->pluck('value', 'key');
                    $mTitle   = $settings['maintenance_title'] ?? 'Site em Manutenção';
                    $mMessage = $settings['maintenance_message'] ?? 'Voltaremos em breve. Estamos realizando atualizações.';
                    $mTimer   = $settings['maintenance_timer'] ?? null;
                    $mBg      = $settings['maintenance_bg_image'] ?? null;
                    
                    // Dados de Empresa
                    $contactData = [
                        'phone'    => $settings['contact_phone'] ?? '',
                        'whatsapp' => $settings['whatsapp'] ?? '',
                        'email'    => $settings['contact_email'] ?? '',
                        'favicon'  => $settings['site_favicon'] ?? '',
                        'logo'     => $settings['site_logo'] ?? ''
                    ];
                    
                    // Outras rotas são bloqueadas e recebem a tela de manutenção
                    return response()->view('errors.503', [
                        'title'   => $mTitle,
                        'message' => $mMessage,
                        'timer'   => $mTimer,
                        'bg'      => $mBg,
                        'contact' => $contactData
                    ], 503)->header('Retry-After', 3600);
                }
            }
        } catch (\Exception $e) {
            // Se houver erro (ex: tabela não existe), continuar normalmente
            // Isso permite que o sistema funcione durante a instalação
        }
        
        return $next($request);
    }
}
