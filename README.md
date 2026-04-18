# 🔧 HomeMechanic System

> Sistema completo para oficinas especializadas em carros de luxo esportivos e tuning

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Ativo-brightgreen.svg)]()

## 📋 Sobre o Projeto

O **HomeMechanic** é um sistema web completo desenvolvido especificamente para oficinas mecânicas que trabalham com carros de luxo esportivos e tuning. O sistema oferece uma solução integrada com painel administrativo, site institucional, blog, galeria de trabalhos e muito mais.

### 🎯 Características Principais

- **🏗️ Arquitetura Modular**: Sistema organizado em módulos independentes
- **🎨 Design Responsivo**: Interface moderna e adaptável a todos os dispositivos
- **🔒 Segurança Avançada**: Múltiplas camadas de proteção e auditoria
- **📱 Mobile-First**: Otimizado para dispositivos móveis
- **🌐 SEO Otimizado**: Meta tags dinâmicas e URLs amigáveis
- **📊 Dashboard Completo**: Estatísticas e métricas em tempo real
- **🔧 Fácil Instalação**: Instalador automático com verificação de requisitos

## 🚀 Funcionalidades

### 🏢 Site Institucional
- Página inicial com seções animadas
- Galeria de trabalhos com lightbox
- Blog com sistema de categorias e tags
- Formulário de contato integrado
- Depoimentos de clientes
- Páginas de serviços detalhadas

### ⚙️ Painel Administrativo
- Dashboard com métricas importantes
- Gerenciamento Dinâmico de Conteúdo Frontend (Hero, CTA, Sobre)
- CRUD avançado para Módulo de Depoimentos, Galeria e Serviços
- **📤 Sistema de Upload Modernizado**: Fluxo assíncrono com FilePond, barra de progresso e UUIDs
- **📸 Gestão de Identidade**: Logo e Favicon configuráveis diretamente pelo painel
- **🚧 Modo de Manutenção Premium**: Interface isolada com countdown e identidades dinâmicas
- **⚡ Performance Agravada**: Limpeza automática de uploads órfãos e sistema de cache otimizado
- Logs de auditoria completos

### 🔐 Segurança
- Autenticação com rate limiting
- Middleware de cabeçalhos de segurança
- Validação MIME real para uploads
- Sanitização automática de entradas
- Proteção CSRF em todos os formulários
- Controle de acesso por IP

## 📋 Requisitos do Sistema

### Servidor Web
- **PHP**: 8.4 ou superior
- **Servidor**: Apache com mod_rewrite
- **Banco de Dados**: MySQL 8.0+ ou MariaDB 10.6+
- **Memória**: Mínimo 512MB RAM
- **Espaço**: Mínimo 1GB de disco

### Extensões PHP Obrigatórias
```
pdo_mysql, mbstring, openssl, tokenizer, xml, 
ctype, json, bcmath, fileinfo, gd
```

### Permissões de Diretório
```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
```

## 🛠️ Instalação

### 1. Download e Configuração

```bash
# Clone o repositório
git clone https://github.com/homemechanic/system.git
cd homemechanic

# Instale as dependências
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

### 2. Configuração do Servidor

#### Apache (.htaccess)
```apache
# Arquivo .htaccess na raiz
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /path/to/homemechanic/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Instalação Automática

1. Acesse `http://seu-dominio.com/install`
2. Siga o assistente de instalação
3. Configure o banco de dados
4. Crie o usuário administrador
5. Finalize a instalação

## 🎨 Personalização

### Paleta de Cores
```scss
:root {
    --color-primary: #FF6B00;    /* Laranja */
    --color-dark: #0D0D0D;       /* Preto */
    --color-graphite: #2C2C2C;   /* Grafite */
    --color-white: #FFFFFF;      /* Branco */
}
```

### Logotipo e Favicon
```
public/img/logo.png          # Logo principal
public/img/favicon.ico       # Favicon
public/img/og-default.jpg    # Imagem padrão Open Graph
```

## 📚 Documentação

### Estrutura de Módulos
```
app/Modules/
├── Auth/              # Autenticação
├── Dashboard/         # Painel principal
├── Services/          # Gerenciamento de serviços
├── Gallery/           # Galeria de fotos
├── Blog/              # Sistema de blog
├── Contact/           # Formulário de contato
├── Settings/          # Configurações
├── Maintenance/       # Modo de manutenção
├── Upload/            # Sistema de upload
└── Frontend/          # Páginas públicas
```

### Configurações Importantes

#### Banco de Dados (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homemechanic
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

#### SMTP (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
```

#### Configurações de Upload
```env
UPLOAD_MAX_SIZE=10240          # 10MB para imagens
UPLOAD_VIDEO_MAX_SIZE=102400   # 100MB para vídeos
```

## 🧪 Testes

### Executar Testes
```bash
# Testes unitários
php artisan test

# Testes de propriedades
php artisan test --testsuite=Properties

# Cobertura de código
php artisan test --coverage
```

### Propriedades Testadas
- Verificação de requisitos do instalador
- Sanitização de entradas
- Validação de uploads
- Geração de slugs únicos
- Middleware de segurança
- Sistema de auditoria

## 🔧 Manutenção

### Backup do Banco
```bash
# Backup completo
mysqldump -u usuario -p homemechanic > backup_$(date +%Y%m%d).sql

# Restaurar backup
mysql -u usuario -p homemechanic < backup_20260415.sql
```

### Limpeza de Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Otimização para Produção
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

## 🐛 Solução de Problemas

### 🛠️ Ferramentas de Debug (v1.0.1+)

Para problemas de instalação, use as ferramentas de debug integradas:

#### 1. Correção de Emergência
```
https://seudominio.com/fix-key-emergency.php
```
- Corrige arquivo .env
- Gera nova APP_KEY
- Limpa caches
- Verifica permissões

#### 2. Debug Completo da Instalação
```
https://seudominio.com/debug-error.php
```
- Simula processo completo de instalação
- Captura todos os erros
- Mostra stack traces detalhados

#### 3. Teste Personalizado
```
https://seudominio.com/check-install-error.php
```
- Formulário com seus dados reais
- Teste passo a passo
- Logs específicos da instalação

#### 4. Teste de CSRF e Rotas
```
https://seudominio.com/test-csrf-install.php
```
- Verifica CSRF tokens
- Testa rotas do sistema
- Compara formulário vs AJAX

#### 5. Diagnóstico Geral
```
https://seudominio.com/debug-installation.php
```
- Verifica requisitos do sistema
- Testa conexão com banco
- Analisa logs de erro

### Problemas Comuns

#### Erro 500 - Internal Server Error
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

#### Erro de Conexão com Banco
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

#### Problemas de Upload
```bash
# Verificar configurações PHP
php -i | grep -E "(upload_max_filesize|post_max_size|max_execution_time)"
```

## 📞 Suporte

### Documentação Completa
- [Manual do Usuário](docs/manual-usuario.md)
- [Guia do Desenvolvedor](docs/guia-desenvolvedor.md)
- [API Reference](docs/api-reference.md)
- [FAQ](docs/faq.md)

### Contato
- **Email**: suporte@homemechanic.com.br
- **Website**: https://homemechanic.com.br
- **Issues**: [GitHub Issues](https://github.com/homemechanic/system/issues)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🏆 Créditos

Desenvolvido com ❤️ pela equipe HomeMechanic

### Tecnologias Utilizadas
- [Laravel 11](https://laravel.com) - Framework PHP
- [AdminLTE 4](https://adminlte.io) - Template administrativo
- [Bootstrap 5](https://getbootstrap.com) - Framework CSS
- [Intervention Image](http://image.intervention.io) - Processamento de imagens
- [FilePond](https://pqina.nl/filepond/) - Sistema de upload assíncrono versátil
- [SweetAlert2](https://sweetalert2.github.io) - Alertas elegantes
- [Toastify](https://apvarun.github.io/toastify-js/) - Notificações

---

<div align="center">
  <strong>HomeMechanic System v1.0.0</strong><br>
  Especialistas em Carros de Luxo e Tuning
</div>