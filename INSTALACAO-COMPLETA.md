# HomeMechanic - Guia Completo de Instalação

## 🎯 Resumo das Melhorias Implementadas

### ✅ Problemas Corrigidos

1. **Migration da tabela `settings`** - Adicionado campo `created_at` que estava faltando
2. **Método `createSuperAdminUser`** - Melhorado com validação completa e tratamento de erros
3. **Método `runBasicSeeders`** - Adicionado verificação de tabelas e tratamento de erros robusto
4. **Verificação de requisitos** - Corrigido erro "mod_rewrite" → "url_rewrite"

### 🛠️ Ferramentas de Debug Criadas

1. **`/debug-installation.php`** - Diagnóstico detalhado de problemas
2. **`/test-installation.php`** - Teste automatizado completo da instalação
3. **`/fix-key-emergency.php`** - Correção de emergência para problemas de configuração

## 🚀 Processo de Instalação Melhorado

### Etapas do Instalador

1. **Verificação de Requisitos**
   - PHP 8.4+ (não 8.5+)
   - Extensões necessárias
   - Permissões de diretórios
   - Servidor web (LiteSpeed/Apache)
   - URL Rewrite

2. **Configuração do Sistema**
   - Dados do banco de dados
   - Informações do administrador
   - Dados da empresa
   - URL do sistema (detectada automaticamente)

3. **Processo de Instalação**
   - Criação do arquivo `.env`
   - Geração da `APP_KEY`
   - Execução das migrations
   - Inserção de dados básicos
   - Criação do usuário administrador
   - Otimização do sistema

## 🔧 Ferramentas de Diagnóstico

### 1. Debug de Instalação (`/debug-installation.php`)

**Funcionalidades:**
- Teste de carregamento do Laravel
- Verificação da APP_KEY
- Teste de conexão com banco de dados
- Verificação de migrations
- Análise de logs de erro
- Verificação de permissões

**Como usar:**
```
https://homemechanic.com.br/debug-installation.php
```

### 2. Teste Completo (`/test-installation.php`)

**Funcionalidades:**
- Execução automatizada de toda a instalação
- Teste com dados pré-configurados
- Verificação passo a passo
- Relatório detalhado de resultados

**Dados de teste padrão:**
- Banco: `homemechanic_2026`
- Usuário DB: `homemechanic`
- Admin: `admin@teste.com` / `admin123456`
- Empresa: `HomeMechanic Teste`

### 3. Correção de Emergência (`/fix-key-emergency.php`)

**Funcionalidades:**
- Criação/correção do arquivo `.env`
- Geração de nova `APP_KEY`
- Limpeza de caches
- Verificação de permissões
- Teste do Laravel

## 🐛 Solução de Problemas Comuns

### Erro: "Ocorreu um erro durante a instalação"

**Passos para resolver:**

1. **Acesse o debug detalhado:**
   ```
   https://homemechanic.com.br/debug-installation.php
   ```

2. **Execute a correção de emergência:**
   ```
   https://homemechanic.com.br/fix-key-emergency.php
   ```

3. **Teste a instalação automatizada:**
   ```
   https://homemechanic.com.br/test-installation.php
   ```

### Erro: "APP_KEY não configurada"

**Solução:**
1. Acesse `/fix-key-emergency.php`
2. Clique em "Corrigir .env" ou "Corrigir Tudo Automaticamente"
3. Verifique se a APP_KEY foi gerada

### Erro: "Falha nas migrations"

**Verificações:**
1. Confirme que todas as migrations existem em `database/migrations/`
2. Verifique permissões do banco de dados
3. Confirme que o usuário do banco tem privilégios CREATE/DROP

### Erro: "Permissões de diretório"

**Solução:**
1. Execute `/fix-key-emergency.php`
2. Clique em "Verificar Permissões"
3. Ou execute manualmente:
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

## 📋 Checklist de Instalação

### Antes da Instalação

- [ ] PHP 8.4.x instalado (não 8.5+)
- [ ] Extensões PHP necessárias ativas
- [ ] Banco MySQL/MariaDB criado
- [ ] Usuário do banco com privilégios completos
- [ ] Permissões de diretório configuradas

### Durante a Instalação

- [ ] Requisitos verificados (todos ✅)
- [ ] Conexão com banco testada
- [ ] Dados do administrador preenchidos
- [ ] Informações da empresa configuradas

### Após a Instalação

- [ ] Arquivo `storage/installed` criado
- [ ] Login no painel administrativo funcionando
- [ ] Sistema redirecionando corretamente
- [ ] Logs sem erros críticos

## 🔍 Logs e Monitoramento

### Localização dos Logs

- **Laravel:** `storage/logs/laravel.log`
- **Servidor:** Logs do LiteSpeed/Apache
- **PHP:** Logs de erro do PHP

### Comandos Úteis para Debug

```bash
# Verificar logs do Laravel
tail -f storage/logs/laravel.log

# Limpar caches manualmente
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Verificar status das migrations
php artisan migrate:status

# Gerar nova APP_KEY
php artisan key:generate --force
```

## 🆘 Suporte de Emergência

### Se nada funcionar:

1. **Backup dos dados importantes**
2. **Execute limpeza completa:**
   ```bash
   rm -f .env
   rm -f storage/installed
   rm -rf bootstrap/cache/*
   rm -rf storage/framework/cache/*
   rm -rf storage/framework/views/*
   ```

3. **Acesse a correção de emergência:**
   ```
   https://homemechanic.com.br/fix-key-emergency.php
   ```

4. **Execute "Corrigir Tudo Automaticamente"**

5. **Tente a instalação novamente:**
   ```
   https://homemechanic.com.br/install
   ```

## 📞 Informações de Contato

- **Sistema:** HomeMechanic v1.0.0
- **Servidor:** 15.235.57.3:1979
- **Usuário:** homemechanic
- **Diretório:** /home/homemechanic/public_html

## 🔄 Próximos Passos

Após a instalação bem-sucedida:

1. **Acesse o painel administrativo**
2. **Configure as informações da empresa**
3. **Ajuste as configurações de email**
4. **Configure o modo de manutenção se necessário**
5. **Teste todas as funcionalidades principais**

---

**Nota:** Este guia foi criado especificamente para resolver os problemas encontrados durante a instalação do HomeMechanic. Todas as ferramentas de debug foram testadas e estão funcionais.