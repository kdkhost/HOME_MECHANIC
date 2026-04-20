<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Exibir dashboard principal
     */
    public function index()
    {
        // Buscar dados com cache para performance
        $dashboardData = Cache::remember('dashboard_data_' . Auth::id(), 300, function () {
            return $this->getDashboardData();
        });

        return view('modules.dashboard.index', [
            'data' => $dashboardData,
            'user' => Auth::user()
        ]);
    }

    /**
     * Obter dados do dashboard via AJAX
     */
    public function getData(Request $request)
    {
        $data = $this->getDashboardData();
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'last_updated' => now()->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Obter atividade recente via AJAX (tempo real)
     */
    public function getRecentActivityAjax(Request $request)
    {
        $activities = $this->getRecentActivity();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    /**
     * Obter estatísticas rápidas
     */
    public function getQuickStats(Request $request)
    {
        $visits = $this->getVisitStats();
        return response()->json([
            'services_count'  => $this->getServicesCount(),
            'posts_published' => $this->getPublishedPostsCount(),
            'gallery_photos'  => $this->getGalleryPhotosCount(),
            'unread_messages' => $this->getUnreadMessagesCount(),
            'total_messages'  => $this->getTotalMessagesCount(),
            'visits_today'    => $visits['today'],
            'visits_month'    => $visits['month'],
            'online_now'      => $visits['online'],
        ]);
    }

    /**
     * Limpar cache do dashboard
     */
    public function clearCache(Request $request)
    {
        Cache::forget('dashboard_data_' . Auth::id());
        Cache::forget('quick_stats');
        Cache::forget('recent_activity');
        
        return response()->json([
            'success' => true,
            'message' => 'Cache do dashboard limpo com sucesso!'
        ]);
    }

    /**
     * Limpar caches do Laravel — suporta tipo específico ou todos
     */
    public function clearAllCache(Request $request)
    {
        $type    = $request->input('type', 'all');
        $results = [];
        $errors  = 0;

        $run = function (string $cmd, string $label) use (&$results, &$errors) {
            try {
                \Illuminate\Support\Facades\Artisan::call($cmd);
                $results[] = "✅ {$label}";
            } catch (\Throwable $e) {
                $results[] = "❌ {$label}: " . $e->getMessage();
                $errors++;
            }
        };

        switch ($type) {
            case 'config': $run('config:clear', 'Cache de configuração limpo'); break;
            case 'view':   $run('view:clear',   'Views compiladas limpas');     break;
            case 'route':  $run('route:clear',  'Cache de rotas limpo');        break;
            case 'app':
                try {
                    Cache::flush();
                    $results[] = '✅ Cache de aplicação limpo';
                } catch (\Throwable $e) {
                    $results[] = '❌ Cache de aplicação: ' . $e->getMessage();
                    $errors++;
                }
                break;
            default: // all
                $run('cache:clear',  'Cache de aplicação limpo');
                $run('view:clear',   'Views compiladas limpas');
                $run('config:clear', 'Cache de configuração limpo');
                $run('route:clear',  'Cache de rotas limpo');
                try { $run('event:clear', 'Cache de eventos limpo'); } catch (\Throwable) {}
                // Limpar cache interno do dashboard
                try {
                    Cache::forget('dashboard_data_' . Auth::id());
                    Cache::forget('quick_stats');
                    Cache::forget('settings_all');
                } catch (\Throwable) {}
                break;
        }

        \Illuminate\Support\Facades\Log::info('Cache limpo pelo admin', [
            'type'    => $type,
            'user_id' => Auth::id(),
            'results' => $results,
        ]);

        $message = $errors === 0
            ? ($type === 'all' ? 'Todos os caches foram limpos!' : 'Cache limpo com sucesso!')
            : 'Concluído com ' . $errors . ' erro(s).';

        return response()->json([
            'success' => $errors === 0,
            'message' => $message,
            'details' => $results,
        ]);
    }

    /**
     * Rodar migrations pendentes
     */
    public function runMigrations()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();

            \Illuminate\Support\Facades\Log::info('Migrations rodadas pelo admin', [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'output'  => $output,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Migrations executadas com sucesso!',
                'output'  => trim($output) ?: 'Nenhuma migration pendente.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rodar migrations: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Obter dados completos do dashboard
     */
    private function getDashboardData(): array
    {
        return [
            // Contadores principais
            'counters' => [
                'services'         => $this->getServicesCount(),
                'posts_published'  => $this->getPublishedPostsCount(),
                'gallery_photos'   => $this->getGalleryPhotosCount(),
                'unread_messages'  => $this->getUnreadMessagesCount(),
            ],
            // Estatísticas detalhadas
            'stats' => [
                'posts_draft'         => $this->getDraftPostsCount(),
                'gallery_categories'  => $this->getGalleryCategoriesCount(),
                'total_messages'      => $this->getTotalMessagesCount(),
                'active_services'     => $this->getActiveServicesCount(),
            ],
            // Visitas (analytics)
            'visits' => $this->getVisitStats(),
            // Atividade recente
            'recent_activity' => $this->getRecentActivity(),
            // Posts recentes
            'recent_posts'    => $this->getRecentPosts(),
            // Mensagens recentes
            'recent_messages' => $this->getRecentMessages(),
            // Informações do sistema
            'system_info'     => $this->getSystemInfo(),
            // Gráficos
            'charts' => [
                'posts_by_month'  => $this->getPostsByMonth(),
                'messages_by_day' => $this->getMessagesByDay(),
                'visits_by_day'   => $this->getVisitsByDay(),
            ],
        ];
    }

    /**
     * Contar serviços ativos
     */
    private function getServicesCount(): int
    {
        try {
            return DB::table('services')->where('active', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar posts publicados
     */
    private function getPublishedPostsCount(): int
    {
        try {
            return DB::table('posts')
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar fotos da galeria
     */
    private function getGalleryPhotosCount(): int
    {
        try {
            return DB::table('gallery_photos')->where('active', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar mensagens não lidas
     */
    private function getUnreadMessagesCount(): int
    {
        try {
            return DB::table('contact_messages')->where('read', false)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar posts em rascunho
     */
    private function getDraftPostsCount(): int
    {
        try {
            return DB::table('posts')->where('status', 'draft')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar categorias da galeria
     */
    private function getGalleryCategoriesCount(): int
    {
        try {
            return DB::table('gallery_categories')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar total de mensagens
     */
    private function getTotalMessagesCount(): int
    {
        try {
            return DB::table('contact_messages')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Contar serviços ativos
     */
    private function getActiveServicesCount(): int
    {
        try {
            return DB::table('services')->where('active', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Obter atividade recente
     */
    private function getRecentActivity(): array
    {
        try {
            return DB::table('audit_logs')
                ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                ->select([
                    'audit_logs.action',
                    'audit_logs.model_type',
                    'audit_logs.created_at',
                    'users.name as user_name'
                ])
                ->orderBy('audit_logs.created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'action' => $item->action,
                        'model' => $this->formatModelName($item->model_type),
                        'user' => $item->user_name ?? 'Sistema',
                        'time' => $item->created_at,
                        'formatted_time' => \Carbon\Carbon::parse($item->created_at)->diffForHumans()
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obter posts recentes
     */
    private function getRecentPosts(): array
    {
        try {
            return DB::table('posts')
                ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                ->leftJoin('blog_categories', 'posts.category_id', '=', 'blog_categories.id')
                ->select([
                    'posts.id',
                    'posts.title',
                    'posts.status',
                    'posts.created_at',
                    'posts.published_at',
                    'users.name as author_name',
                    'blog_categories.name as category_name'
                ])
                ->orderBy('posts.created_at', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obter mensagens recentes
     */
    private function getRecentMessages(): array
    {
        try {
            return DB::table('contact_messages')
                ->select(['id', 'name', 'email', 'subject', 'read', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obter informações do sistema
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->format('d/m/Y H:i:s'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => $this->isMaintenanceMode()
        ];
    }

    /**
     * Obter posts por mês (últimos 6 meses)
     */
    private function getPostsByMonth(): array
    {
        try {
            $months = [];
            $data = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M/Y');
                
                $count = DB::table('posts')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                    
                $data[] = $count;
            }
            
            return [
                'labels' => $months,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Obter mensagens por dia (últimos 7 dias)
     */
    private function getMessagesByDay(): array
    {
        try {
            $days = [];
            $data = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $days[] = $date->format('d/m');
                
                $count = DB::table('contact_messages')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                    
                $data[] = $count;
            }
            
            return [
                'labels' => $days,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Verificar se está em modo de manutenção
     */
    private function isMaintenanceMode(): bool
    {
        try {
            $maintenance = DB::table('settings')
                ->where('key', 'maintenance_mode')
                ->value('value');
                
            return $maintenance === '1' || $maintenance === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Estatísticas de visitas (analytics)
     */
    private function getVisitStats(): array
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('analytics')) {
                return ['today' => 0, 'month' => 0, 'online' => 0, 'total' => 0];
            }
            return [
                'today'  => DB::table('analytics')->where('is_bot', false)->whereDate('created_at', today())->count(),
                'month'  => DB::table('analytics')->where('is_bot', false)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'online' => DB::table('analytics')->where('is_bot', false)->where('created_at', '>=', now()->subMinutes(5))->distinct('session_id')->count(),
                'total'  => DB::table('analytics')->where('is_bot', false)->count(),
            ];
        } catch (\Exception $e) {
            return ['today' => 0, 'month' => 0, 'online' => 0, 'total' => 0];
        }
    }

    /**
     * Gráfico de visitas por dia (últimos 7 dias)
     */
    private function getVisitsByDay(): array
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('analytics')) {
                return ['labels' => [], 'total' => [], 'unique' => []];
            }
            $labels = $total = $unique = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('d/m');
                $total[]  = DB::table('analytics')->where('is_bot', false)->whereDate('created_at', $date->toDateString())->count();
                $unique[] = DB::table('analytics')->where('is_bot', false)->where('is_unique', true)->whereDate('created_at', $date->toDateString())->count();
            }
            return compact('labels', 'total', 'unique');
        } catch (\Exception $e) {
            return ['labels' => [], 'total' => [], 'unique' => []];
        }
    }

    /**
     * Formatar nome do modelo para exibição
     */
    private function formatModelName(?string $modelType): string
    {
        if (!$modelType) {
            return 'Desconhecido';
        }

        $modelNames = [
            'App\\Models\\User' => 'Usuário',
            'App\\Modules\\Services\\Models\\Service' => 'Serviço',
            'App\\Modules\\Blog\\Models\\Post' => 'Post',
            'App\\Modules\\Gallery\\Models\\GalleryPhoto' => 'Foto',
            'App\\Modules\\Testimonials\\Models\\Testimonial' => 'Depoimento',
            'App\\Modules\\Contact\\Models\\ContactMessage' => 'Mensagem'
        ];

        return $modelNames[$modelType] ?? class_basename($modelType);
    }
}