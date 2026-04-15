<?php

namespace App\Modules\Seo\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SeoSetting;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SeoController extends Controller
{
    private SeoService $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->middleware('auth');
        $this->seoService = $seoService;
    }

    /**
     * Listar configurações SEO
     */
    public function index(Request $request)
    {
        try {
            $query = SeoSetting::query();

            // Filtros
            if ($request->has('page_type') && $request->filled('page_type')) {
                $query->where('page_type', $request->input('page_type'));
            }

            if ($request->has('search') && $request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('meta_title', 'like', "%{$search}%")
                      ->orWhere('meta_description', 'like', "%{$search}%")
                      ->orWhere('page_identifier', 'like', "%{$search}%");
                });
            }

            // Ordenação
            $query->orderBy('page_type')->orderBy('page_identifier');

            if ($request->wantsJson()) {
                $seoSettings = $query->paginate(20);
                
                return response()->json([
                    'success' => true,
                    'data' => $seoSettings->items(),
                    'pagination' => [
                        'current_page' => $seoSettings->currentPage(),
                        'last_page' => $seoSettings->lastPage(),
                        'per_page' => $seoSettings->perPage(),
                        'total' => $seoSettings->total()
                    ]
                ]);
            }

            $seoSettings = $query->paginate(20);
            $pageTypes = $this->getPageTypes();

            return view('modules.seo.index', compact('seoSettings', 'pageTypes'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar configurações SEO', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar configurações SEO.');
        }
    }

    /**
     * Exibir formulário de criação/edição
     */
    public function create(Request $request)
    {
        $pageType = $request->input('page_type', 'home');
        $pageIdentifier = $request->input('page_identifier');
        
        $seoSetting = SeoSetting::where('page_type', $pageType)
                               ->where('page_identifier', $pageIdentifier)
                               ->first();

        if (!$seoSetting) {
            $seoSetting = new SeoSetting([
                'page_type' => $pageType,
                'page_identifier' => $pageIdentifier,
                'index' => true,
                'follow' => true
            ]);
        }

        $pageTypes = $this->getPageTypes();
        $hashtags = $this->seoService->generateHashtags($pageType);

        return view('modules.seo.form', compact('seoSetting', 'pageTypes', 'hashtags'));
    }

    /**
     * Salvar configurações SEO
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'page_type' => 'required|string|max:50',
                'page_identifier' => 'nullable|string|max:100',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:1000',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string|max:500',
                'og_image' => 'nullable|url|max:500',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string|max:500',
                'twitter_image' => 'nullable|url|max:500',
                'custom_head_tags' => 'nullable|string',
                'canonical_url' => 'nullable|url|max:500',
                'index' => 'boolean',
                'follow' => 'boolean'
            ]);

            $data = $request->only([
                'meta_title', 'meta_description', 'meta_keywords',
                'og_title', 'og_description', 'og_image', 'og_type',
                'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
                'custom_head_tags', 'canonical_url', 'index', 'follow'
            ]);

            // Gerar Schema.org automaticamente se solicitado
            if ($request->boolean('generate_schema')) {
                $schemaData = $this->prepareSchemaData($request);
                $data['schema_markup'] = $this->seoService->generateSchemaMarkup(
                    $request->input('page_type'),
                    $schemaData
                );
            } else {
                $data['schema_markup'] = $request->input('schema_markup');
            }

            $success = $this->seoService->saveSeoSettings(
                $request->input('page_type'),
                $data,
                $request->input('page_identifier')
            );

            if ($success) {
                // Registrar no audit log
                AuditLog::record('seo_settings_updated', new SeoSetting(), [], $data);

                Log::info('Configurações SEO salvas com sucesso', [
                    'page_type' => $request->input('page_type'),
                    'page_identifier' => $request->input('page_identifier'),
                    'user_id' => Auth::id()
                ]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Configurações SEO salvas com sucesso!'
                    ]);
                }

                return redirect()->route('admin.seo.index')
                               ->with('success', 'Configurações SEO salvas com sucesso!');
            } else {
                throw new \Exception('Falha ao salvar configurações SEO');
            }

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações SEO', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao salvar configurações SEO.');
        }
    }

    /**
     * Excluir configuração SEO
     */
    public function destroy(SeoSetting $seoSetting, Request $request)
    {
        try {
            $oldData = $seoSetting->toArray();
            $seoSetting->delete();

            // Registrar no audit log
            AuditLog::record('seo_settings_deleted', $seoSetting, $oldData, []);

            Log::info('Configuração SEO excluída', [
                'seo_setting_id' => $seoSetting->id,
                'page_type' => $seoSetting->page_type,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Configuração SEO excluída com sucesso!'
                ]);
            }

            return redirect()->route('admin.seo.index')
                           ->with('success', 'Configuração SEO excluída com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir configuração SEO', [
                'error' => $e->getMessage(),
                'seo_setting_id' => $seoSetting->id,
                'user_id' => Auth::id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno no servidor.'
                ], 500);
            }

            return back()->with('error', 'Erro ao excluir configuração SEO.');
        }
    }

    /**
     * Preview das meta tags
     */
    public function preview(Request $request)
    {
        try {
            $seoData = $request->only([
                'meta_title', 'meta_description', 'meta_keywords',
                'og_title', 'og_description', 'og_image',
                'twitter_title', 'twitter_description', 'twitter_image',
                'canonical_url'
            ]);

            $currentUrl = $request->input('preview_url', url('/'));
            $metaTags = $this->seoService->generateMetaTags($seoData, $currentUrl);

            return response()->json([
                'success' => true,
                'meta_tags' => $metaTags,
                'preview' => [
                    'title' => $seoData['meta_title'] ?: $seoData['og_title'],
                    'description' => $seoData['meta_description'] ?: $seoData['og_description'],
                    'image' => $seoData['og_image'] ?: $seoData['twitter_image'],
                    'url' => $currentUrl
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar preview.'
            ], 500);
        }
    }

    /**
     * Gerar hashtags para redes sociais
     */
    public function generateHashtags(Request $request)
    {
        try {
            $pageType = $request->input('page_type', 'home');
            $data = $request->input('data', []);
            
            $hashtags = $this->seoService->generateHashtags($pageType, $data);

            return response()->json([
                'success' => true,
                'hashtags' => $hashtags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar hashtags.'
            ], 500);
        }
    }

    /**
     * Análise SEO da página
     */
    public function analyze(Request $request)
    {
        try {
            $url = $request->input('url');
            $seoData = $request->only([
                'meta_title', 'meta_description', 'meta_keywords'
            ]);

            $analysis = $this->performSeoAnalysis($seoData, $url);

            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao analisar SEO.'
            ], 500);
        }
    }

    /**
     * Obter tipos de página disponíveis
     */
    private function getPageTypes(): array
    {
        return [
            'home' => 'Página Inicial',
            'services' => 'Serviços',
            'gallery' => 'Galeria',
            'blog' => 'Blog',
            'contact' => 'Contato',
            'about' => 'Sobre Nós',
            'custom' => 'Página Personalizada'
        ];
    }

    /**
     * Preparar dados para Schema.org
     */
    private function prepareSchemaData(Request $request): array
    {
        return [
            'name' => $request->input('meta_title'),
            'description' => $request->input('meta_description'),
            'url' => $request->input('canonical_url', url('/')),
            'image' => $request->input('og_image'),
            'title' => $request->input('meta_title'),
            'author' => 'George Marcelo (Marcelo Brad RJ)',
            'logo' => url('/img/logo.png'),
            'published_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Realizar análise SEO
     */
    private function performSeoAnalysis(array $seoData, ?string $url = null): array
    {
        $analysis = [
            'score' => 0,
            'issues' => [],
            'recommendations' => [],
            'good_practices' => []
        ];

        // Análise do título
        if (empty($seoData['meta_title'])) {
            $analysis['issues'][] = 'Título meta não definido';
        } elseif (strlen($seoData['meta_title']) < 30) {
            $analysis['issues'][] = 'Título muito curto (menos de 30 caracteres)';
        } elseif (strlen($seoData['meta_title']) > 60) {
            $analysis['issues'][] = 'Título muito longo (mais de 60 caracteres)';
        } else {
            $analysis['good_practices'][] = 'Título com tamanho adequado';
            $analysis['score'] += 25;
        }

        // Análise da descrição
        if (empty($seoData['meta_description'])) {
            $analysis['issues'][] = 'Meta descrição não definida';
        } elseif (strlen($seoData['meta_description']) < 120) {
            $analysis['issues'][] = 'Meta descrição muito curta (menos de 120 caracteres)';
        } elseif (strlen($seoData['meta_description']) > 160) {
            $analysis['issues'][] = 'Meta descrição muito longa (mais de 160 caracteres)';
        } else {
            $analysis['good_practices'][] = 'Meta descrição com tamanho adequado';
            $analysis['score'] += 25;
        }

        // Análise das palavras-chave
        if (!empty($seoData['meta_keywords'])) {
            $keywords = explode(',', $seoData['meta_keywords']);
            if (count($keywords) > 10) {
                $analysis['recommendations'][] = 'Considere usar menos de 10 palavras-chave';
            } else {
                $analysis['good_practices'][] = 'Número adequado de palavras-chave';
                $analysis['score'] += 25;
            }
        }

        // Verificar se contém marca
        $brandKeywords = ['home mechanic', 'marcelo brad', 'george marcelo'];
        $hasBrand = false;
        
        foreach ($brandKeywords as $brand) {
            if (stripos($seoData['meta_title'] ?? '', $brand) !== false ||
                stripos($seoData['meta_description'] ?? '', $brand) !== false) {
                $hasrand = true;
                break;
            }
        }

        if ($hasBrand) {
            $analysis['good_practices'][] = 'Contém referência à marca';
            $analysis['score'] += 25;
        } else {
            $analysis['recommendations'][] = 'Considere incluir a marca "Home Mechanic" no título ou descrição';
        }

        return $analysis;
    }
}