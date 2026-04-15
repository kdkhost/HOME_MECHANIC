# 🔧 Troubleshooting - Solução de Problemas

Guia completo para diagnosticar e resolver problemas técnicos no HomeMechanic System.

## 📋 Índice

- [Problemas de Instalação](#problemas-de-instalação)
- [Erros de Servidor](#erros-de-servidor)
- [Problemas de Banco de Dados](#problemas-de-banco-de-dados)
- [Problemas de Upload](#problemas-de-upload)
- [Problemas de Email](#problemas-de-email)
- [Problemas de Performance](#problemas-de-performance)
- [Problemas de Interface](#problemas-de-interface)
- [Ferramentas de Diagnóstico](#ferramentas-de-diagnóstico)

## 🚀 Problemas de Instalação

### Erro: "Extensão PHP não encontrada"

**Sintomas:**
- Instalador mostra extensões em vermelho
- Erro durante verificação de requisitos

**Soluções:**

#### Ubuntu/Debian:
```bash
# Instalar extensões PHP 8.4
sudo apt update
sudo apt install php8.4-mysql php8.4-mbstring php8.4-xml php8.4-gd php8.4-curl php8.4-zip

# Reiniciar Apache
sudo systemctl restart apache2
```

#### CentOS/RHEL:
```bash
# Instalar extensões PHP 8.4
sudo yum install php84-php-mysql php84-php-mbstring php84-php-xml php84-php-gd

# Reiniciar Apache
sudo systemctl restart httpd
```

#### Windows (XAMPP):
1. Edite `php.ini`
2. Descomente as linhas das extensões:
```ini
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=gd
```
3. Reinicie o Apache

### Erro: "Permissões de diretório"

**Sintomas:**
- Erro ao criar arquivos
- Instalação falha na etapa final

**Soluções:**

#### Linux/macOS:
```bash
# Definir permissões corretas
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# Se necessário, alterar proprietário
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
```

#### Windows:
1. Clique com botão direito nas pastas `storage` e `bootstrap/cache`
2. Propriedades → Segurança
3. Dar controle total ao usuário do IIS/Apache

### Erro: "mod_rewrite não habilitado"

**Sintomas:**
- URLs não funcionam
- Erro 404 em todas as páginas

**Soluções:**

#### Ubuntu/Debian:
```bash
# Habilitar mod_rewrite
sudo a2enmod rewrite

# Reiniciar Apache
sudo systemctl restart apache2

# Verificar se está habilitado
apache2ctl -M | grep rewrite
```

#### CentOS/RHEL:
```bash
# Editar httpd.conf
sudo nano /etc/httpd/conf/httpd.conf

# Descomentar linha:
LoadModule rewrite_module modules/mod_rewrite.so

# Reiniciar Apache
sudo systemctl restart httpd
```

## 🚨 Erros de Servidor

### Erro 500 - Internal Server Error

**Diagnóstico:**
```bash
# Verificar logs do Apache
tail -f /var/log/apache2/error.log

# Verificar logs do Laravel
tail -f storage/logs/laravel.log

# Verificar logs do PHP
tail -f /var/log/php_errors.log
```

**Soluções Comuns:**

#### 1. Problema de Permissões:
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

#### 2. Arquivo .env Corrompido:
```bash
# Copiar exemplo
cp .env.example .env

# Gerar nova chave
php artisan key:generate
```

#### 3. Cache Corrompido:
```bash
# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Composer Autoload:
```bash
composer dump-autoload
```

### Erro 404 - Página não encontrada

**Diagnóstico:**
1. Verificar se `.htaccess` existe em `public/`
2. Testar mod_rewrite
3. Verificar configuração do servidor

**Soluções:**

#### Apache - Criar/Verificar .htaccess:
```apache
# public/.htaccess
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx - Configuração:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Erro 419 - CSRF Token Mismatch

**Sintomas:**
- Formulários não funcionam
- Erro ao fazer login
- Requisições AJAX falham

**Soluções:**

#### 1. Verificar Meta Tag:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### 2. Verificar Formulários:
```html
<form method="POST">
    @csrf
    <!-- campos do formulário -->
</form>
```

#### 3. Verificar AJAX:
```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

#### 4. Limpar Sessões:
```bash
# Limpar sessões
php artisan session:table
php artisan migrate
```

## 🗄️ Problemas de Banco de Dados

### Erro: "Connection refused"

**Diagnóstico:**
```bash
# Testar conexão MySQL
mysql -h localhost -u usuario -p

# Verificar se MySQL está rodando
sudo systemctl status mysql

# Verificar porta
netstat -tlnp | grep :3306
```

**Soluções:**

#### 1. Iniciar MySQL:
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

#### 2. Verificar Configurações .env:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homemechanic
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

#### 3. Criar Banco e Usuário:
```sql
CREATE DATABASE homemechanic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'homemechanic_user'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON homemechanic.* TO 'homemechanic_user'@'localhost';
FLUSH PRIVILEGES;
```

### Erro: "Access denied for user"

**Soluções:**

#### 1. Verificar Credenciais:
```bash
# Testar login
mysql -u homemechanic_user -p homemechanic
```

#### 2. Resetar Senha MySQL:
```bash
# Parar MySQL
sudo systemctl stop mysql

# Iniciar em modo seguro
sudo mysqld_safe --skip-grant-tables &

# Conectar sem senha
mysql -u root

# Alterar senha
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nova_senha';
FLUSH PRIVILEGES;
```

### Erro: "Table doesn't exist"

**Soluções:**

#### 1. Executar Migrations:
```bash
php artisan migrate
```

#### 2. Verificar Status:
```bash
php artisan migrate:status
```

#### 3. Resetar Migrations (CUIDADO - apaga dados):
```bash
php artisan migrate:fresh --seed
```

## 📁 Problemas de Upload

### Upload Falha Silenciosamente

**Diagnóstico:**
```bash
# Verificar configurações PHP
php -i | grep -E "(upload_max_filesize|post_max_size|max_execution_time)"

# Verificar logs
tail -f storage/logs/laravel.log
```

**Soluções:**

#### 1. Aumentar Limites PHP:
```ini
# php.ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 512M
```

#### 2. Verificar Permissões:
```bash
chmod -R 755 storage/app/public/uploads/
```

#### 3. Criar Link Simbólico:
```bash
php artisan storage:link
```

### Erro: "File type not allowed"

**Diagnóstico:**
- Verificar extensão do arquivo
- Verificar validação MIME

**Soluções:**

#### 1. Verificar Tipos Permitidos:
```php
// app/Modules/Upload/Services/MimeValidatorService.php
$allowedTypes = [
    'image/jpeg',
    'image/png', 
    'image/webp',
    'image/gif',
    'video/mp4',
    'video/webm'
];
```

#### 2. Verificar Tamanho:
- Imagens: máximo 10MB
- Vídeos: máximo 100MB

## 📧 Problemas de Email

### Emails Não São Enviados

**Diagnóstico:**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Testar configuração SMTP
php artisan tinker
>>> Mail::raw('Teste', function($msg) { $msg->to('teste@email.com')->subject('Teste'); });
```

**Soluções:**

#### 1. Configurar SMTP Corretamente:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@gmail.com
MAIL_FROM_NAME="HomeMechanic"
```

#### 2. Gmail - Usar Senha de App:
1. Ativar autenticação de 2 fatores
2. Gerar senha de app
3. Usar senha de app no lugar da senha normal

#### 3. Verificar Firewall:
```bash
# Testar conectividade SMTP
telnet smtp.gmail.com 587
```

### Erro: "Connection timed out"

**Soluções:**

#### 1. Verificar Porta:
- TLS: porta 587
- SSL: porta 465

#### 2. Verificar Firewall:
```bash
# Liberar portas no firewall
sudo ufw allow 587
sudo ufw allow 465
```

#### 3. Provedor Alternativo:
- Usar SendGrid, Mailgun ou similar
- Configurar como SMTP externo

## ⚡ Problemas de Performance

### Site Lento

**Diagnóstico:**
```bash
# Verificar uso de recursos
top
htop

# Verificar logs de acesso
tail -f /var/log/apache2/access.log
```

**Soluções:**

#### 1. Otimizar Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

#### 2. Otimizar Banco:
```sql
-- Verificar queries lentas
SHOW PROCESSLIST;

-- Otimizar tabelas
OPTIMIZE TABLE services, posts, gallery_photos;

-- Adicionar índices se necessário
CREATE INDEX idx_services_active ON services(active);
CREATE INDEX idx_posts_status ON posts(status);
```

#### 3. Otimizar Imagens:
- Comprimir imagens antes do upload
- Usar WebP quando possível
- Implementar lazy loading

### Alto Uso de Memória

**Diagnóstico:**
```bash
# Verificar uso de memória PHP
php -i | grep memory_limit

# Monitorar processos
ps aux | grep php
```

**Soluções:**

#### 1. Aumentar Limite PHP:
```ini
memory_limit = 512M
```

#### 2. Otimizar Queries:
```php
// Usar paginação
$posts = Post::paginate(15);

// Eager loading
$posts = Post::with('category', 'tags')->get();
```

## 🖥️ Problemas de Interface

### Layout Quebrado

**Sintomas:**
- CSS não carrega
- JavaScript não funciona
- Layout desorganizado

**Soluções:**

#### 1. Compilar Assets:
```bash
npm install
npm run build
```

#### 2. Verificar Vite:
```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

#### 3. Limpar Cache do Navegador:
- Ctrl+F5 (Windows)
- Cmd+Shift+R (Mac)

### AdminLTE Não Carrega

**Soluções:**

#### 1. Verificar CDN:
- Testar conectividade com CDNs
- Usar versões locais se necessário

#### 2. Verificar Ordem de Scripts:
```html
<!-- jQuery primeiro -->
<script src="jquery.min.js"></script>
<!-- Bootstrap depois -->
<script src="bootstrap.bundle.min.js"></script>
<!-- AdminLTE por último -->
<script src="adminlte.min.js"></script>
```

## 🔍 Ferramentas de Diagnóstico

### Comandos Úteis

#### Verificar Status do Sistema:
```bash
# Versão PHP
php -v

# Extensões PHP
php -m

# Configuração Laravel
php artisan about

# Status do banco
php artisan migrate:status

# Limpar caches
php artisan optimize:clear
```

#### Logs Importantes:
```bash
# Laravel
tail -f storage/logs/laravel.log

# Apache
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# MySQL
tail -f /var/log/mysql/error.log
```

### Modo Debug

#### Ativar Debug (apenas desenvolvimento):
```env
APP_DEBUG=true
APP_ENV=local
```

#### Instalar Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Testes de Conectividade

#### Testar Banco:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SELECT 1');
```

#### Testar Email:
```bash
php artisan tinker
>>> Mail::raw('Teste', function($msg) { $msg->to('teste@email.com'); });
```

#### Testar Upload:
```bash
# Verificar permissões
ls -la storage/app/public/

# Testar escrita
touch storage/app/public/test.txt
```

## 📞 Quando Buscar Ajuda

### Informações para Incluir no Suporte:

1. **Versão do Sistema**: Encontre em Configurações → Sobre
2. **Versão PHP**: `php -v`
3. **Sistema Operacional**: `uname -a` (Linux) ou versão Windows
4. **Servidor Web**: Apache/Nginx + versão
5. **Banco de Dados**: MySQL/MariaDB + versão
6. **Logs de Erro**: Últimas linhas relevantes
7. **Passos para Reproduzir**: Sequência exata que causa o problema

### Template de Reporte de Bug:

```
**Descrição do Problema:**
[Descreva o que está acontecendo]

**Passos para Reproduzir:**
1. Vá para...
2. Clique em...
3. Veja o erro...

**Comportamento Esperado:**
[O que deveria acontecer]

**Ambiente:**
- Versão HomeMechanic: 1.0.0
- PHP: 8.4.x
- Servidor: Apache 2.4.x
- SO: Ubuntu 22.04

**Logs de Erro:**
[Cole os logs relevantes aqui]

**Screenshots:**
[Se aplicável]
```

---

**Não conseguiu resolver?** Entre em contato: suporte@homemechanic.com.br

**Última atualização**: 15 de Abril de 2026