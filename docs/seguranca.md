# Guia de Segurança - HomeMechanic System

Este guia apresenta as práticas de segurança implementadas no sistema e recomendações para manter sua instalação segura.

## Recursos de Segurança Implementados

### 1. Autenticação e Autorização

**Sistema de Login Seguro:**
- Rate limiting: máximo 5 tentativas por 10 minutos
- Senhas criptografadas com bcrypt (custo 12)
- Regeneração de sessão após login
- Timeout de sessão configurável (padrão: 120 minutos)

**Controle de Acesso:**
- Sistema de roles (administrador, editor, etc.)
- Políticas de autorização por recurso
- Middleware de verificação de permissões

### 2. Proteção Contra Ataques

**Cabeçalhos de Segurança:**
- `X-Frame-Options: DENY` - Previne clickjacking
- `X-Content-Type-Options: nosniff` - Previne MIME sniffing
- `X-XSS-Protection: 1; mode=block` - Proteção XSS
- `Referrer-Policy: strict-origin-when-cross-origin` - Controla referrer

**Proteção CSRF:**
- Tokens CSRF em todos os formulários
- Verificação automática em requisições POST/PUT/DELETE
- Regeneração de tokens por sessão

**Sanitização de Entrada:**
- Remoção automática de tags HTML perigosas
- Validação rigorosa de tipos de arquivo
- Escape de saída para prevenir XSS

### 3. Upload Seguro de Arquivos

**Validação de MIME Type:**
- Verificação real do tipo de arquivo (não apenas extensão)
- Lista restrita de tipos permitidos
- Limites de tamanho por tipo de arquivo

**Armazenamento Seguro:**
- Arquivos armazenados fora do webroot
- Nomes de arquivo UUID para prevenir conflitos
- Verificação de integridade

### 4. Auditoria e Logs

**Sistema de Auditoria:**
- Log de todas as ações administrativas
- Registro de IP, user agent e timestamp
- Rastreamento de alterações (old/new values)

**Logs de Segurança:**
- Tentativas de login falhadas
- Acessos suspeitos
- Erros de validação
- Tentativas de upload malicioso

## Configurações de Segurança

### 1. Configuração do Servidor Web

#### Apache (.htaccess)

```apache
# Bloquear acesso a arquivos sensíveis
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "storage/installed">
    Order allow,deny
    Deny from all
</Files>

# Cabeçalhos de segurança
Header always set X-Frame-Options "DENY"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Forçar HTTPS (recomendado para produção)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

#### Nginx

```nginx
# Bloquear arquivos sensíveis
location ~ /\.env {
    deny all;
    return 404;
}

location ~ /storage/installed {
    deny all;
    return 404;
}

# Cabeçalhos de segurança
add_header X-Frame-Options "DENY" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# Forçar HTTPS
if ($scheme != "https") {
    return 301 https://$host$request_uri;
}
```

### 2. Configuração PHP

**php.ini recomendado:**

```ini
# Ocultar versão do PHP
expose_php = Off

# Desabilitar funções perigosas
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

# Configurações de upload
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20

# Configurações de sessão
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = "Strict"

# Logs de erro
log_errors = On
error_log = /var/log/php_errors.log
display_errors = Off
```

### 3. Configuração do Banco de Dados

**MySQL/MariaDB:**

```sql
-- Criar usuário específico para a aplicação
CREATE USER 'homemechanic'@'localhost' IDENTIFIED BY 'senha_forte_aqui';

-- Conceder apenas permissões necessárias
GRANT SELECT, INSERT, UPDATE, DELETE ON homemechanic.* TO 'homemechanic'@'localhost';

-- Remover usuários padrão
DROP USER 'root'@'%';
DROP USER ''@'localhost';

-- Configurações de segurança
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
```

## Práticas de Segurança Recomendadas

### 1. Senhas e Autenticação

**Políticas de Senha:**
- Mínimo 8 caracteres
- Combinação de letras, números e símbolos
- Não reutilizar senhas anteriores
- Trocar senhas regularmente

**Autenticação de Dois Fatores:**
- Implementar quando possível
- Usar aplicativos como Google Authenticator
- Backup codes para recuperação

### 2. Atualizações e Patches

**Manter Sistema Atualizado:**
- Atualizar Laravel regularmente
- Aplicar patches de segurança do PHP
- Atualizar dependências do Composer
- Monitorar vulnerabilidades conhecidas

**Comando para verificar vulnerabilidades:**
```bash
composer audit
```

### 3. Backup e Recuperação

**Estratégia de Backup:**
- Backup diário do banco de dados
- Backup semanal dos arquivos
- Testar restauração regularmente
- Armazenar backups em local seguro

**Script de backup automático:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u usuario -p senha homemechanic > backup_$DATE.sql
tar -czf files_$DATE.tar.gz /caminho/para/aplicacao
```

### 4. Monitoramento

**Logs a Monitorar:**
- Tentativas de login falhadas
- Uploads de arquivos suspeitos
- Erros 404 em massa (possível scan)
- Acessos a arquivos sensíveis

**Ferramentas Recomendadas:**
- Fail2ban para bloqueio automático
- Logwatch para análise de logs
- Monitoramento de integridade de arquivos

## Configuração para Produção

### 1. Variáveis de Ambiente

```env
# Nunca usar debug em produção
APP_DEBUG=false
APP_ENV=production

# Chave de aplicação forte
APP_KEY=base64:sua_chave_de_32_caracteres_aqui

# HTTPS obrigatório
APP_URL=https://seudominio.com

# Configurações de sessão seguras
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE_COOKIE=strict
```

### 2. Permissões de Arquivo

```bash
# Proprietário correto
chown -R www-data:www-data /caminho/para/aplicacao

# Permissões recomendadas
find /caminho/para/aplicacao -type f -exec chmod 644 {} \;
find /caminho/para/aplicacao -type d -exec chmod 755 {} \;

# Permissões especiais para storage e cache
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 3. Firewall

**Regras básicas do UFW:**
```bash
# Bloquear tudo por padrão
ufw default deny incoming
ufw default allow outgoing

# Permitir apenas serviços necessários
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp

# Ativar firewall
ufw enable
```

## Checklist de Segurança

### Instalação Inicial
- [ ] Alterar credenciais padrão
- [ ] Configurar HTTPS
- [ ] Definir permissões de arquivo corretas
- [ ] Configurar firewall
- [ ] Remover arquivos desnecessários

### Configuração
- [ ] Ativar todos os middlewares de segurança
- [ ] Configurar rate limiting
- [ ] Definir políticas de senha
- [ ] Configurar backup automático
- [ ] Testar recuperação de desastre

### Manutenção Regular
- [ ] Atualizar sistema e dependências
- [ ] Revisar logs de segurança
- [ ] Testar backups
- [ ] Verificar integridade dos arquivos
- [ ] Auditar contas de usuário

### Monitoramento
- [ ] Configurar alertas de segurança
- [ ] Monitorar tentativas de login
- [ ] Verificar uploads suspeitos
- [ ] Analisar padrões de tráfego
- [ ] Revisar logs de erro

## Resposta a Incidentes

### Em Caso de Comprometimento

1. **Isolamento Imediato:**
   - Desconectar servidor da internet
   - Ativar modo de manutenção
   - Preservar logs para análise

2. **Avaliação:**
   - Identificar escopo do comprometimento
   - Verificar integridade dos dados
   - Analisar logs de acesso

3. **Recuperação:**
   - Restaurar a partir de backup limpo
   - Aplicar patches de segurança
   - Alterar todas as senhas
   - Revisar configurações de segurança

4. **Prevenção:**
   - Implementar medidas adicionais
   - Atualizar políticas de segurança
   - Treinar usuários
   - Documentar lições aprendidas

## Contatos de Emergência

**Suporte de Segurança:**
- E-mail: security@homemechanic.com.br
- Telefone: (11) 99999-9999 (24h)
- Telegram: @homemechanic_security

**Recursos Externos:**
- CERT.br: https://www.cert.br/
- CVE Database: https://cve.mitre.org/
- Laravel Security: https://laravel.com/docs/security

---

**Importante:** A segurança é um processo contínuo. Revise e atualize regularmente suas práticas de segurança conforme novas ameaças surgem.

**Última atualização:** {{ date('d/m/Y') }}
**Versão:** 1.0.0