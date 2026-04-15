# Deploy HomeMechanic System

## Informações do Servidor
- **IP:** 15.235.57.3
- **Usuário:** homemechanic
- **Banco:** homemechanic_2026
- **Usuário DB:** homemechanic_2026
- **Senha DB:** homemechanic_2026
- **Diretório:** /home/homemechanic/public_html

## Passos para Deploy

### 1. Conectar via SSH
```bash
ssh homemechanic@15.235.57.3
```

### 2. Navegar para o diretório
```bash
cd /home/homemechanic/public_html
```

### 3. Fazer backup (se houver arquivos existentes)
```bash
mkdir -p ../backups
mv * ../backups/ 2>/dev/null || true
```

### 4. Clonar o repositório
```bash
git clone https://github.com/SEU_USUARIO/homemechanic-system.git .
```

### 5. Configurar permissões
```bash
chmod -R 755 .
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

### 6. Verificar PHP e extensões
```bash
php -v
php -m | grep -E "(pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd)"
```

### 7. Acessar o domínio
- O sistema redirecionará automaticamente para `/install`
- Seguir o processo de instalação guiado

## Configurações do Instalador

### Banco de Dados:
- **Host:** localhost
- **Porta:** 3306
- **Nome do Banco:** homemechanic_2026
- **Usuário:** homemechanic_2026
- **Senha:** homemechanic_2026

### Administrador:
- **Nome:** Administrador
- **Email:** marcelobradrj@gmail.com
- **Senha:** 83388601Mm...

### Empresa:
- **Nome:** HomeMechanic
- **Descrição:** Sistema de gestão para oficinas especializadas

## Verificações Pós-Instalação

1. **Testar acesso ao painel:** /admin/login
2. **Verificar logs:** storage/logs/laravel.log
3. **Testar upload de arquivos**
4. **Verificar documentação:** /admin/documentacao

## Troubleshooting

### Se der erro de permissões:
```bash
sudo chown -R homemechanic:homemechanic /home/homemechanic/public_html
chmod -R 755 /home/homemechanic/public_html
chmod -R 777 /home/homemechanic/public_html/storage
chmod -R 777 /home/homemechanic/public_html/bootstrap/cache
```

### Se der erro de extensões PHP:
Verificar se todas as extensões estão instaladas no servidor.

### Se der erro de banco:
Verificar se o banco de dados existe e as credenciais estão corretas.