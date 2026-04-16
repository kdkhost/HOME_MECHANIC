# 🚀 Guia de Instalação - HomeMechanic System

## 📋 Pré-requisitos

- **PHP**: 8.4.x (NÃO 8.5+)
- **MySQL/MariaDB**: 5.7+ ou 10.3+
- **Servidor Web**: LiteSpeed, Apache ou Nginx
- **Extensões PHP Obrigatórias**:
  - PDO
  - PDO MySQL
  - Mbstring
  - OpenSSL
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - Fileinfo
  - GD (para processamento de imagens)

## 🔧 Instalação

### 1. Clone o Repositório

```bash
git clone https://github.com/kdkhost/HOME_MECHANIC.git
cd HOME_MECHANIC
```

### 2. Configure as Permissões

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework
```

### 3. Acesse o Instalador

Abra seu navegador e acesse:

```
https://seu-dominio.com
```

**IMPORTANTE**: Mesmo sem o arquivo `.env`, o sistema irá:
1. Detectar que o `.env` não existe
2. Copiar automaticamente o `.env.installer` para `.env` (temporário)
3. Redirecionar para o instalador em `/install`
4. O instalador criará o `.env` definitivo com suas configurações

### 4. Siga os 5 Steps do Instalador

#### Step 1: Banco de Dados
- Configure o host, porta, nome do banco, usuário e senha
- Clique em "Testar Conexão" para validar
- O banco de dados **deve já existir** (crie via cPanel/WHM)

#### Step 2: Administrador
- Defina nome, email e senha do primeiro usuário admin
- A senha deve ter no mínimo 8 caracteres

#### Step 3: Empresa
- Informe o nome da empresa
- Adicione uma descrição (opcional)
- A URL será detectada automaticamente

#### Step 4: Instalação
- Aguarde o processo automático
- Você verá o progresso de cada etapa:
  - ✅ Testando conexão com banco
  - ✅ Criando arquivo .env
  - ✅ Gerando APP_KEY
  - ✅ Criando tabelas
  - ✅ Inserindo dados iniciais
  - ✅ Criando usuário administrador
  - ✅ Finalizando instalação

#### Step 5: Concluído
- Anote as credenciais exibidas
- Escolha entre "Ir ao Site" ou "Ir ao Painel Admin"

## 🎯 Processo Automático

O instalador executa automaticamente:

1. ✅ Verifica requisitos do sistema
2. ✅ Testa conexão com banco de dados
3. ✅ Remove `.env` temporário
4. ✅ Cria arquivo `.env` definitivo
5. ✅ Preenche `.env` com dados fornecidos
6. ✅ Detecta e preenche URL automaticamente
7. ✅ Gera `APP_KEY` (ANTES de criar tabelas)
8. ✅ Executa migrations (cria todas as tabelas)
9. ✅ Insere dados iniciais (seeders)
10. ✅ Cria usuário superadmin
11. ✅ Marca instalação como concluída
12. ✅ Otimiza aplicação (cache de config e rotas)

## 🔐 Credenciais Padrão

As credenciais serão exibidas na tela final do instalador.

**IMPORTANTE**: Guarde-as em local seguro!

## 🌐 Ambientes de Produção

### cPanel/WHM

1. Faça upload dos arquivos para `public_html`
2. Crie o banco de dados via cPanel
3. Acesse seu domínio
4. Siga o instalador

### CloudLinux + LiteSpeed

O sistema detecta automaticamente:
- CloudLinux
- LiteSpeed Web Server
- Imunify360

Nenhuma configuração adicional é necessária.

## 🐛 Solução de Problemas

### Erro: "Arquivo .env não encontrado"

**Solução**: O sistema cria automaticamente. Se persistir:

```bash
cp .env.installer .env
php artisan key:generate
```

### Erro: "Permissão negada"

**Solução**: Ajuste as permissões:

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework
```

### Erro: "Conexão com banco de dados falhou"

**Solução**: Verifique:
- O banco de dados existe?
- As credenciais estão corretas?
- O usuário tem permissões no banco?

### Erro: "APP_KEY não definida"

**Solução**: O instalador gera automaticamente. Se persistir:

```bash
php artisan key:generate --force
```

## 📞 Suporte

Para problemas ou dúvidas:
- Email: dev@homemechanic.com.br
- GitHub Issues: https://github.com/kdkhost/HOME_MECHANIC/issues

## 📝 Notas Importantes

- ✅ O arquivo `.env` será criado pelo instalador
- ✅ O arquivo `.env.installer` é apenas um template temporário
- ✅ Após a instalação, o `.env` conterá suas configurações reais
- ✅ O instalador só pode ser executado uma vez
- ✅ Para reinstalar, delete o arquivo `storage/installed`
- ✅ Sempre use MySQL/MariaDB (não SQLite) para compatibilidade com cPanel/WHM

## 🎉 Pronto!

Após a instalação, você terá:
- ✅ Sistema totalmente configurado
- ✅ Banco de dados criado e populado
- ✅ Usuário administrador criado
- ✅ Sistema pronto para uso

Acesse o painel admin e comece a usar o HomeMechanic! 🚗🔧
