# 🛠️ Guia do Desenvolvedor - HomeMechanic System

Este guia é destinado a desenvolvedores que desejam contribuir, personalizar ou estender o HomeMechanic System.

## 📋 Índice

1. [Arquitetura do Sistema](#arquitetura-do-sistema)
2. [Estrutura de Módulos](#estrutura-de-módulos)
3. [Padrões de Código](#padrões-de-código)
4. [Desenvolvimento Local](#desenvolvimento-local)
5. [Criando Novos Módulos](#criando-novos-módulos)
6. [Sistema de Testes](#sistema-de-testes)
7. [API Interna](#api-interna)
8. [Hooks e Eventos](#hooks-e-eventos)
9. [Contribuição](#contribuição)

## 🏗️ Arquitetura do Sistema

### Visão Geral
O HomeMechanic System utiliza uma arquitetura modular baseada no Laravel 11, onde cada funcionalidade é encapsulada em módulos independentes.

```
HomeMechanic System
├── Core Laravel Framework
├── Module Service Provider (Descoberta automática)
├── Módulos Independentes
├── Shared Services
├── Security Middleware Stack
└── Frontend/Admin Layouts
```

### Princípios Arquiteturais

#### 1. Modularidade
- Cada funcionalidade é um módulo independente
- Módulos podem ser desenvolvidos separadamente
- Fácil manutenção e extensibilidade

#### 2. Separação de Responsabilidades
- **Controllers**: Orquestração de requests
- **Services**: Lógica de negócio
- **Models**: Acesso a dados
- **Requests**: Validação de entrada
- **Resources**: Serialização de saída

#### 3. Segurança por Design
- Middleware de segurança em todas as camadas
- Sanitização automática de entradas
- Validação rigorosa de dados
- Audit log completo

### Stack Tecnológico

#### Backend
```php
// Versões utilizadas
PHP: 8.4+
Laravel: 11.x
MySQL: 8.0+ / MariaDB: 10.6+
```

#### Frontend
```javascript
// Bibliotecas principais
AdminLTE: 4.x
Bootstrap: 5.x
jQuery: 3.7.x
Dropzone.js: 6.x
SweetAlert2: 11.x
Toastify: 1.x
Swiper: 10.x
```

#### Ferramentas de Build
```json
{
  "vite": "^5.0",
  "sass": "^1.69",
  "autoprefixer": "^10.4"
}
```

## 📁 Estrutura de Módulos

### Anatomia de um Módulo

```
app/Modules/ExemploModulo/
├── Controllers/
│   ├── ExemploController.php
│   └── Api/
│       └── ExemploApiController.php
├── Models/
│   └── Exemplo.php
├── Requests/
│   ├── StoreExemploRequest.php
│   └── UpdateExemploRequest.php
├── Resources/
│   └── ExemploResource.php
├── Services/
│   └── ExemploService.php
└── Routes/
    ├── web.php
    └── api.php
```

### Estrutura de Views

```
resources/views/modules/exemplo/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
└── partials/
    ├── _form.blade.php
    └── _table.blade.php
```

### Estrutura de Assets

```
resources/modules/exemplo/
├── css/
│   └── exemplo.scss
├── js/
│   ├── exemplo.js
│   └── components/
│       └── exemplo-component.js
└── images/
    └── exemplo-icon.svg
```

## 📝 Padrões de Código

### Convenções de Nomenclatura

#### Classes PHP
```php
// Controllers
class ServiceController extends Controller

// Models
class Service extends Model

// Requests
class StoreServiceRequest extends FormRequest

// Resources
class ServiceResource extends JsonResource

// Services
class ServiceManagementService
```

#### Métodos e Variáveis
```php
// Métodos - camelCase
public function createService()
public function updateServiceStatus()

// Variáveis - camelCase
$serviceData = [];
$isActiveService = true;

// Constantes - UPPER_SNAKE_CASE
const MAX_UPLOAD_SIZE = 10485760;
const DEFAULT_STATUS = 'active';
```

#### Banco de Dados
```php
// Tabelas - snake_case plural
services
gallery_photos
blog_categories

// Colunas - snake_case
created_at
updated_at
is_active
sort_order
```

### Estrutura de Controllers

```php
<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Services\Models\Service;
use App\Modules\Services\Requests\StoreServiceRequest;
use App\Modules\Services\Requests\UpdateServiceRequest;
use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Services\Services\ServiceManagementService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private ServiceManagementService $serviceService
    ) {
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Modules\Services\Models\Service')->only(['index']);
        $this->middleware('can:create,App\Modules\Services\Models\Service')->only(['create', 'store']);
        $this->middleware('can:update,service')->only(['edit', 'update']);
        $this->middleware('can:delete,service')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $services = $this->serviceService->getPaginatedServices($request);
        
        if ($request->expectsJson()) {
            return ServiceResource::collection($services);
        }
        
        return view('modules.services.index', compact('services'));
    }

    public function store(StoreServiceRequest $request)
    {
        $service = $this->serviceService->createService($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Serviço criado com sucesso!',
            'data' => new ServiceResource($service)
        ], 201);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $updatedService = $this->serviceService->updateService($service, $request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Serviço atualizado com sucesso!',
            'data' => new ServiceResource($updatedService)
        ]);
    }

    public function destroy(Service $service)
    {
        $this->serviceService->deleteService($service);
        
        return response()->json([
            'success' => true,
            'message' => 'Serviço excluído com sucesso!'
        ]);
    }
}
```

### Estrutura de Services

```php
<?php

namespace App\Modules\Services\Services;

use App\Models\AuditLog;
use App\Modules\Services\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceManagementService
{
    public function getPaginatedServices(Request $request)
    {
        $query = Service::query();
        
        // Busca
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }
        
        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }
        
        // Ordenação
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
        return $query->paginate($request->get('per_page', 15));
    }

    public function createService(array $data): Service
    {
        // Gerar slug se não fornecido
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }
        
        // Sanitizar dados
        $data = $this->sanitizeServiceData($data);
        
        $service = Service::create($data);
        
        // Registrar no audit log
        AuditLog::record('created', $service, [], $data);
        
        return $service;
    }

    public function updateService(Service $service, array $data): Service
    {
        $oldData = $service->toArray();
        
        // Gerar novo slug se título mudou
        if (isset($data['title']) && $data['title'] !== $service->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $service->id);
        }
        
        // Sanitizar dados
        $data = $this->sanitizeServiceData($data);
        
        $service->update($data);
        
        // Registrar no audit log
        AuditLog::record('updated', $service, $oldData, $service->toArray());
        
        return $service->fresh();
    }

    public function deleteService(Service $service): void
    {
        $oldData = $service->toArray();
        
        $service->delete();
        
        // Registrar no audit log
        AuditLog::record('deleted', $service, $oldData, []);
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Service::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    private function sanitizeServiceData(array $data): array
    {
        // Sanitizar campos de texto
        $textFields = ['title', 'description', 'content'];
        
        foreach ($textFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = strip_tags($data[$field], '<p><br><strong><em><ul><ol><li><a>');
                $data[$field] = trim($data[$field]);
            }
        }
        
        return $data;
    }
}
```

### Estrutura de Models

```php
<?php

namespace App\Modules\Services\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'icon',
        'cover_image',
        'featured',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Accessors
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }
        
        return asset('storage/uploads/' . $this->cover_image);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->description), 150);
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = trim(strip_tags($value));
    }
}
```

## 🚀 Desenvolvimento Local

### Configuração do Ambiente

#### 1. Clonagem e Dependências
```bash
# Clonar repositório
git clone https://github.com/homemechanic/system.git
cd homemechanic

# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
```

#### 2. Banco de Dados Local
```bash
# Criar banco MySQL
mysql -u root -p
CREATE DATABASE homemechanic_dev;
EXIT;

# Configurar .env
DB_DATABASE=homemechanic_dev
DB_USERNAME=root
DB_PASSWORD=sua_senha

# Executar migrations
php artisan migrate --seed
```

#### 3. Servidor de Desenvolvimento
```bash
# Servidor PHP
php artisan serve

# Compilação de assets (modo watch)
npm run dev

# Em outro terminal, para hot reload
npm run hot
```

### Ferramentas de Desenvolvimento

#### Debug e Profiling
```php
// Usar Laravel Debugbar (desenvolvimento)
composer require barryvdh/laravel-debugbar --dev

// Usar Telescope para profiling
composer require laravel/telescope --dev
php artisan telescope:install
```

#### Testes Automatizados
```bash
# Executar todos os testes
php artisan test

# Testes com cobertura
php artisan test --coverage

# Testes específicos
php artisan test --filter ServiceTest
```

#### Code Quality
```bash
# PHP CS Fixer
composer require friendsofphp/php-cs-fixer --dev
./vendor/bin/php-cs-fixer fix

# PHPStan
composer require phpstan/phpstan --dev
./vendor/bin/phpstan analyse
```

## 🔧 Criando Novos Módulos

### Comando Artisan Personalizado

```php
// app/Console/Commands/MakeModuleCommand.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Criar um novo módulo completo';

    public function handle()
    {
        $name = $this->argument('name');
        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        
        $this->createModuleStructure($studlyName, $kebabName);
        $this->info("Módulo {$studlyName} criado com sucesso!");
    }

    private function createModuleStructure($studlyName, $kebabName)
    {
        // Criar diretórios
        $basePath = app_path("Modules/{$studlyName}");
        $directories = ['Controllers', 'Models', 'Requests', 'Resources', 'Services', 'Routes'];
        
        foreach ($directories as $dir) {
            mkdir("{$basePath}/{$dir}", 0755, true);
        }
        
        // Criar arquivos base
        $this->createController($studlyName);
        $this->createModel($studlyName);
        $this->createRoutes($studlyName, $kebabName);
        $this->createViews($studlyName, $kebabName);
    }
}
```

### Template de Controller

```php
// Usar para gerar controllers automaticamente
<?php

namespace App\Modules\{{MODULE}}\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\{{MODULE}}\Models\{{MODEL}};
use App\Modules\{{MODULE}}\Requests\Store{{MODEL}}Request;
use App\Modules\{{MODULE}}\Requests\Update{{MODEL}}Request;
use App\Modules\{{MODULE}}\Resources\{{MODEL}}Resource;
use App\Modules\{{MODULE}}\Services\{{MODEL}}Service;
use Illuminate\Http\Request;

class {{MODEL}}Controller extends Controller
{
    public function __construct(
        private {{MODEL}}Service ${{VARIABLE}}Service
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        ${{PLURAL}} = $this->{{VARIABLE}}Service->getPaginated{{PLURAL}}($request);
        
        if ($request->expectsJson()) {
            return {{MODEL}}Resource::collection(${{PLURAL}});
        }
        
        return view('modules.{{KEBAB}}.index', compact('{{PLURAL}}'));
    }

    // ... outros métodos
}
```

## 🧪 Sistema de Testes

### Testes Unitários

```php
<?php

namespace Tests\Unit\Modules\Services;

use App\Modules\Services\Models\Service;
use App\Modules\Services\Services\ServiceManagementService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    private ServiceManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ServiceManagementService();
    }

    public function test_can_create_service_with_auto_slug()
    {
        $data = [
            'title' => 'Alinhamento e Balanceamento',
            'description' => 'Serviço completo de alinhamento',
            'active' => true,
        ];

        $service = $this->service->createService($data);

        $this->assertEquals('alinhamento-e-balanceamento', $service->slug);
        $this->assertEquals($data['title'], $service->title);
    }

    public function test_generates_unique_slug_for_duplicate_titles()
    {
        // Criar primeiro serviço
        Service::create([
            'title' => 'Teste',
            'slug' => 'teste',
            'description' => 'Primeiro',
        ]);

        // Criar segundo com mesmo título
        $service = $this->service->createService([
            'title' => 'Teste',
            'description' => 'Segundo',
        ]);

        $this->assertEquals('teste-2', $service->slug);
    }
}
```

### Testes de Feature

```php
<?php

namespace Tests\Feature\Modules\Services;

use App\Models\User;
use App\Modules\Services\Models\Service;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_list_services()
    {
        Service::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get('/admin/services');

        $response->assertStatus(200);
        $response->assertViewIs('modules.services.index');
        $response->assertViewHas('services');
    }

    public function test_can_create_service_via_ajax()
    {
        $data = [
            'title' => 'Novo Serviço',
            'description' => 'Descrição do serviço',
            'active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/admin/services', $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('services', [
            'title' => 'Novo Serviço',
            'slug' => 'novo-servico',
        ]);
    }
}
```

### Testes de Propriedades (Property-Based Testing)

```php
<?php

namespace Tests\Properties;

use Eris\Generator;
use Eris\TestTrait;
use App\Modules\Services\Services\ServiceManagementService;
use Tests\TestCase;

class ServiceSlugPropertyTest extends TestCase
{
    use TestTrait;

    /**
     * @test
     * Propriedade: Slugs de Serviços São Únicos Para Qualquer Sequência de Títulos
     */
    public function slugs_are_always_unique_for_any_title_sequence()
    {
        $this->forAll(
            Generator\seq(Generator\string())
                ->withSize(10)
        )
        ->then(function ($titles) {
            $service = new ServiceManagementService();
            $slugs = [];
            
            foreach ($titles as $title) {
                if (empty(trim($title))) continue;
                
                $slug = $service->generateUniqueSlug($title);
                $slugs[] = $slug;
            }
            
            // Verificar que todos os slugs são únicos
            $uniqueSlugs = array_unique($slugs);
            $this->assertEquals(count($slugs), count($uniqueSlugs));
            
            // Verificar que todos os slugs são URL-safe
            foreach ($slugs as $slug) {
                $this->assertMatchesRegularExpression('/^[a-z0-9\-]+$/', $slug);
            }
        });
    }
}
```

## 🔌 API Interna

### Estrutura de Resposta Padrão

```php
// Sucesso
{
    "success": true,
    "message": "Operação realizada com sucesso",
    "data": {
        // dados do recurso
    },
    "meta": {
        "pagination": {
            "current_page": 1,
            "total": 100,
            "per_page": 15
        }
    }
}

// Erro de Validação
{
    "success": false,
    "message": "Dados inválidos",
    "errors": {
        "title": ["O campo título é obrigatório"],
        "email": ["O email deve ser válido"]
    }
}

// Erro Geral
{
    "success": false,
    "message": "Erro interno do servidor",
    "error_code": "INTERNAL_ERROR"
}
```

### Middleware de API

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Padronizar respostas JSON
        if ($request->expectsJson() && $response->getStatusCode() >= 400) {
            $content = json_decode($response->getContent(), true);
            
            $standardResponse = [
                'success' => false,
                'message' => $content['message'] ?? 'Erro na requisição',
            ];
            
            if (isset($content['errors'])) {
                $standardResponse['errors'] = $content['errors'];
            }
            
            $response->setContent(json_encode($standardResponse));
        }
        
        return $response;
    }
}
```

## 🎣 Hooks e Eventos

### Sistema de Eventos

```php
<?php

namespace App\Modules\Services\Events;

use App\Modules\Services\Models\Service;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Service $service
    ) {}
}
```

### Listeners

```php
<?php

namespace App\Modules\Services\Listeners;

use App\Modules\Services\Events\ServiceCreated;
use App\Models\AuditLog;

class LogServiceCreation
{
    public function handle(ServiceCreated $event): void
    {
        AuditLog::record(
            'service_created',
            $event->service,
            [],
            $event->service->toArray()
        );
    }
}
```

### Observers

```php
<?php

namespace App\Modules\Services\Observers;

use App\Modules\Services\Models\Service;
use Illuminate\Support\Str;

class ServiceObserver
{
    public function creating(Service $service): void
    {
        if (empty($service->slug)) {
            $service->slug = Str::slug($service->title);
        }
    }

    public function updating(Service $service): void
    {
        if ($service->isDirty('title')) {
            $service->slug = Str::slug($service->title);
        }
    }
}
```

## 🤝 Contribuição

### Fluxo de Contribuição

1. **Fork** do repositório
2. **Clone** do seu fork
3. **Branch** para sua feature (`git checkout -b feature/nova-funcionalidade`)
4. **Commit** das mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
5. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
6. **Pull Request** no repositório original

### Padrões de Commit

```bash
# Tipos de commit
feat: nova funcionalidade
fix: correção de bug
docs: documentação
style: formatação de código
refactor: refatoração
test: adição de testes
chore: tarefas de manutenção

# Exemplos
feat(services): adiciona validação de slug único
fix(upload): corrige validação de tipo MIME
docs(api): atualiza documentação da API
```

### Code Review

#### Checklist para PRs
- [ ] Código segue os padrões estabelecidos
- [ ] Testes unitários incluídos
- [ ] Documentação atualizada
- [ ] Sem quebras de compatibilidade
- [ ] Performance considerada
- [ ] Segurança verificada

### Configuração de Hooks Git

```bash
# .git/hooks/pre-commit
#!/bin/sh
# Executar testes antes do commit
php artisan test
if [ $? -ne 0 ]; then
    echo "Testes falharam. Commit cancelado."
    exit 1
fi

# Verificar padrões de código
./vendor/bin/php-cs-fixer fix --dry-run
if [ $? -ne 0 ]; then
    echo "Padrões de código não seguidos. Execute php-cs-fixer fix"
    exit 1
fi
```

---

**Última atualização**: 15 de Abril de 2026  
**Versão**: 1.0.0

> 🚀 **Contribua!** O HomeMechanic System é um projeto open source e sua contribuição é muito bem-vinda!