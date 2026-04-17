# Guia de Teste do Instalador

## Correções Aplicadas

Foi identificado e corrigido o problema onde o nome do banco de dados estava sendo perdido no fluxo de dados entre o Controller e o Service.

### O que foi corrigido:
1. ✅ Mapeamento correto de `db_name` → `database.name` no Controller
2. ✅ Logs detalhados adicionados para debug
3. ✅ Script de debug criado para verificar dados enviados
4. ✅ JavaScript corrigido para não consumir FormData

## Passo a Passo para Testar

### 1. Limpar Cache do Navegador
```
Ctrl + Shift + Delete (Chrome/Edge)
Cmd + Shift + Delete (Mac)
```
Ou abrir em aba anônima: `Ctrl + Shift + N`

### 2. Testar Fluxo de Dados (Opcional)
Acesse: `https://homemechanic.com.br/test-install-flow.php`

**Resultado esperado:**
```json
{
  "success": true,
  "message": "Fluxo de dados correto!",
  "use_statement": "USE `homemechanic_test`",
  "use_statement_valid": true
}
```

Se `use_statement_valid` for `false`, há um problema no código.

### 3. Acessar o Instalador
```
https://homemechanic.com.br/install/steps
```

### 4. Abrir Console do Navegador
Pressione `F12` e vá para a aba "Console"

### 5. Preencher Dados

#### Step 1: Banco de Dados
- **Host:** localhost (ou 127.0.0.1)
- **Porta:** 3306
- **Nome do Banco:** homemechanic_db (ou outro nome)
- **Usuário:** seu_usuario
- **Senha:** sua_senha

**IMPORTANTE:** Clique em "Testar Conexão" antes de avançar!

#### Step 2: Administrador
- **Nome:** Seu Nome
- **E-mail:** seu@email.com
- **Senha:** mínimo 8 caracteres
- **Confirmar Senha:** mesma senha

#### Step 3: Empresa
- **Nome da Empresa:** HomeMechanic
- **Descrição:** (opcional)
- **URL do Sistema:** (detectada automaticamente)
- ✅ **Aceitar Termos:** marcar checkbox

### 6. Iniciar Instalação

Clique em "Iniciar Instalação" e observe:

#### No Console do Navegador:
```javascript
DEBUG - Dados sendo enviados: {
  "success": true,
  "present_fields": {
    "db_name": "homemechanic_db",  // ← DEVE ESTAR PRESENTE
    "db_host": "localhost",
    "db_user": "usuario",
    ...
  },
  "missing_fields": []  // ← DEVE ESTAR VAZIO
}
```

**Se houver campos em `missing_fields`:**
- Volte e preencha os campos faltando
- Verifique se todos os campos obrigatórios estão preenchidos

#### Progresso da Instalação:
```
✓ Testando conexão com banco de dados...
✓ Criando arquivo .env...
✓ Gerando APP_KEY...
✓ Criando tabelas do banco de dados...
✓ Inserindo dados iniciais...
✓ Criando usuário administrador...
✓ Finalizando instalação...
```

### 7. Verificar Logs do Laravel

Se houver erro, verifique:
```bash
tail -f storage/logs/laravel.log
```

**Logs esperados:**
```
[INFO] Dados preparados para instalação
[INFO] Iniciando instalação do HomeMechanic
[INFO] Etapa 1: Preparando dados de instalação
[INFO] Dados preparados
  - database_name: homemechanic_db  ← DEVE ESTAR PREENCHIDO
  - database_host: localhost
[INFO] Etapa 5.1: Reconectando ao banco de dados
[INFO] Configuração do banco atualizada
[INFO] Banco selecionado via USE statement
  - database: homemechanic_db  ← DEVE ESTAR CORRETO
[INFO] Instalação concluída com sucesso
```

## Erros Comuns e Soluções

### Erro: "No database selected"
**Causa:** Nome do banco vazio
**Solução:** 
1. Verificar console do navegador
2. Verificar se `db_name` está em `present_fields`
3. Verificar logs do Laravel para ver o valor de `database_name`

### Erro: "Campos obrigatórios faltando"
**Causa:** JavaScript não está enviando todos os campos
**Solução:**
1. Limpar cache do navegador
2. Recarregar página (Ctrl+F5)
3. Verificar console para erros JavaScript

### Erro: "Dados inválidos. Verifique os campos"
**Causa:** Validação do Laravel falhou
**Solução:**
1. Verificar console do navegador para ver quais campos falharam
2. Verificar se senhas conferem
3. Verificar se email é válido
4. Verificar se termos foram aceitos

### Erro: "SQLSTATE[HY000] [1045] Access denied"
**Causa:** Credenciais do banco incorretas
**Solução:**
1. Verificar usuário e senha do banco
2. Verificar se usuário tem permissões no banco
3. Testar conexão antes de instalar

### Erro: "SQLSTATE[HY000] [2002] Connection refused"
**Causa:** MySQL não está rodando ou host incorreto
**Solução:**
1. Verificar se MySQL está rodando
2. Usar `localhost` ou `127.0.0.1`
3. Verificar porta (padrão: 3306)

## Scripts de Debug Disponíveis

### 1. Debug de Dados Enviados
```
https://homemechanic.com.br/debug-install.php
```
Mostra exatamente quais dados o JavaScript está enviando.

### 2. Teste de Fluxo
```
https://homemechanic.com.br/test-install-flow.php
```
Simula o fluxo completo de dados do instalador.

### 3. Verificar Rotas
```bash
php artisan route:list | grep install
```

### 4. Limpar Todos os Caches
```
https://homemechanic.com.br/clear-all-cache.php
```

## Após Instalação Bem-Sucedida

1. **Remover scripts de debug:**
```bash
rm public/debug-install.php
rm public/test-install-flow.php
rm public/test-database-direct.php
rm public/check-routes.php
rm public/clear-all-cache.php
rm public/clear-opcache.php
rm public/install-nocache.html
```

2. **Acessar painel admin:**
```
https://homemechanic.com.br/admin/login
```

3. **Credenciais:**
- Email: o que você cadastrou
- Senha: a que você cadastrou

## Suporte

Se o problema persistir:

1. **Coletar informações:**
   - Console do navegador (F12)
   - Logs do Laravel (`storage/logs/laravel.log`)
   - Resposta do `debug-install.php`
   - Resposta do `test-install-flow.php`

2. **Verificar:**
   - Versão do PHP: `php -v` (deve ser 8.4.x)
   - Versão do MySQL: `mysql --version`
   - Permissões dos diretórios `storage/` e `bootstrap/cache/`

3. **Informar:**
   - Mensagem de erro completa
   - Logs relevantes
   - Passos que levaram ao erro
