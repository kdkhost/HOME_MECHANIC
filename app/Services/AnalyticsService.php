<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class AnalyticsService
{
    private Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Registrar visita
     */
    public function trackVisit(Request $request): void
    {
        try {
            $sessionId = session()->getId();
            $ipAddress = $this->getClientIp($request);
            $userAgent = $request->userAgent();
            $url = $request->fullUrl();
            $referer = $request->header('referer');

            // Verificar se é bot
            $isBot = $this->isBot($userAgent);

            // Verificar se é visita única (primeira visita do IP nas últimas 24h)
            $isUnique = $this->isUniqueVisit($ipAddress);

            // Detectar informações do dispositivo
            $this->agent->setUserAgent($userAgent);
            $deviceInfo = $this->getDeviceInfo();

            // Obter localização (cache por IP)
            $location = $this->getLocationByIp($ipAddress);

            // Registrar no banco
            DB::table('analytics')->insert([
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'url' => $url,
                'referer' => $referer,
                'country' => $location['country'] ?? null,
                'city' => $location['city'] ?? null,
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'is_bot' => $isBot,
                'is_unique' => $isUnique,
                'created_at' => now()
            ]);

            // Atualizar contadores em cache
            $this->updateCounters($url, $isBot, $isUnique);

        } catch (\Exception $e) {
            Log::error('Erro ao registrar analytics', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);
        }
    }

    /**
     * Obter IP real do cliente
     */
    private function getClientIp(Request $request): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load Balancer/Proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    /**
     * Verificar se é bot
     */
    private function isBot(?string $userAgent): bool
    {
        if (!$userAgent) return true;

        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python',
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'facebookexternalhit', 'twitterbot', 'linkedinbot',
            'whatsapp', 'telegram', 'discord', 'slack', 'postman', 'insomnia'
        ];

        $userAgent = strtolower($userAgent);
        
        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar se é visita única
     */
    private function isUniqueVisit(string $ipAddress): bool
    {
        $cacheKey = "unique_visit_{$ipAddress}";
        
        if (Cache::has($cacheKey)) {
            return false;
        }

        // Marcar como visitado por 24 horas
        Cache::put($cacheKey, true, now()->addDay());
        
        return true;
    }

    /**
     * Obter informações do dispositivo
     */
    private function getDeviceInfo(): array
    {
        $deviceType = 'desktop';
        
        if ($this->agent->isMobile()) {
            $deviceType = 'mobile';
        } elseif ($this->agent->isTablet()) {
            $deviceType = 'tablet';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $this->agent->browser() ?: 'Unknown',
            'os' => $this->agent->platform() ?: 'Unknown'
        ];
    }

    /**
     * Obter localização por IP (simulado - em produção usar serviço real)
     */
    private function getLocationByIp(string $ipAddress): array
    {
        $cacheKey = "location_{$ipAddress}";
        
        return Cache::remember($cacheKey, now()->addWeek(), function () use ($ipAddress) {
            // Em produção, integrar com serviço como MaxMind, IPInfo, etc.
            // Por enquanto, retornar dados simulados
            
            if ($ipAddress === '127.0.0.1' || str_starts_with($ipAddress, '192.168.')) {
                return ['country' => 'BR', 'city' => 'Local'];
            }

            // Simulação básica baseada no IP
            return [
                'country' => 'BR',
                'city' => 'São Paulo'
            ];
        });
    }

    /**
     * Atualizar contadores em cache
     */
    private function updateCounters(string $url, bool $isBot, bool $isUnique): void
    {
        if ($isBot) return;

        $today = now()->format('Y-m-d');
        
        // Contador geral de hoje
        Cache::increment("visits_today_{$today}");
        
        // Contador único de hoje
        if ($isUnique) {
            Cache::increment("unique_visits_today_{$today}");
        }
        
        // Contador por página
        $urlHash = md5($url);
        Cache::increment("page_visits_{$urlHash}_{$today}");
    }

    /**
     * Obter estatísticas do dashboard
     */
    public function getDashboardStats(): array
    {
        $today = now();
        $yesterday = now()->subDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'today' => [
                'visits' => $this->getVisitsCount($today, $today->copy()->endOfDay()),
                'unique_visits' => $this->getUniqueVisitsCount($today, $today->copy()->endOfDay()),
                'pages_views' => $this->getPageViewsCount($today, $today->copy()->endOfDay())
            ],
            'yesterday' => [
                'visits' => $this->getVisitsCount($yesterday, $yesterday->copy()->endOfDay()),
                'unique_visits' => $this->getUniqueVisitsCount($yesterday, $yesterday->copy()->endOfDay())
            ],
            'this_month' => [
                'visits' => $this->getVisitsCount($thisMonth, $today->copy()->endOfDay()),
                'unique_visits' => $this->getUniqueVisitsCount($thisMonth, $today->copy()->endOfDay())
            ],
            'last_month' => [
                'visits' => $this->getVisitsCount($lastMonth, $lastMonth->copy()->endOfMonth()),
                'unique_visits' => $this->getUniqueVisitsCount($lastMonth, $lastMonth->copy()->endOfMonth())
            ],
            'top_pages' => $this->getTopPages(10),
            'top_referrers' => $this->getTopReferrers(10),
            'device_stats' => $this->getDeviceStats(),
            'browser_stats' => $this->getBrowserStats(),
            'country_stats' => $this->getCountryStats()
        ];
    }

    /**
     * Obter contagem de visitas
     */
    private function getVisitsCount($start, $end): int
    {
        return DB::table('analytics')
            ->where('is_bot', false)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * Obter contagem de visitas únicas
     */
    private function getUniqueVisitsCount($start, $end): int
    {
        return DB::table('analytics')
            ->where('is_bot', false)
            ->where('is_unique', true)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * Obter contagem de visualizações de página
     */
    private function getPageViewsCount($start, $end): int
    {
        return DB::table('analytics')
            ->where('is_bot', false)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * Obter páginas mais visitadas
     */
    private function getTopPages(int $limit = 10): array
    {
        return DB::table('analytics')
            ->select('url', DB::raw('COUNT(*) as visits'))
            ->where('is_bot', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('url')
            ->orderByDesc('visits')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obter principais referenciadores
     */
    private function getTopReferrers(int $limit = 10): array
    {
        return DB::table('analytics')
            ->select('referer', DB::raw('COUNT(*) as visits'))
            ->where('is_bot', false)
            ->whereNotNull('referer')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('referer')
            ->orderByDesc('visits')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obter estatísticas por dispositivo
     */
    private function getDeviceStats(): array
    {
        return DB::table('analytics')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Obter estatísticas por navegador
     */
    private function getBrowserStats(): array
    {
        return DB::table('analytics')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Obter estatísticas por país
     */
    private function getCountryStats(): array
    {
        return DB::table('analytics')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->whereNotNull('country')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Limpar dados antigos (executar via cron)
     */
    public function cleanupOldData(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return DB::table('analytics')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}