# Troubleshooting - Solução de Problemas

Este guia ajuda a resolver os problemas mais comuns do HomeMechanic System.

## Problemas de Instalação

### Erro: "Nenhuma conexão pôde ser feita"

**Sintoma:** Erro de conexão com o banco de dados durante a instalação.

**Soluções:**
1. Verifique se o MySQL/MariaDB está rodando
2. Confirme as credenciais no arquivo `.env`
3. Teste a conexão manualmente:
   ```bash
   mysql -h 127.0.0.1 -u root -p
   ```

### Erro: "Class not found"

**Sintoma:** Erro de classe não encontrada após instalação.

**Soluções:**
1. Execute o comando de otimização:
   ```bash
   php artisan optimize:clear
   composer dump-autoload
   ```

### Permissões de Arquivo

**Sintoma:** Erro de permissão ao criar arquivos.

**Soluções:**
1. Configure as permissões corretas:
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

## Problemas de Upload

### Uploads Não Funcionam

**Sintomas:**
- Arquivos não são enviados
- Erro 413 (Request Entity Too Large)
- Timeout durante upload

**Soluções:**

1. **Verificar configurações PHP:**
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   memory_limit = 256M
   ```

2. **Verificar configurações do servidor web:**
   
   **Apache (.htaccess):**
   ```apache
   LimitRequestBody 104857600
   ```
   
   **Nginx:**
   ```nginx
   client_max_body_size 100M;
   ```

3. **Verificar permissões do diretório:**
   ```bash
   chmod -R 755 storage/app/public/
   ```

### Imagens Não Aparecem

**Sintoma:** Uploads são feitos mas imagens não aparecem no site.

**Soluções:**
1. Criar link simbólico:
   ```bash
   php artisan storage:link
   ```

2. Verificar configuração do `.env`:
   ```env
   FILESYSTEM_DISK=public
   ```

## Problemas de E-mail

### E-mails Não São Enviados

**Sintomas:**
- Formulário de contato não envia
- Notificações não chegam
- Erro SMTP

**Soluções:**

1. **Verificar configurações SMTP no painel admin**
2. **Testar configurações manualmente:**
   ```bash
   php artisan tinker
   Mail::raw('Teste', function($msg) {
       $msg->to('teste@exemplo.com')->subject('Teste SMTP');
   });
   ```

3. **Configurações comuns por provedor:**

   **Gmail:**
   ```env
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_ENCRYPTION=tls
   ```

   **Outlook/Hotmail:**
   ```env
   MAIL_HOST=smtp-mail.outlook.com
   MAIL_PORT=587
   MAIL_ENCRYPTION=starttls
   ```

## Problemas de Performance

### Site Lento

**Sintomas:**
- Páginas demoram para carregar
- Timeout em operações

**Soluções:**

1. **Ativar cache:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Otimizar banco de dados:**
   ```sql
   OPTIMIZE TABLE posts, gallery_photos, services;
   ```

3. **Configurar cache de sessão:**
   ```env
   SESSION_DRIVER=database
   CACHE_STORE=database
   ```

### Muitas Consultas ao Banco

**Sintoma:** Logs mostram muitas queries SQL.

**Soluções:**
1. Verificar relacionamentos Eloquent
2. Usar eager loading quando necessário
3. Implementar cache de queries

## Problemas de Segurança

### Ataques de Força Bruta

**Sintomas:**
- Muitas tentativas de login
- IPs suspeitos nos logs

**Soluções:**
1. Verificar rate limiting no login
2. Implementar bloqueio por IP
3. Usar senhas fortes
4. Ativar autenticação de dois fatores (se disponível)

### Arquivos Maliciosos

**Sintoma:** Upload de arquivos suspeitos.

**Soluções:**
1. Verificar validação de MIME types
2. Implementar scan de vírus
3. Restringir tipos de arquivo permitidos

## Problemas do Modo de Manutenção

### Não Consegue Acessar Durante Manutenção

**Sintoma:** Bloqueado mesmo sendo administrador.

**Soluções:**
1. Adicionar seu IP na lista de permitidos
2. Desativar via banco de dados:
   ```sql
   UPDATE settings SET value = '0' WHERE key = 'maintenance_mode';
   ```

## Logs e Debugging

### Onde Encontrar Logs

1. **Logs do Laravel:**
   ```
   storage/logs/laravel.log
   ```

2. **Logs do servidor web:**
   - Apache: `/var/log/apache2/error.log`
   - Nginx: `/var/log/nginx/error.log`

3. **Logs do PHP:**
   - Verificar `php.ini` para localização

### Ativar Debug Mode

**Apenas em desenvolvimento:**
```env
APP_DEBUG=true
APP_ENV=local
```

**NUNCA em produção!**

### Comandos Úteis para Debug

```bash
# Limpar todos os caches
php artisan optimize:clear

# Verificar configuração
php artisan config:show

# Verificar rotas
php artisan route:list

# Verificar jobs na fila
php artisan queue:work --verbose

# Verificar status do sistema
php artisan about
```

## Problemas Específicos do HomeMechanic

### Galeria Não Carrega

**Soluções:**
1. Verificar se as imagens existem em `storage/app/public/uploads/`
2. Regenerar thumbnails se necessário
3. Verificar permissões das pastas

### Blog Posts Não Aparecem

**Soluções:**
1. Verificar se posts estão publicados (`status = 'published'`)
2. Verificar data de publicação (`published_at`)
3. Limpar cache de views

### Serviços Não Exibem

**Soluções:**
1. Verificar se serviços estão ativos (`active = 1`)
2. Verificar ordem de exibição (`sort_order`)
3. Verificar se há conteúdo nos campos obrigatórios

## Contato para Suporte

Se os problemas persistirem:

- **E-mail:** suporte@homemechanic.com.br
- **Telefone:** (11) 99999-9999
- **Horário:** Segunda a Sexta, 9h às 18h

### Informações para Incluir no Contato

1. Versão do sistema
2. Versão do PHP
3. Tipo de servidor (Apache/Nginx)
4. Logs de erro relevantes
5. Passos para reproduzir o problema
6. Screenshots se aplicável

---

**Última atualização:** {{ date('d/m/Y') }}