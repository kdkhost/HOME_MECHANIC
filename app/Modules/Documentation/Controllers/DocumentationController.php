<?php

namespace App\Modules\Documentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class DocumentationController extends Controller
{
    private CommonMarkConverter $markdownConverter;
    private string $docsPath;

    public function __construct()
    {
        $this->markdownConverter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $this->docsPath = base_path('docs');
    }

    /**
     * Exibir índice da documentação
     */
    public function index()
    {
        $documentationStructure = $this->getDocumentationStructure();
        
        return view('modules.documentation.index', [
            'structure' => $documentationStructure,
            'title' => 'Documentação do Sistema'
        ]);
    }

    /**
     * Exibir documento específico
     */
    public function show(Request $request, string $document = 'README')
    {
        $documentPath = $this->getDocumentPath($document);
        
        if (!File::exists($documentPath)) {
            abort(404, 'Documento não encontrado');
        }

        $content = File::get($documentPath);
        $htmlContent = $this->markdownConverter->convert($content);
        
        // Extrair título do documento
        $title = $this->extractTitle($content) ?: 'Documentação';
        
        // Gerar índice do documento
        $tableOfContents = $this->generateTableOfContents($content);
        
        // Obter navegação (anterior/próximo)
        $navigation = $this->getDocumentNavigation($document);
        
        return view('modules.documentation.show', [
            'content' => $htmlContent,
            'title' => $title,
            'document' => $document,
            'tableOfContents' => $tableOfContents,
            'navigation' => $navigation,
            'lastModified' => File::lastModified($documentPath)
        ]);
    }

    /**
     * Buscar na documentação
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'results' => [],
                'message' => 'Digite um termo para buscar'
            ]);
        }

        $results = $this->searchInDocuments($query);
        
        return response()->json([
            'results' => $results,
            'query' => $query,
            'total' => count($results)
        ]);
    }

    /**
     * Obter estrutura da documentação
     */
    private function getDocumentationStructure(): array
    {
        $structure = [
            'Primeiros Passos' => [
                'README' => 'Visão Geral',
                'instalacao' => 'Guia de Instalação',
                'configuracao-inicial' => 'Configuração Inicial',
                'requisitos' => 'Requisitos do Sistema'
            ],
            'Manual do Usuário' => [
                'manual-usuario' => 'Manual Completo',
                'painel-admin' => 'Painel Administrativo',
                'gerenciamento-conteudo' => 'Gerenciamento de Conteúdo'
            ],
            'Configurações' => [
                'configuracoes-gerais' => 'Configurações Gerais',
                'configuracao-smtp' => 'Configuração SMTP',
                'configuracao-upload' => 'Sistema de Upload',
                'modo-manutencao' => 'Modo de Manutenção'
            ],
            'Personalização' => [
                'personalizacao-visual' => 'Personalização Visual',
                'customizacao-temas' => 'Customização de Temas',
                'configuracao-seo' => 'Configuração SEO'
            ],
            'Desenvolvimento' => [
                'guia-desenvolvedor' => 'Guia do Desenvolvedor',
                'arquitetura' => 'Arquitetura do Sistema',
                'api-reference' => 'API Reference',
                'criando-modulos' => 'Criando Módulos'
            ],
            'Segurança' => [
                'seguranca' => 'Guia de Segurança',
                'backup-restauracao' => 'Backup e Restauração',
                'logs-auditoria' => 'Logs de Auditoria'
            ],
            'Solução de Problemas' => [
                'faq' => 'FAQ - Perguntas Frequentes',
                'troubleshooting' => 'Troubleshooting',
                'codigos-erro' => 'Códigos de Erro'
            ]
        ];

        // Verificar quais documentos existem
        foreach ($structure as $section => $documents) {
            foreach ($documents as $slug => $title) {
                $path = $this->getDocumentPath($slug);
                if (!File::exists($path)) {
                    $structure[$section][$slug] = $title . ' (Em breve)';
                }
            }
        }

        return $structure;
    }

    /**
     * Obter caminho do documento
     */
    private function getDocumentPath(string $document): string
    {
        $document = Str::slug($document);
        return $this->docsPath . '/' . $document . '.md';
    }

    /**
     * Extrair título do documento markdown
     */
    private function extractTitle(string $content): ?string
    {
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Gerar índice do documento
     */
    private function generateTableOfContents(string $content): array
    {
        $toc = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            if (preg_match('/^(#{2,6})\s+(.+)$/', $line, $matches)) {
                $level = strlen($matches[1]);
                $title = trim($matches[2]);
                $anchor = Str::slug($title);
                
                $toc[] = [
                    'level' => $level,
                    'title' => $title,
                    'anchor' => $anchor
                ];
            }
        }
        
        return $toc;
    }

    /**
     * Obter navegação do documento
     */
    private function getDocumentNavigation(string $currentDocument): array
    {
        $allDocuments = [
            'README', 'instalacao', 'manual-usuario', 'guia-desenvolvedor',
            'configuracoes-gerais', 'configuracao-smtp', 'seguranca',
            'faq', 'troubleshooting'
        ];
        
        $currentIndex = array_search($currentDocument, $allDocuments);
        
        $navigation = [
            'previous' => null,
            'next' => null
        ];
        
        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $prevDoc = $allDocuments[$currentIndex - 1];
                $navigation['previous'] = [
                    'slug' => $prevDoc,
                    'title' => $this->getDocumentTitle($prevDoc)
                ];
            }
            
            if ($currentIndex < count($allDocuments) - 1) {
                $nextDoc = $allDocuments[$currentIndex + 1];
                $navigation['next'] = [
                    'slug' => $nextDoc,
                    'title' => $this->getDocumentTitle($nextDoc)
                ];
            }
        }
        
        return $navigation;
    }

    /**
     * Obter título do documento
     */
    private function getDocumentTitle(string $document): string
    {
        $path = $this->getDocumentPath($document);
        
        if (!File::exists($path)) {
            return Str::title(str_replace('-', ' ', $document));
        }
        
        $content = File::get($path);
        return $this->extractTitle($content) ?: Str::title(str_replace('-', ' ', $document));
    }

    /**
     * Buscar em documentos
     */
    private function searchInDocuments(string $query): array
    {
        $results = [];
        $files = File::glob($this->docsPath . '/*.md');
        
        foreach ($files as $file) {
            $content = File::get($file);
            $filename = pathinfo($file, PATHINFO_FILENAME);
            
            // Buscar no conteúdo
            if (stripos($content, $query) !== false) {
                $title = $this->extractTitle($content) ?: $filename;
                
                // Extrair contexto
                $context = $this->extractSearchContext($content, $query);
                
                $results[] = [
                    'document' => $filename,
                    'title' => $title,
                    'context' => $context,
                    'url' => route('admin.documentation.show', $filename)
                ];
            }
        }
        
        return $results;
    }

    /**
     * Extrair contexto da busca
     */
    private function extractSearchContext(string $content, string $query): string
    {
        $position = stripos($content, $query);
        if ($position === false) {
            return '';
        }
        
        $start = max(0, $position - 100);
        $length = 200;
        
        $context = substr($content, $start, $length);
        
        // Destacar termo buscado
        $context = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $context);
        
        return '...' . trim($context) . '...';
    }
}