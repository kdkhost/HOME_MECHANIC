# Changelog - HomeMechanic System

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Não Lançado]

### Planejado
- Sistema de backup automático
- Integração com WhatsApp Business API (Evolution API GO)
- Dashboard de analytics avançado
- Sistema de agendamento online
- App mobile (React Native)

## [1.0.1] - 2026-04-16

### Corrigido
- **Instalação**: Migration da tabela `settings` - adicionado campo `created_at` ausente
- **Instalação**: Erro "mod_rewrite" → "url_rewrite" na verificação de requisitos
- **Instalação**: Método `createSuperAdminUser` com validação robusta e tratamento de erros
- **Instalação**: Método `runBasicSeeders` com verificação de tabelas e cleanup automático
- **Instalação**: Tratamento de exceções genéricas no processo de instalação

### Adicionado
- **Debug**: `/debug-installation.php` - Diagnóstico geral do sistema
- **Debug**: `/debug-error.php` - Simulação completa do processo de instalação
- **Debug**: `/test-installation.php` - Teste automatizado com dados pré-configurados
- **Debug**: `/check-install-error.php` - Teste personalizado com dados do usuário
- **Debug**: `/test-csrf-install.php` - Teste de CSRF e rotas
- **Documentação**: `CORREÇÕES-INSTALACAO.md` - Guia completo das correções implementadas
- **Documentação**: `INSTALACAO-COMPLETA.md` - Guia detalhado de instalação e solução de problemas

### Alterado
- **Debug**: `/fix-key-emergency.php` - Melhorado com mais funcionalidades de correção
- **Instalação**: Logging detalhado em todas as etapas da instalação
- **Instalação**: Validação de dados de entrada mais robusta
- **Instalação**: Mensagens de erro mais específicas e informativas
- **Instalação**: Cleanup automático em caso de falha na instalação
- **Instalação**: Verificação de tabelas antes de inserir dados

### Segurança
- **Instalação**: Sanitização aprimorada de dados sensíveis nos logs
- **Instalação**: Validação de entrada em todas as etapas do processo
- **Instalação**: Proteção contra reinstalação acidental

## [1.0.0] - 2026-04-15

### Adicionado
- **Sistema Base**
  - Arquitetura modular Laravel 11 com PHP 8.4
  - Estrutura de módulos independentes em `app/Modules/`
  - ModuleServiceProvider com descoberta automática de rotas
  - Configuração completa para MySQL/MariaDB
  - Localização completa em português brasileiro

- **Interface Administrativa**
  - Painel AdminLTE 4 customizado com paleta laranja/preto/grafite
  - Layout responsivo para dispositivos móveis
  - Preloader animado personalizado
  - Menu lateral com navegação intuitiva

- **Interface Pública**
  - Site institucional responsivo com Bootstrap 5
  - Layout moderno com animações CSS
  - Menu sticky com efeitos de scroll
  - Footer completo com informações de contato

- **Segurança**
  - Middleware de cabeçalhos de segurança
  - Middleware de verificação de instalação
  - Middleware de modo de manutenção com controle por IP
  - Hash bcrypt com custo 12 para senhas
  - Proteção CSRF em todos os formulários

- **Módulos Principais**
  - **Instalador**: Verificação automática de requisitos e instalação guiada
  - **Autenticação**: Sistema de login com rate limiting e sanitização
  - **Dashboard**: Cards de resumo e estatísticas do sistema
  - **Serviços**: CRUD completo com upload de imagens
  - **Galeria**: Categorização, lightbox e lazy loading
  - **Blog**: Editor WYSIWYG, SEO e sistema de tags
  - **Depoimentos**: Avaliações com estrelas e fotos
  - **Contato**: Formulário com envio SMTP
  - **Configurações**: Painel de configurações gerais e SMTP
  - **Manutenção**: Página de manutenção com controle de IPs
  - **Upload**: Sistema drag & drop com validação MIME

- **Funcionalidades Avançadas**
  - Sistema de upload com Dropzone.js e progress bar
  - Geração automática de thumbnails (400x300px)
  - Validação MIME real via `finfo_file()`
  - Slugs únicos automáticos para posts e serviços
  - Audit log completo de ações administrativas
  - Sistema de configurações dinâmicas
  - Páginas de erro personalizadas (403, 404, 419, 429, 500, 503)

- **Testes**
  - Framework de testes baseados em propriedades (Eris)
  - 13 propriedades de corretude implementadas
  - Testes de segurança e validação
  - Cobertura de todos os módulos críticos

- **Documentação**
  - Documentação completa integrada ao sistema
  - Guias de instalação e configuração
  - Manual do usuário detalhado
  - Documentação técnica para desenvolvedores

### Configurações Técnicas
- **PHP**: 8.4+
- **Laravel**: 11.x
- **Banco de Dados**: MySQL 8.0+ / MariaDB 10.6+
- **Frontend**: Bootstrap 5, AdminLTE 4, jQuery 3.7
- **Bibliotecas JS**: Dropzone, Toastify, SweetAlert2, Swiper
- **Processamento de Imagem**: Intervention Image 4.0
- **Testes**: PHPUnit 10.x, Eris (Property-Based Testing)

### Requisitos do Servidor
- PHP 8.4 ou superior
- Extensões PHP: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- Apache com mod_rewrite habilitado
- MySQL 8.0+ ou MariaDB 10.6+
- Permissões de escrita em `storage/` e `bootstrap/cache/`

### Estrutura de Arquivos
```
homemechanic/
├── app/
│   ├── Modules/           # Módulos do sistema
│   ├── Providers/         # Service Providers
│   ├── Policies/          # Políticas de autorização
│   └── Models/            # Models globais
├── resources/
│   ├── views/
│   │   ├── layouts/       # Layouts base
│   │   └── modules/       # Views dos módulos
│   ├── modules/           # Assets dos módulos
│   └── sass/              # Estilos customizados
├── database/
│   ├── migrations/        # Migrations do banco
│   └── seeders/           # Seeders de dados
├── docs/                  # Documentação do sistema
└── public/                # Assets públicos
```

### Paleta de Cores
- **Primária**: #FF6B00 (Laranja)
- **Escura**: #0D0D0D (Preto)
- **Grafite**: #2C2C2C
- **Branco**: #FFFFFF
- **Hover**: #E55A00

### Notas de Segurança
- Todas as entradas são sanitizadas com `strip_tags()` e `trim()`
- Validação MIME real para uploads
- Cabeçalhos de segurança implementados
- Proteção contra SQL Injection via Eloquent ORM
- Rate limiting em formulários de login
- Logs de auditoria para ações críticas

---

## Tipos de Mudanças
- `Adicionado` para novas funcionalidades
- `Alterado` para mudanças em funcionalidades existentes
- `Descontinuado` para funcionalidades que serão removidas
- `Removido` para funcionalidades removidas
- `Corrigido` para correções de bugs
- `Segurança` para vulnerabilidades corrigidas

## Links
- [Repositório](https://github.com/homemechanic/system)
- [Documentação](docs/README.md)
- [Issues](https://github.com/homemechanic/system/issues)
- [Releases](https://github.com/homemechanic/system/releases)