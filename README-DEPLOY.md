# 🚀 Deploy HomeMechanic System

## 📋 Informações do Servidor
- **IP:** 15.235.57.3
- **Usuário:** homemechanic
- **Banco:** homemechanic_2026
- **Usuário DB:** homemechanic_2026
- **Senha DB:** homemechanic_2026
- **Diretório:** /home/homemechanic/public_html

## 🔧 Pré-requisitos no Servidor
- PHP 8.4+
- MySQL/MariaDB
- Extensões PHP: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- mod_rewrite habilitado
- Permissões de escrita em storage/ e bootstrap/cache/

## 📦 Opções de Deploy

### Opção 1: Deploy Manual via SSH

1. **Conectar ao servidor:**
```bash
ssh homemechanic@15.235.57.3
```

2. **Navegar para o diretório:**
```bash
cd /home/homemechanic/public_html
```

3. **Fazer backup (se necessário):**
```bash
mkdir -p ../backups/$(date +%Y%m%d_%H%M%S)
mv * ../backups/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
```

4. **Clonar o repositório:**
```bash
git clone https://github.com/SEU_USUARIO/homemechanic-system.git .
```

5. **Configurar permissões:**
```bash
chmod -R 755 .
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

### Opção 2: Upload via FTP/SFTP

1. Compactar o projeto localmente (excluindo .git)
2. Fazer upload via FTP para `/home/homemechanic/public_html`
3. Descompactar no servidor
4. Configurar permissões via painel de controle ou SSH

### Opção 3: Deploy Automatizado

Execute o script `deploy.sh` (em sistemas Unix/Linux):
```bash
./deploy.sh
```

## 🌐 Processo de Instalação

1. **Acessar o domínio**
   - O sistema detectará que não está instalado
   - Redirecionará automaticamente para `/install`

2. **Verificação de Requisitos**
   - O instalador verificará todos os requisitos do servidor
   - Mostrará status verde/vermelho para cada item

3. **Configuração do Sistema**
   - **Banco de Dados:**
     - Host: `localhost`
     - Porta: `3306`
     - Nome: `homemechanic_2026`
     - Usuário: `homemechanic_2026`
     - Senha: `homemechanic_2026`
   
   - **Administrador:**
     - Nome: `Administrador`
     - Email: `admin@seudominio.com.br`
     - Senha: [definir senha segura]
   
   - **Empresa:**
     - Nome: `HomeMechanic`
     - Descrição: `Sistema de gestão para oficinas especializadas`

4. **Finalização**
   - O sistema criará o arquivo `.env`
   - Executará as migrations
   - Criará o usuário administrador
   - Redirecionará para o painel admin

## ✅ Verificações Pós-Instalação

1. **Testar Login Admin:**
   - Acessar `/admin/login`
   - Fazer login com as credenciais criadas

2. **Verificar Funcionalidades:**
   - Dashboard com estatísticas
   - Sistema de upload
   - Documentação integrada
   - Módulos de gestão

3. **Verificar Logs:**
   - Checar `storage/logs/laravel.log` para erros

## 🔧 Troubleshooting

### Erro de Permissões
```bash
sudo chown -R homemechanic:homemechanic /home/homemechanic/public_html
chmod -R 755 /home/homemechanic/public_html
chmod -R 777 /home/homemechanic/public_html/storage
chmod -R 777 /home/homemechanic/public_html/bootstrap/cache
```

### Erro de Extensões PHP
Verificar se todas as extensões estão instaladas:
```bash
php -m | grep -E "(pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd)"
```

### Erro de Banco de Dados
- Verificar se o banco `homemechanic_2026` existe
- Testar conexão com as credenciais fornecidas
- Verificar se o usuário tem permissões adequadas

### Erro 500
- Verificar logs em `storage/logs/laravel.log`
- Verificar permissões de escrita
- Verificar configuração do servidor web

## 📞 Suporte

Se encontrar problemas durante o deploy:
1. Verificar os logs de erro
2. Conferir as permissões de arquivo
3. Validar configurações do servidor
4. Testar requisitos do sistema

## 🔐 Segurança Pós-Deploy

1. **Alterar senhas padrão**
2. **Configurar HTTPS**
3. **Configurar backup automático**
4. **Atualizar regularmente**
5. **Monitorar logs de acesso**

---

**Sistema HomeMechanic v1.0.0**  
Desenvolvido com Laravel 13 e PHP 8.4  
Compatível com MySQL/MariaDB