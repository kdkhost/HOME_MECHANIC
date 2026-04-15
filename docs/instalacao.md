# 🚀 Guia Completo de Instalação - HomeMechanic System

Este guia fornece instruções detalhadas para instalar o HomeMechanic System em diferentes ambientes.

## 📋 Pré-requisitos

### Requisitos do Servidor

#### Servidor Web
- **Apache 2.4+** com mod_rewrite habilitado
- **Nginx 1.18+** (alternativa ao Apache)
- **PHP 8.4+** com as extensões necessárias
- **MySQL 8.0+** ou **MariaDB 10.6+**

#### Extensões PHP Obrigatórias
```bash
# Verificar extensões instaladas
php -m | grep -E "(pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd)"
```

Extensões necessárias:
- `pdo_mysql` - Conexão com MySQL/MariaDB
- `mbstring` - Manipulação de strings multibyte
- `openssl` - Criptografia e HTTPS
- `tokenizer` - Análise de código PHP
- `xml` - Processamento XML
- `ctype` - Verificação de tipos de caracteres
- `json` - Manipulação JSON
- `bcmath` - Matemática de precisão arbitrária
- `fileinfo` - Detecção de tipos de arquivo
- `gd` - Processamento de imagens

#### Configurações PHP Recomendadas
```ini
# php.ini
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_vars = 3000
```

### Requisitos de Hardware

#### Mínimo
- **CPU**: 1 core 2.0 GHz
- **RAM**: 512 MB
- **Disco**: 2 GB livres
- **Largura de Banda**: 10 Mbps

#### Recomendado
- **CPU**: 2+ cores 2.5 GHz
- **RAM**: 2 GB+
- **Disco**: 10 GB+ SSD
- **Largura de Banda**: 100 Mbps

## 📥 Métodos de Instalação

### Método 1: Instalação via Composer (Recomendado)

#### Passo 1: Download do Sistema
```bash
# Via Composer (se disponível no Packagist)
composer create-project homemechanic/system minha-oficina

# Ou via Git
git clone https://github.com/homemechanic/system.git minha-oficina
cd minha-oficina
```

#### Passo 2: Instalação de Dependências
```bash
# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Instalar dependências JavaScript
npm install

# Compilar assets
npm run build
```

#### Passo 3: Configuração de Permissões
```bash
# Linux/macOS
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env.example

# Copiar arquivo de configuração
cp .env.example .env
```

### Método 2: Upload Manual via FTP

#### Passo 1: Preparação Local
1. Baixe o sistema do GitHub
2. Execute `composer install --no-dev`
3. Execute `npm install && npm run build`
4. Comprima todos os arquivos

#### Passo 2: Upload via FTP
1. Conecte-se ao seu servidor via FTP
2. Navegue até a pasta `public_html` ou equivalente
3. Faça upload de todos os arquivos
4. Extraia se necessário

#### Passo 3: Configuração no Servidor
```bash
# Via SSH ou painel de controle
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 🔧 Configuração do Servidor Web

### Apache (.htaccess)

#### Arquivo .htaccess na Raiz
```apache
# /.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirecionar para HTTPS (opcional)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Redirecionar para pasta public
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Bloquear acesso a arquivos sensíveis
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>
```

#### Arquivo public/.htaccess
```apache
# /public/.htaccess
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

# Cabeçalhos de Segurança
<IfModule mod_headers.c>
    Header always set X-Frame-Options "DENY"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Compressão Gzip
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>

# Cache de Assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Nginx

#### Configuração Completa
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name seu-dominio.com www.seu-dominio.com;
    
    # Redirecionar para HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name seu-dominio.com www.seu-dominio.com;
    
    root /var/www/homemechanic/public;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    # Security Headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
    
    # Cache Control
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Block access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(composer\.json|composer\.lock|\.env) {
        deny all;
    }
    
    # PHP Processing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

## 🗄️ Configuração do Banco de Dados

### MySQL/MariaDB

#### Criação do Banco
```sql
-- Conectar como root
mysql -u root -p

-- Criar banco de dados
CREATE DATABASE homemechanic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário (recomendado)
CREATE USER 'homemechanic_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';

-- Conceder privilégios
GRANT ALL PRIVILEGES ON homemechanic.* TO 'homemechanic_user'@'localhost';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Sair
EXIT;
```

#### Configuração de Performance
```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
max_connections = 100
query_cache_size = 32M
query_cache_type = 1
```

### Configuração do .env

```env
# Aplicação
APP_NAME=HomeMechanic
APP_ENV=production
APP_KEY=base64:GERAR_NOVA_CHAVE_AQUI
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo
APP_URL=https://seu-dominio.com

# Localização
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homemechanic
DB_USERNAME=homemechanic_user
DB_PASSWORD=senha_segura_aqui

# Cache e Sessão
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Email (configurar depois)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contato@seu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"

# Segurança
BCRYPT_ROUNDS=12
```

## 🎯 Instalação Automática via Web

### Passo 1: Acesso ao Instalador
1. Abra seu navegador
2. Acesse `http://seu-dominio.com/install`
3. O sistema redirecionará automaticamente se não estiver instalado

### Passo 2: Verificação de Requisitos
O instalador verificará:
- ✅ Versão do PHP (8.4+)
- ✅ Extensões PHP necessárias
- ✅ Permissões de diretórios
- ✅ Módulo mod_rewrite (Apache)
- ✅ Conectividade com banco de dados

### Passo 3: Configuração do Banco
Preencha os campos:
- **Host**: Geralmente `localhost` ou `127.0.0.1`
- **Porta**: Geralmente `3306`
- **Nome do Banco**: Nome criado anteriormente
- **Usuário**: Usuário do banco
- **Senha**: Senha do usuário

### Passo 4: Dados do Administrador
Configure o primeiro usuário:
- **Nome Completo**: Seu nome
- **Email**: Seu email (será usado para login)
- **Senha**: Senha segura (mínimo 8 caracteres)

### Passo 5: Informações da Empresa
- **Nome da Empresa**: Nome da sua oficina
- **Email de Contato**: Email público
- **Telefone**: Número de contato
- **Endereço**: Endereço completo

### Passo 6: Finalização
1. Revise todas as informações
2. Clique em "Instalar Sistema"
3. Aguarde o processamento (pode levar alguns minutos)
4. Será redirecionado para o painel administrativo

## 🔒 Configurações de Segurança Pós-Instalação

### Arquivo .env
```bash
# Alterar permissões do .env
chmod 600 .env

# Verificar se está sendo ignorado pelo Git
echo ".env" >> .gitignore
```

### Gerar Nova Chave da Aplicação
```bash
php artisan key:generate
```

### Configurar SSL/HTTPS
1. Obtenha um certificado SSL (Let's Encrypt recomendado)
2. Configure no servidor web
3. Atualize APP_URL no .env para https://
4. Force HTTPS no .htaccess

### Backup Inicial
```bash
# Backup do banco
mysqldump -u usuario -p homemechanic > backup_inicial.sql

# Backup dos arquivos
tar -czf backup_arquivos_inicial.tar.gz /caminho/para/homemechanic/
```

## 🚀 Otimização para Produção

### Cache de Configuração
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Otimização do Composer
```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize
```

### Configuração de Logs
```bash
# Rotação de logs (crontab)
0 0 * * * /usr/sbin/logrotate /etc/logrotate.d/laravel
```

## 🐛 Solução de Problemas Comuns

### Erro 500 - Internal Server Error
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Limpar cache
php artisan cache:clear
php artisan config:clear
```

### Erro de Conexão com Banco
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar configurações
php artisan config:show database
```

### Problemas de Upload
```bash
# Verificar configurações PHP
php -i | grep -E "(upload_max_filesize|post_max_size|max_execution_time)"

# Verificar permissões
chmod -R 755 storage/app/public/
```

### Erro 404 - Página não encontrada
```bash
# Verificar mod_rewrite (Apache)
apache2ctl -M | grep rewrite

# Verificar .htaccess
ls -la public/.htaccess

# Testar configuração
php artisan route:list
```

## 📞 Suporte Técnico

### Informações para Suporte
Ao entrar em contato, forneça:
- URL do site
- Versão do PHP (`php -v`)
- Versão do sistema
- Logs de erro relevantes
- Passos para reproduzir o problema

### Canais de Suporte
- **Email**: suporte@homemechanic.com.br
- **Documentação**: [docs/](../docs/)
- **Issues**: GitHub Issues
- **FAQ**: [faq.md](faq.md)

---

**Última atualização**: 15 de Abril de 2026  
**Versão**: 1.0.0

> 🎉 **Parabéns!** Se chegou até aqui, seu HomeMechanic System está instalado e pronto para uso!