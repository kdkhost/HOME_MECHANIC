<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Dashboard de analytics
     */
    public function index(Request $request)
    {
        try {
            // Dados básicos para a view (sem dependência do AnalyticsService)
            $stats = [
                'total_visits' => 0, // Placeholder
                'unique_visits' => 0, // Placeholder
                'online_now' => 0, // Placeholder
                'avg_time' => 0 // Placeholder
            ];
            
            // Tentar obter dados reais se a tabela analytics existir
            try {
                if (DB::getSchemaBuilder()->hasTable('analytics')) {
                    $stats = [
                        'total_visits' => DB::table('analytics')->where('is_bot', false)->count(),
                        'unique_visits' => DB::table('analytics')->where('is_bot', false)->where('is_unique', true)->count(),
                        'online_now' => DB::table('analytics')->where('created_at', '>=', now()->subMinutes(5))->where('is_bot', false)->distinct('session_id')->count(),
                        'avg_time' => DB::table('analytics')->where('is_bot', false)->avg('time_on_page') ?? 0
                    ];
                }
            } catch (\Exception $e) {
                // Se a tabela não existir, usar valores padrão
                Log::info('Tabela analytics não existe ainda', ['error' => $e->getMessage()]);
            }
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $stats
                ]);
            }

            return view('modules.analytics.index', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar analytics', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar estatísticas.');
        }
    }

    /**
     * Obter dados via AJAX
     */
    public function getData(Request $request)
    {
        try {
            $period = $request->input('period', 30);
            
            $data = [
                'visits' => ['labels' => [], 'datasets' => []],
                'devices' => ['labels' => [], 'datasets' => []],
                'browsers' => ['labels' => [], 'data' => []],
                'countries' => ['labels' => [], 'data' => []],
                'pages' => ['labels' => [], 'data' => []],
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados'
            ], 500);
        }
    }

    /**
     * Obter visitantes
     */
    public function getVisitors(Request $request)
    {
        try {
            $visitors = [];
            
            // Tentar obter dados reais se a tabela existir
            try {
                if (DB::getSchemaBuilder()->hasTable('analytics')) {
                    $visitors = DB::table('analytics')
                        ->select('ip_address', 'country', 'city', 'device_type', 'browser', 'created_at')
                        ->where('is_bot', false)
                        ->orderBy('created_at', 'desc')
                        ->limit(100)
                        ->get()
                        ->toArray();
                }
            } catch (\Exception $e) {
                // Tabela não existe
            }
            
            return response()->json([
                'success' => true,
                'data' => $visitors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar visitantes'
            ], 500);
        }
    }

    /**
     * Obter páginas mais visitadas
     */
    public function getPages(Request $request)
    {
        try {
            $pages = [];
            
            // Tentar obter dados reais se a tabela existir
            try {
                if (DB::getSchemaBuilder()->hasTable('analytics')) {
                    $pages = DB::table('analytics')
                        ->select('url', DB::raw('COUNT(*) as visits'))
                        ->where('is_bot', false)
                        ->groupBy('url')
                        ->orderByDesc('visits')
                        ->limit(20)
                        ->get()
                        ->toArray();
                }
            } catch (\Exception $e) {
                // Tabela não existe
            }
            
            return response()->json([
                'success' => true,
                'data' => $pages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar páginas'
            ], 500);
        }
    }

    /**
     * Relatório detalhado
     */
    public function report(Request $request)
    {
        try {
            $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));
            $groupBy = $request->input('group_by', 'day'); // day, week, month

            $data = $this->generateReport($startDate, $endDate, $groupBy);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return view('modules.analytics.report', compact('data', 'startDate', 'endDate', 'groupBy'));

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao gerar relatório.');
        }
    }

    /**
     * Dados para gráficos
     */
    public function chartData(Request $request)
    {
        try {
            $type = $request->input('type', 'visits'); // visits, devices, browsers, countries
            $period = $request->input('period', '30'); // 7, 30, 90 dias

            $data = match ($type) {
                'visits' => $this->getVisitsChartData($period),
                'devices' => $this->getDevicesChartData($period),
                'browsers' => $this->getBrowsersChartData($period),
                'countries' => $this->getCountriesChartData($period),
                'pages' => $this->getPagesChartData($period),
                'referrers' => $this->getReferrersChartData($period),
                default => []
            };

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter dados do gráfico', [
                'error' => $e->getMessage(),
                'type' => $request->input('type'),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados do gráfico.'
            ], 500);
        }
    }

    /**
     * Visitantes online
     */
    public function onlineVisitors(Request $request)
    {
        try {
            // Considerar visitantes dos últimos 5 minutos como "online"
            $onlineCount = DB::table('analytics')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->where('is_bot', false)
                ->distinct('session_id')
                ->count();

            // Últimas páginas visitadas
            $recentPages = DB::table('analytics')
                ->select('url', 'created_at', 'country', 'device_type')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->where('is_bot', false)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'online_count' => $onlineCount,
                    'recent_pages' => $recentPages
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao obter visitantes online', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar visitantes online.'
            ], 500);
        }
    }

    /**
     * Limpar dados antigos
     */
    public function cleanup(Request $request)
    {
        try {
            $daysToKeep = $request->input('days', 365);
            $deletedCount = $this->analyticsService->cleanupOldData($daysToKeep);

            Log::info('Limpeza de analytics executada', [
                'days_to_keep' => $daysToKeep,
                'deleted_count' => $deletedCount,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Limpeza concluída. {$deletedCount} registros removidos.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Erro na limpeza de analytics', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro na limpeza dos dados.'
            ], 500);
        }
    }

    /**
     * Gerar relatório personalizado
     */
    private function generateReport(string $startDate, string $endDate, string $groupBy): array
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Formato de agrupamento
        $dateFormat = match ($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        // Visitas por período
        $visitsByPeriod = DB::table('analytics')
            ->select(DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"), DB::raw('COUNT(*) as visits'))
            ->where('is_bot', false)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Visitas únicas por período
        $uniqueVisitsByPeriod = DB::table('analytics')
            ->select(DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"), DB::raw('COUNT(*) as unique_visits'))
            ->where('is_bot', false)
            ->where('is_unique', true)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'period' => [
                'start' => $start->format('d/m/Y'),
                'end' => $end->format('d/m/Y'),
                'group_by' => $groupBy
            ],
            'visits_by_period' => $visitsByPeriod,
            'unique_visits_by_period' => $uniqueVisitsByPeriod,
            'summary' => [
                'total_visits' => DB::table('analytics')->where('is_bot', false)->whereBetween('created_at', [$start, $end])->count(),
                'unique_visits' => DB::table('analytics')->where('is_bot', false)->where('is_unique', true)->whereBetween('created_at', [$start, $end])->count(),
                'top_pages' => DB::table('analytics')->select('url', DB::raw('COUNT(*) as visits'))->where('is_bot', false)->whereBetween('created_at', [$start, $end])->groupBy('url')->orderByDesc('visits')->limit(10)->get(),
                'top_countries' => DB::table('analytics')->select('country', DB::raw('COUNT(*) as visits'))->where('is_bot', false)->whereNotNull('country')->whereBetween('created_at', [$start, $end])->groupBy('country')->orderByDesc('visits')->limit(10)->get()
            ]
        ];
    }

    /**
     * Dados do gráfico de visitas
     */
    private function getVisitsChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as visits'), DB::raw('COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_visits'))
            ->where('is_bot', false)
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Visitas Totais',
                    'data' => $data->pluck('visits')->toArray(),
                    'borderColor' => '#FF6B00',
                    'backgroundColor' => 'rgba(255, 107, 0, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Visitas Únicas',
                    'data' => $data->pluck('unique_visits')->toArray(),
                    'borderColor' => '#0D0D0D',
                    'backgroundColor' => 'rgba(13, 13, 13, 0.1)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    /**
     * Dados do gráfico de dispositivos
     */
    private function getDevicesChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->where('created_at', '>=', $startDate)
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        $colors = ['#FF6B00', '#0D0D0D', '#1A1A1A', '#FF8C42', '#2D2D2D'];

        return [
            'labels' => $data->pluck('device_type')->map(fn($type) => ucfirst($type))->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }

    /**
     * Dados do gráfico de navegadores
     */
    private function getBrowsersChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->where('created_at', '>=', $startDate)
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('browser')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    /**
     * Dados do gráfico de países
     */
    private function getCountriesChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->whereNotNull('country')
            ->where('created_at', '>=', $startDate)
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('country')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    /**
     * Dados das páginas mais visitadas
     */
    private function getPagesChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select('url', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->where('created_at', '>=', $startDate)
            ->groupBy('url')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Simplificar URLs para exibição
        $labels = $data->pluck('url')->map(function ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path === '/' ? 'Página Inicial' : basename($path);
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $data->pluck('count')->toArray()
        ];
    }

    /**
     * Dados dos referenciadores
     */
    private function getReferrersChartData(int $days): array
    {
        $startDate = now()->subDays($days);
        
        $data = DB::table('analytics')
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->where('is_bot', false)
            ->whereNotNull('referer')
            ->where('created_at', '>=', $startDate)
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Simplificar referenciadores para exibição
        $labels = $data->pluck('referer')->map(function ($referer) {
            $host = parse_url($referer, PHP_URL_HOST);
            return $host ?: 'Direto';
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $data->pluck('count')->toArray()
        ];
    }
}