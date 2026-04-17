# Correções do Instalador - Erro "No database selected"

## Problema Identificado

O erro `SQLSTATE[3D000]: Invalid catalog name: 1046 No database selected (Connection: mysql, SQL: USE ``)` ocorria porque:

1. O **Controller** estava passando `$dbConfig` diretamente para `$installData['database']`
2. O `$dbConfig` tinha a chave `'database'` (para o método `testDatabaseConnection`)
3. Mas o **Service** esperava a chave `'name'` em `$data['database']['name']`
4. Resultado: `$data['database']['name']` estava vazio, gerando `USE ``` (banco vazio)

## Correções Aplicadas

### 1. InstallerController.php
**Linha ~95-120**: Corrigido mapeamento de dados

**ANTES:**
```php
$dbConfig = [
    'host' => $request->input('db_host'),
    'port' => $request->input('db_port', 3306),
    'database' => $request->input('db_name'),  // ← Chave 'database'
    'username' => $request->input('db_user'),
    'password' => $request->input('db_password', '')
];

$installData = [
    'database' => $dbConfig,  // ← Passava 'database' mas Service espera 'name'
    ...
];
```

**DEPOIS:**
```php
$dbConfig = [
    'host' => $request->input('db_host'),
    'port' => $request->input('db_port', 3306),
    'database' => $request->input('db_name'),  // Para testDatabaseConnection
    'username' => $request->input('db_user'),
    'password' => $request->input('db_password', '')
];

$installData = [
    'database' => [
        'host' => $request->input('db_host'),
        'port' => $request->input('db_port', 3306),
        'name' => $request->input('db_name'),  // ← Chave 'name' para o Service
        'username' => $request->input('db_user'),
        'password' => $request->input('db_password', '')
    ],
    ...
];
```

### 2. Logs Detalhados Adicionados
Adicionado log no Controller antes de chamar o Service:
```php
Log::info('Dados preparados para instalação', [
    'database_name' => $installData['database']['name'],
    'database_host' => $installData['database']['host'],
    'database_port' => $installData['database']['port'],
    'database_username' => $installData['database']['username'],
    'admin_name' => $installData['admin']['name'],
    'admin_email' => $installData['admin']['email'],
    'company_name' => $installData['company']['name'],
    'system_url' => $installData['system']['url']
]);
```

### 3. Script de Debug Criado
**Arquivo:** `public/debug-install.php`

Permite verificar exatamente quais dados o JavaScript está enviando:
- Campos presentes vs. campos faltando
- Valores enviados (senhas ocultas)
- Método HTTP e Content-Type

### 4. JavaScript Corrigido
**Arquivo:** `public/js/installer-steps.js`

Corrigido problema de FormData sendo consumido:
- Criado FormData separado para debug
- FormData original preservado para instalação
- Evita que dados sejam perdidos entre requisições

## Fluxo de Dados Correto

```
JavaScript (installer-steps.js)
    ↓ envia: db_name, db_user, db_password, etc.
    
InstallRequest (validação)
    ↓ valida: db_name, db_user, db_password
    
InstallerController
    ↓ monta: ['database' => ['name' => db_name, ...]]
    
InstallerService
    ✓ recebe: $data['database']['name'] (correto!)
```

## Como Testar

1. Limpar cache do navegador (Ctrl+Shift+Delete)
2. Acessar `/install/steps`
3. Preencher todos os dados
4. Abrir Console do navegador (F12)
5. Clicar em "Iniciar Instalação"
6. Verificar logs no console:
   - "DEBUG - Dados sendo enviados" deve mostrar todos os campos
   - Não deve haver campos faltando

## Logs para Verificar

### No Laravel (storage/logs/laravel.log):
```
[INFO] Dados preparados para instalação
[INFO] Iniciando instalação do HomeMechanic
[INFO] Etapa 1: Preparando dados de instalação
[INFO] Dados preparados (database_name deve estar preenchido)
[INFO] Etapa 5.1: Reconectando ao banco de dados
[INFO] Configuração do banco atualizada
[INFO] Banco selecionado via USE statement
```

### No Console do Navegador:
```
DEBUG - Dados sendo enviados: {
  "success": true,
  "present_fields": {
    "db_name": "homemechanic_db",
    "db_host": "localhost",
    ...
  },
  "missing_fields": []
}
```

## Arquivos Modificados

1. ✅ `app/Modules/Installer/Controllers/InstallerController.php`
2. ✅ `public/js/installer-steps.js`
3. ✅ `public/debug-install.php` (novo)
4. ✅ `CORRECOES_INSTALADOR.md` (este arquivo)

## Próximos Passos

Se o erro persistir:
1. Verificar logs do Laravel em `storage/logs/laravel.log`
2. Verificar console do navegador para erros JavaScript
3. Verificar resposta do `debug-install.php` para confirmar dados enviados
4. Verificar se o banco de dados existe e o usuário tem permissões

## Collation Configurada

Todo o sistema está configurado para usar:
- **Charset:** `utf8mb4`
- **Collation:** `utf8mb4_unicode_ci`

Arquivos configurados:
- `.env.installer`
- `config/database.php`
- Migrations (via collation padrão)
