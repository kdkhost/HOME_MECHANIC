<?php

namespace App\Services;

use App\Models\SeoSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SeoService
{
    /**
     * Obter configurações SEO para uma página
     */
    public function getSeoSettings(string $pageType, ?string $pageIdentifier = null): array
    {
        $cacheKey = "seo_{$pageType}" . ($pageIdentifier ? "_{$pageIdentifier}" : '');
        
        return Cache::remember($cacheKey, now()->addHour(), function () use ($pageType, $pageIdentifier) {
            $seoSetting = SeoSetting::where('page_type', $pageType)
                ->where('page_identifier', $pageIdentifier)
                ->first();

            if (!$seoSetting) {
                return $this->getDefaultSeoSettings($pageType);
            }

            return [
                'meta_title' => $seoSetting->meta_title,
                'meta_description' => $seoSetting->meta_description,
                'meta_keywords' => $seoSetting->meta_keywords,
                'og_title' => $seoSetting->og_title,
                'og_description' => $seoSetting->og_description,
                'og_image' => $seoSetting->og_image,
                'og_type' => $seoSetting->og_type,
                'twitter_card' => $seoSetting->twitter_card,
                'twitter_title' => $seoSetting->twitter_title,
                'twitter_description' => $seoSetting->twitter_description,
                'twitter_image' => $seoSetting->twitter_image,
                'custom_head_tags' => $seoSetting->custom_head_tags,
                'schema_markup' => $seoSetting->schema_markup,
                'canonical_url' => $seoSetting->canonical_url,
                'robots' => $this->getRobotsContent($seoSetting->index, $seoSetting->follow)
            ];
        });
    }

    /**
     * Salvar configurações SEO
     */
    public function saveSeoSettings(string $pageType, array $data, ?string $pageIdentifier = null): bool
    {
        try {
            SeoSetting::updateOrCreate(
                [
                    'page_type' => $pageType,
                    'page_identifier' => $pageIdentifier
                ],
                [
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                    'og_title' => $data['og_title'] ?? null,
                    'og_description' => $data['og_description'] ?? null,
                    'og_image' => $data['og_image'] ?? null,
                    'og_type' => $data['og_type'] ?? 'website',
                    'twitter_card' => $data['twitter_card'] ?? 'summary_large_image',
                    'twitter_title' => $data['twitter_title'] ?? null,
                    'twitter_description' => $data['twitter_description'] ?? null,
                    'twitter_image' => $data['twitter_image'] ?? null,
                    'custom_head_tags' => $data['custom_head_tags'] ?? null,
                    'schema_markup' => $data['schema_markup'] ?? null,
                    'canonical_url' => $data['canonical_url'] ?? null,
                    'index' => $data['index'] ?? true,
                    'follow' => $data['follow'] ?? true
                ]
            );

            // Limpar cache
            $cacheKey = "seo_{$pageType}" . ($pageIdentifier ? "_{$pageIdentifier}" : '');
            Cache::forget($cacheKey);

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações SEO', [
                'error' => $e->getMessage(),
                'page_type' => $pageType,
                'page_identifier' => $pageIdentifier
            ]);

            return false;
        }
    }

    /**
     * Gerar meta tags HTML
     */
    public function generateMetaTags(array $seoData, string $currentUrl): string
    {
        $html = '';

        // Meta tags básicas
        if (!empty($seoData['meta_title'])) {
            $html .= '<title>' . e($seoData['meta_title']) . '</title>' . "\n";
        }

        if (!empty($seoData['meta_description'])) {
            $html .= '<meta name="description" content="' . e($seoData['meta_description']) . '">' . "\n";
        }

        if (!empty($seoData['meta_keywords'])) {
            $html .= '<meta name="keywords" content="' . e($seoData['meta_keywords']) . '">' . "\n";
        }

        // Robots
        if (!empty($seoData['robots'])) {
            $html .= '<meta name="robots" content="' . e($seoData['robots']) . '">' . "\n";
        }

        // Canonical URL
        $canonicalUrl = $seoData['canonical_url'] ?? $currentUrl;
        $html .= '<link rel="canonical" href="' . e($canonicalUrl) . '">' . "\n";

        // Open Graph
        $html .= '<meta property="og:url" content="' . e($currentUrl) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($seoData['og_type'] ?? 'website') . '">' . "\n";
        
        if (!empty($seoData['og_title'])) {
            $html .= '<meta property="og:title" content="' . e($seoData['og_title']) . '">' . "\n";
        }

        if (!empty($seoData['og_description'])) {
            $html .= '<meta property="og:description" content="' . e($seoData['og_description']) . '">' . "\n";
        }

        if (!empty($seoData['og_image'])) {
            $html .= '<meta property="og:image" content="' . e($seoData['og_image']) . '">' . "\n";
        }

        // Twitter Cards
        $html .= '<meta name="twitter:card" content="' . e($seoData['twitter_card'] ?? 'summary_large_image') . '">' . "\n";
        
        if (!empty($seoData['twitter_title'])) {
            $html .= '<meta name="twitter:title" content="' . e($seoData['twitter_title']) . '">' . "\n";
        }

        if (!empty($seoData['twitter_description'])) {
            $html .= '<meta name="twitter:description" content="' . e($seoData['twitter_description']) . '">' . "\n";
        }

        if (!empty($seoData['twitter_image'])) {
            $html .= '<meta name="twitter:image" content="' . e($seoData['twitter_image']) . '">' . "\n";
        }

        // Schema.org JSON-LD
        if (!empty($seoData['schema_markup'])) {
            $html .= '<script type="application/ld+json">' . "\n";
            $html .= $seoData['schema_markup'] . "\n";
            $html .= '</script>' . "\n";
        }

        // Tags personalizadas
        if (!empty($seoData['custom_head_tags'])) {
            $html .= $seoData['custom_head_tags'] . "\n";
        }

        return $html;
    }

    /**
     * Gerar Schema.org para diferentes tipos de página
     */
    public function generateSchemaMarkup(string $pageType, array $data): string
    {
        switch ($pageType) {
            case 'home':
                return $this->generateOrganizationSchema($data);
            
            case 'services':
                return $this->generateServiceSchema($data);
            
            case 'gallery':
                return $this->generateImageGallerySchema($data);
            
            case 'blog':
                return $this->generateArticleSchema($data);
            
            case 'contact':
                return $this->generateContactSchema($data);
            
            default:
                return $this->generateWebPageSchema($data);
        }
    }

    /**
     * Schema para organização (página inicial)
     */
    private function generateOrganizationSchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'AutoRepair',
            'name' => $data['name'] ?? 'Home Mechanic',
            'description' => $data['description'] ?? 'Oficina mecânica especializada em carros esportivos de luxo e tuning',
            'url' => $data['url'] ?? url('/'),
            'logo' => $data['logo'] ?? null,
            'image' => $data['image'] ?? null,
            'telephone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $data['address'] ?? null,
                'addressLocality' => $data['city'] ?? null,
                'addressRegion' => $data['state'] ?? null,
                'postalCode' => $data['zip'] ?? null,
                'addressCountry' => 'BR'
            ],
            'openingHours' => $data['opening_hours'] ?? [
                'Mo-Fr 08:00-18:00',
                'Sa 08:00-12:00'
            ],
            'priceRange' => '$$$',
            'paymentAccepted' => ['Cash', 'Credit Card', 'Debit Card', 'Bank Transfer'],
            'currenciesAccepted' => 'BRL'
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Schema para serviços
     */
    private function generateServiceSchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'provider' => [
                '@type' => 'AutoRepair',
                'name' => 'Home Mechanic'
            ],
            'areaServed' => 'Rio de Janeiro, RJ',
            'serviceType' => 'Automotive Repair'
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Schema para galeria de imagens
     */
    private function generateImageGallerySchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ImageGallery',
            'name' => $data['name'] ?? 'Galeria - Home Mechanic',
            'description' => $data['description'] ?? 'Galeria de trabalhos realizados pela Home Mechanic',
            'image' => $data['images'] ?? []
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Schema para artigos do blog
     */
    private function generateArticleSchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? 'George Marcelo (Marcelo Brad RJ)'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Home Mechanic',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $data['logo'] ?? null
                ]
            ],
            'datePublished' => $data['published_at'] ?? null,
            'dateModified' => $data['updated_at'] ?? null
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Schema para página de contato
     */
    private function generateContactSchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contato - Home Mechanic',
            'description' => 'Entre em contato com a Home Mechanic',
            'mainEntity' => [
                '@type' => 'AutoRepair',
                'name' => 'Home Mechanic'
            ]
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Schema genérico para página web
     */
    private function generateWebPageSchema(array $data): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'url' => $data['url'] ?? null
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obter configurações SEO padrão
     */
    private function getDefaultSeoSettings(string $pageType): array
    {
        $defaults = [
            'home' => [
                'meta_title' => 'Home Mechanic - Oficina Especializada em Carros Esportivos de Luxo e Tuning',
                'meta_description' => 'A Home Mechanic é especializada em manutenção e tuning de carros esportivos de luxo. Desenvolvido por George Marcelo (Marcelo Brad RJ).',
                'meta_keywords' => 'oficina mecânica, carros esportivos, tuning, luxo, manutenção automotiva, Rio de Janeiro',
                'og_type' => 'website'
            ],
            'services' => [
                'meta_title' => 'Serviços - Home Mechanic',
                'meta_description' => 'Conheça todos os serviços especializados da Home Mechanic para carros esportivos de luxo.',
                'meta_keywords' => 'serviços automotivos, manutenção, reparo, tuning, carros esportivos',
                'og_type' => 'website'
            ],
            'gallery' => [
                'meta_title' => 'Galeria - Home Mechanic',
                'meta_description' => 'Veja os trabalhos realizados pela Home Mechanic em carros esportivos de luxo.',
                'meta_keywords' => 'galeria, trabalhos, carros esportivos, antes e depois, tuning',
                'og_type' => 'website'
            ],
            'blog' => [
                'meta_title' => 'Blog - Home Mechanic',
                'meta_description' => 'Artigos e dicas sobre manutenção e tuning de carros esportivos de luxo.',
                'meta_keywords' => 'blog automotivo, dicas, manutenção, tuning, carros esportivos',
                'og_type' => 'blog'
            ],
            'contact' => [
                'meta_title' => 'Contato - Home Mechanic',
                'meta_description' => 'Entre em contato com a Home Mechanic. Estamos prontos para cuidar do seu carro esportivo.',
                'meta_keywords' => 'contato, orçamento, localização, telefone, email',
                'og_type' => 'website'
            ]
        ];

        return array_merge([
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_image' => null,
            'custom_head_tags' => null,
            'schema_markup' => null,
            'canonical_url' => null,
            'robots' => 'index, follow'
        ], $defaults[$pageType] ?? []);
    }

    /**
     * Gerar conteúdo robots
     */
    private function getRobotsContent(bool $index, bool $follow): string
    {
        $robots = [];
        
        $robots[] = $index ? 'index' : 'noindex';
        $robots[] = $follow ? 'follow' : 'nofollow';
        
        return implode(', ', $robots);
    }

    /**
     * Gerar hashtags para redes sociais
     */
    public function generateHashtags(string $pageType, array $data = []): array
    {
        $baseHashtags = [
            '#HomeMechanic',
            '#MarceloBradRJ',
            '#GeorgeMarcelo',
            '#OficinaEspecializada',
            '#CarrosEsportivos',
            '#Tuning',
            '#RioDeJaneiro'
        ];

        $specificHashtags = match ($pageType) {
            'services' => ['#Servicos', '#Manutencao', '#Reparo', '#Qualidade'],
            'gallery' => ['#Galeria', '#Trabalhos', '#AntesEDepois', '#Transformacao'],
            'blog' => ['#Blog', '#Dicas', '#Artigos', '#Conhecimento'],
            'contact' => ['#Contato', '#Orcamento', '#Atendimento'],
            default => ['#Especialistas', '#Confianca', '#Excelencia']
        };

        return array_merge($baseHashtags, $specificHashtags);
    }
}