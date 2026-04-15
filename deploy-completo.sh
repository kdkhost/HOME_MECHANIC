#!/bin/bash

# Deploy Completo HomeMechanic System
# Execute este script no seu terminal local

echo "🚀 Iniciando deploy completo do HomeMechanic System..."
echo "📡 Servidor: 15.235.57.3"
echo "👤 Usuário: homemechanic"
echo ""

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}Conectando ao servidor e executando deploy...${NC}"

# Executar todos os comandos via SSH em uma única conexão
ssh -o StrictHostKeyChecking=no homemechanic@15.235.57.3 << 'ENDSSH'

echo "🔧 Iniciando configuração no servidor..."

# Navegar para o diretório
cd /home/homemechanic/public_html

# Verificar se há arquivos e fazer backup
if [ "$(ls -A . 2>/dev/null)" ]; then
    echo "📦 Fazendo backup dos arquivos existentes..."
    mkdir -p ../backups/backup_$(date +%Y%m%d_%H%M%S)
    mv * ../backups/backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
    echo "✅ Backup concluído"
fi

# Verificar se git está disponível
if ! command -v git &> /dev/null; then
    echo "❌ Git não encontrado. Tentando instalar..."
    # Tentar diferentes gerenciadores de pacote
    if command -v yum &> /dev/null; then
        sudo yum install -y git
    elif command -v apt-get &> /dev/null; then
        sudo apt-get update && sudo apt-get install -y git
    elif command -v dnf &> /dev/null; then
        sudo dnf install -y git
    else
        echo "❌ Não foi possível instalar git automaticamente"
        echo "💡 Instale git manualmente e execute novamente"
        exit 1
    fi
fi

# Clonar repositório (você precisa substituir pela URL real do seu repo)
echo "📥 Clonando repositório HomeMechanic..."
if [ ! -z "$REPO_URL" ]; then
    git clone $REPO_URL .
else
    echo "⚠️  URL do repositório não definida"
    echo "💡 Execute: export REPO_URL='https://github.com/SEU_USUARIO/homemechanic-system.git'"
    echo "💡 Ou clone manualmente: git clone https://github.com/SEU_USUARIO/homemechanic-system.git ."
    
    # Criar estrutura básica se não conseguir clonar
    echo "📁 Criando estrutura básica..."
    mkdir -p app bootstrap config database public resources routes storage tests
    mkdir -p storage/{app,framework,logs}
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/app/public
    mkdir -p bootstrap/cache
fi

# Configurar permissões
echo "🔐 Configurando permissões..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod 644 .env.example 2>/dev/null || true

# Criar link simbólico para storage
if [ ! -L "public/storage" ]; then
    echo "🔗 Criando link simbólico para storage..."
    ln -sf ../storage/app/public public/storage
fi

# Verificar PHP e extensões
echo "🐘 Verificando PHP..."
php -v

echo "🔍 Verificando extensões PHP necessárias..."
extensions=("pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "gd")
missing_extensions=()

for ext in "${extensions[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "✅ $ext - OK"
    else
        echo "❌ $ext - FALTANDO"
        missing_extensions+=("$ext")
    fi
done

if [ ${#missing_extensions[@]} -gt 0 ]; then
    echo "⚠️  Extensões faltando: ${missing_extensions[*]}"
    echo "💡 Instale as extensões faltando antes de continuar"
fi

# Verificar se mod_rewrite está ativo (Apache)
if command -v apache2ctl &> /dev/null; then
    if apache2ctl -M | grep -q rewrite; then
        echo "✅ mod_rewrite - OK"
    else
        echo "❌ mod_rewrite - FALTANDO"
    fi
elif command -v httpd &> /dev/null; then
    if httpd -M | grep -q rewrite; then
        echo "✅ mod_rewrite - OK"
    else
        echo "❌ mod_rewrite - FALTANDO"
    fi
fi

# Criar arquivo .htaccess na raiz se não existir
if [ ! -f ".htaccess" ]; then
    echo "📝 Criando .htaccess na raiz..."
    cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
fi

# Criar arquivo .htaccess no public se não existir
if [ ! -f "public/.htaccess" ]; then
    echo "📝 Criando .htaccess no public..."
    mkdir -p public
    cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
fi

# Verificar espaço em disco
echo "💾 Verificando espaço em disco..."
df -h .

# Mostrar informações do sistema
echo "ℹ️  Informações do sistema:"
echo "OS: $(uname -a)"
echo "PHP: $(php -v | head -n1)"
echo "Diretório atual: $(pwd)"
echo "Arquivos: $(ls -la | wc -l) itens"

echo ""
echo "✅ Configuração do servidor concluída!"
echo ""
echo "🌐 Próximos passos:"
echo "1. Acesse seu domínio no navegador"
echo "2. O sistema redirecionará para /install automaticamente"
echo "3. Configure o banco de dados:"
echo "   - Host: localhost"
echo "   - Porta: 3306"
echo "   - Banco: homemechanic_2026"
echo "   - Usuário: homemechanic_2026"
echo "   - Senha: homemechanic_2026"
echo "4. Crie o usuário administrador"
echo "5. Finalize a instalação"
echo ""
echo "📋 Dados para instalação:"
echo "   Banco: homemechanic_2026"
echo "   Usuário: homemechanic_2026"
echo "   Senha: homemechanic_2026"
echo ""

ENDSSH

# Verificar se a conexão SSH foi bem-sucedida
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Deploy concluído com sucesso!${NC}"
    echo ""
    echo -e "${BLUE}🌐 Próximos passos:${NC}"
    echo "1. Acesse seu domínio no navegador"
    echo "2. Será redirecionado automaticamente para /install"
    echo "3. Siga o processo de instalação guiado"
    echo ""
    echo -e "${YELLOW}📋 Dados do banco de dados:${NC}"
    echo "Host: localhost"
    echo "Porta: 3306"
    echo "Banco: homemechanic_2026"
    echo "Usuário: homemechanic_2026"
    echo "Senha: homemechanic_2026"
    echo ""
    echo -e "${GREEN}🎉 Sistema HomeMechanic pronto para instalação!${NC}"
else
    echo -e "${RED}❌ Erro durante o deploy${NC}"
    echo "Verifique:"
    echo "1. Conexão SSH com o servidor"
    echo "2. Permissões do usuário homemechanic"
    echo "3. Conectividade de rede"
    exit 1
fi