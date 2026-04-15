#!/bin/bash

# Script de Deploy HomeMechanic System
# Servidor: 15.235.57.3
# Usuário: homemechanic

echo "🚀 Iniciando deploy do HomeMechanic System..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

# Verificar se git está instalado
if ! command -v git &> /dev/null; then
    error "Git não está instalado"
    exit 1
fi

# Conectar ao servidor e executar deploy
log "Conectando ao servidor 15.235.57.3..."

ssh homemechanic@15.235.57.3 << 'ENDSSH'
    echo "🔧 Executando deploy no servidor..."
    
    # Navegar para o diretório
    cd /home/homemechanic/public_html
    
    # Fazer backup se houver arquivos
    if [ "$(ls -A)" ]; then
        echo "📦 Fazendo backup dos arquivos existentes..."
        mkdir -p ../backups/$(date +%Y%m%d_%H%M%S)
        mv * ../backups/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
    fi
    
    # Clonar repositório (substitua pela URL do seu repositório)
    echo "📥 Clonando repositório..."
    git clone https://github.com/SEU_USUARIO/homemechanic-system.git .
    
    # Configurar permissões
    echo "🔐 Configurando permissões..."
    chmod -R 755 .
    chmod -R 777 storage
    chmod -R 777 bootstrap/cache
    
    # Verificar PHP
    echo "🐘 Verificando PHP..."
    php -v
    
    # Verificar extensões necessárias
    echo "🔍 Verificando extensões PHP..."
    php -m | grep -E "(pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd)"
    
    # Criar link simbólico para storage (se necessário)
    if [ ! -L "public/storage" ]; then
        echo "🔗 Criando link simbólico para storage..."
        ln -s ../storage/app/public public/storage
    fi
    
    echo "✅ Deploy concluído!"
    echo "🌐 Acesse seu domínio para iniciar a instalação"
    echo "📋 O sistema redirecionará automaticamente para /install"
    
ENDSSH

if [ $? -eq 0 ]; then
    log "✅ Deploy concluído com sucesso!"
    log "🌐 Acesse seu domínio para continuar a instalação"
    log "📋 Dados do banco:"
    log "   - Host: localhost"
    log "   - Banco: homemechanic_2026"
    log "   - Usuário: homemechanic_2026"
    log "   - Senha: homemechanic_2026"
else
    error "❌ Erro durante o deploy"
    exit 1
fi