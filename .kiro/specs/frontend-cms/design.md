# Design Document — Frontend CMS

## Overview

O Frontend CMS adiciona uma página de administração (`/admin/settings/frontend-cms`) que permite editar todo o conteúdo visível no frontend (textos, imagens, overlays, estatísticas, depoimentos, CTA e heroes de páginas internas) sem alterar código-fonte.

A solução reutiliza integralmente a infraestrutura existente:
- `App\Models\Setting` — persistência via `setMany()` / `group()` com cache de 10 min
- `App\Helpers\FileUploadHelper` — upload direto em `public/uploads/cms/`
- `App\Providers\FrontendSettingsProvider` — injeção de variáveis nas views frontend
- Padrão de controller/view do módulo `Settings` já existente

---

## Architecture

```
Browser (Admin)
    │  GET/POST /admin/settings/frontend-cms
    ▼
FrontendCmsController
    ├── index()        → lê Setting::group('cms') + merge defaults → view
    └── update()       → valida → handleImageUpload() → Setting::setMany()
                                                              │
                                                    Cache::forget('settings_all')

Browser (Frontend)
    │  GET /
    ▼
FrontendSettingsProvider (View Composer)
    ├── modules.frontend.*  → $siteSettings + $frontendContent
    └── layouts.frontend    → $siteSettings + $frontendContent
                                    │
                            Setting::group('cms')  ← cache 10 min
```

---

## Components and Interfaces

### FrontendCmsController
**Namespace:** `App\Modules\Settings\Controllers\FrontendCmsController`

```php
class FrontendCmsController extends Controller
{
    private array $defaults = [...];   // todos os defaults do grupo cms

    public function index(): View
    public function update(Request $request): RedirectResponse
    private function handleImageUpload(Request $request, string $key, string $subdir = 'uploads/cms'): ?string
    private function readCms(): array  // merge Setting::group('cms') + defaults
}
```

**`index()`** — carrega `readCms()`, passa `$settings` para a view com a aba ativa (`$activeTab` via query string `?tab=hero`).

**`update()`** — lê `$request->input('section')`, despacha para validação específica da seção, chama `handleImageUpload()` para cada campo de imagem, persiste via `Setting::setMany($data, 'cms')`, redireciona com `?tab={section}` e flash de sucesso/erro.

**`handleImageUpload()`** — se `$request->hasFile($key)`: valida extensão/tamanho, chama `FileUploadHelper::delete()` no valor anterior, chama `FileUploadHelper::save()`, retorna novo path. Caso contrário retorna `null` (mantém valor atual).

### Rotas (adicionadas em `app/Modules/Settings/Routes/web.php`)

```php
Route::get('/frontend-cms',  [FrontendCmsController::class, 'index'])->name('frontend-cms');
Route::post('/frontend-cms', [FrontendCmsController::class, 'update'])->name('frontend-cms.update');
```

### FrontendSettingsProvider (atualizado)

Adiciona um segundo bloco `View::composer` (ou expande os existentes) para compartilhar `$frontendContent` em `modules.frontend.*` e `layouts.frontend`:

```php
$frontendContent = Setting::group('cms');
$view->with('frontendContent', $frontendContent);
```

### View Admin — `resources/views/modules/settings/frontend-cms.blade.php`

Abas Bootstrap com `id` correspondendo ao valor de `section`:

| Aba | `section` | Campos principais |
|-----|-----------|-------------------|
| Hero & Estatísticas | `hero` | título, subtítulo, badge, bg_image, overlay, 3× stat |
| Sobre Nós | `about` | label, título, texto, imagem, badge, 4× feature |
| Depoimentos | `testimonials` | 3× name/car/text/initials |
| CTA | `cta` | título, subtítulo, bg_image, overlay |
| Páginas Internas | `pages` | services/gallery/blog/contact × title/subtitle/bg_image/overlay |

Cada aba tem:
- `<form method="POST" enctype="multipart/form-data">`
- `<input type="hidden" name="section" value="{section}">`
- Preview da imagem atual + input file + botão "Remover" (hidden input `{key}_remove=1`)
- Slider `<input type="range" min="0" max="100">` sincronizado com `<input type="number">` via JS

### Sidebar de Configurações (atualizada)

Adiciona item em `resources/views/modules/settings/_sidebar.blade.php`:

```html
<li class="nav-item">
    <a href="{{ route('admin.settings.frontend-cms') }}"
       class="nav-link {{ ($active ?? '') === 'frontend-cms' ? 'active' : '' }}">
        <i class="fas fa-paint-brush"></i> Conteúdo Frontend
    </a>
</li>
```

---

## Data Models

Nenhuma migration necessária. Todos os dados são armazenados na tabela `settings` existente com `group = 'cms'`.

### Chaves do grupo `cms`

**Hero:**
`hero_title_1`, `hero_title_2`, `hero_subtitle`, `hero_badge_text`, `hero_bg_image`, `hero_overlay`

**Estatísticas Hero:**
`hero_stat_1_num`, `hero_stat_1_label`, `hero_stat_2_num`, `hero_stat_2_label`, `hero_stat_3_num`, `hero_stat_3_label`

**About:**
`about_label`, `about_title`, `about_title_highlight`, `about_text`, `about_image`, `about_badge_num`, `about_badge_text`
`about_feature_1_icon`, `about_feature_1_title`, `about_feature_1_text` (repetido para 2, 3, 4)

**Depoimentos:**
`testimonial_1_name`, `testimonial_1_car`, `testimonial_1_text`, `testimonial_1_initials` (repetido para 2, 3)

**CTA:**
`cta_title`, `cta_title_highlight`, `cta_subtitle`, `cta_bg_image`, `cta_overlay`

**Páginas Internas** (para cada `{page}` em `services`, `gallery`, `blog`, `contact`):
`{page}_hero_title`, `{page}_hero_subtitle`, `{page}_hero_bg_image`, `{page}_hero_overlay`

### Defaults (definidos no controller)

```php
private array $defaults = [
    'hero_title_1'       => 'PERFORMANCE',
    'hero_title_2'       => 'SEM LIMITES',
    'hero_subtitle'      => 'Transformamos supercars em obras de arte mecânica...',
    'hero_badge_text'    => 'Especialistas em Supercars',
    'hero_bg_image'      => '',
    'hero_overlay'       => '70',
    'hero_stat_1_num'    => '15+',
    'hero_stat_1_label'  => 'Anos de Experiência',
    'hero_stat_2_num'    => '800+',
    'hero_stat_2_label'  => 'Projetos Realizados',
    'hero_stat_3_num'    => '98%',
    'hero_stat_3_label'  => 'Clientes Satisfeitos',
    'about_label'        => 'Sobre Nós',
    'about_title'        => 'A Oficina Que',
    'about_title_highlight' => 'Entende',
    'about_text'         => 'Fundada por apaixonados por automóveis de alto desempenho...',
    'about_image'        => '',
    'about_badge_num'    => '15',
    'about_badge_text'   => 'Anos de Excelência',
    'about_feature_1_icon'  => 'bi-award-fill',
    'about_feature_1_title' => 'Certificação Internacional',
    'about_feature_1_text'  => 'Técnicos certificados pelas principais marcas premium',
    'about_feature_2_icon'  => 'bi-cpu-fill',
    'about_feature_2_title' => 'Tecnologia de Ponta',
    'about_feature_2_text'  => 'Equipamentos de diagnóstico de última geração',
    'about_feature_3_icon'  => 'bi-shield-check-fill',
    'about_feature_3_title' => 'Garantia Total',
    'about_feature_3_text'  => 'Todos os serviços com garantia documentada',
    'about_feature_4_icon'  => 'bi-clock-fill',
    'about_feature_4_title' => 'Atendimento VIP',
    'about_feature_4_text'  => 'Serviço personalizado com acompanhamento em tempo real',
    'testimonial_1_name'     => 'Ricardo Almeida',
    'testimonial_1_car'      => 'Lamborghini Huracán',
    'testimonial_1_text'     => 'Serviço impecável...',
    'testimonial_1_initials' => 'RA',
    'testimonial_2_name'     => 'Fernanda Costa',
    'testimonial_2_car'      => 'Ferrari 488 GTB',
    'testimonial_2_text'     => 'A HomeMechanic é a única oficina que confio...',
    'testimonial_2_initials' => 'FC',
    'testimonial_3_name'     => 'Marcos Oliveira',
    'testimonial_3_car'      => 'Porsche 911 GT3',
    'testimonial_3_text'     => 'Fiz o tuning completo do meu GT3 aqui...',
    'testimonial_3_initials' => 'MO',
    'cta_title'           => 'Pronto Para',
    'cta_title_highlight' => 'Elevar',
    'cta_subtitle'        => 'Agende uma visita e descubra o que podemos fazer...',
    'cta_bg_image'        => '',
    'cta_overlay'         => '88',
    'services_hero_title'    => 'Nossos Serviços',
    'services_hero_subtitle' => 'Tuning, performance e manutenção para supercars',
    'services_hero_bg_image' => '',
    'services_hero_overlay'  => '70',
    'gallery_hero_title'     => 'Galeria de Projetos',
    'gallery_hero_subtitle'  => 'Conheça nossos trabalhos realizados',
    'gallery_hero_bg_image'  => '',
    'gallery_hero_overlay'   => '70',
    'blog_hero_title'        => 'Blog',
    'blog_hero_subtitle'     => 'Novidades, dicas e conteúdo sobre supercars',
    'blog_hero_bg_image'     => '',
    'blog_hero_overlay'      => '70',
    'contact_hero_title'     => 'Entre em Contato',
    'contact_hero_subtitle'  => 'Estamos prontos para atender você',
    'contact_hero_bg_image'  => '',
    'contact_hero_overlay'   => '70',
];
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Round trip de persistência CMS

*Para qualquer* conjunto de valores de texto válidos para as chaves do grupo `cms`, salvar via `Setting::setMany($data, 'cms')` e depois ler via `Setting::group('cms')` deve retornar exatamente os mesmos valores.

**Validates: Requirements 1.4, 2.2, 3.4, 4.2, 5.3, 6.3**

### Property 2: Merge de defaults cobre todas as chaves

*Para qualquer* subconjunto de chaves do grupo `cms` presentes no banco, o método `readCms()` do controller deve retornar um array que contém **todas** as chaves definidas em `$defaults`, sem nenhuma chave com valor `null`.

**Validates: Requirements 1.2, 2.3, 3.5, 4.3, 5.4, 6.4**

### Property 3: Rejeição de overlay fora do intervalo

*Para qualquer* valor numérico fora do intervalo [0, 100] enviado nos campos `hero_overlay` ou `cta_overlay`, o controller deve retornar erro de validação e o valor salvo no banco não deve ser alterado.

**Validates: Requirements 1.7, 5.5**

### Property 4: Rejeição de arquivos inválidos

*Para qualquer* arquivo com extensão diferente de `jpg`, `jpeg`, `png`, `webp`, `gif` ou com tamanho superior a 5 MB, o controller deve rejeitar o upload e manter o valor de imagem anterior inalterado no banco.

**Validates: Requirements 8.1, 8.3**

---

## Error Handling

| Situação | Comportamento |
|----------|---------------|
| Validação falha (overlay, campos obrigatórios) | `back()->withErrors()->withInput()` |
| Upload com formato/tamanho inválido | Erro de validação, valor anterior mantido |
| Exceção ao salvar no banco | `Log::error()` + `back()->with('error', ...)` |
| Chave ausente em `$frontendContent` na view | Operador `??` com valor padrão inline |
| `FileUploadHelper::delete()` em arquivo inexistente | Silencioso (`@unlink` já trata) |

---

## Testing Strategy

### Testes de Unidade / Feature (PHPUnit)

- **FrontendCmsControllerTest**
  - `test_index_loads_cms_settings_with_defaults` — verifica merge de defaults
  - `test_update_hero_persists_all_fields` — round trip de persistência
  - `test_overlay_validation_rejects_out_of_range` — valores -1, 101, 200
  - `test_image_upload_stores_path_and_deletes_old` — mock FileUploadHelper
  - `test_invalid_image_format_rejected` — extensão `.exe`, `.pdf`
  - `test_image_over_5mb_rejected`
  - `test_remove_image_flag_clears_value`

- **FrontendSettingsProviderTest**
  - `test_frontend_content_shared_with_frontend_views` — view composer injeta `$frontendContent`

### Testes de Propriedade (PBT com [eris](https://github.com/giorgiosironi/eris) ou similar para PHP)

Cada property acima deve ser implementada como um teste de propriedade com mínimo de 100 iterações, gerando:
- **Property 1**: arrays aleatórios de strings para as chaves cms
- **Property 2**: subconjuntos aleatórios das chaves (0 a N chaves presentes)
- **Property 3**: inteiros aleatórios fora de [0, 100]
- **Property 4**: arquivos com extensões e tamanhos aleatórios

Tag format: `Feature: frontend-cms, Property {N}: {texto}`
