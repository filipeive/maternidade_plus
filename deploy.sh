#!/bin/bash
# ╔═══════════════════════════════════════════════════════╗
# ║  Maternidade+ — Script de Deploy para Produção       ║
# ║  Autor: Filipe dos Santos                            ║
# ║  Servidor: 146.235.224.99 (Oracle Cloud)             ║
# ╚═══════════════════════════════════════════════════════╝

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'
BOLD='\033[1m'

REMOTE_USER="ubuntu"
REMOTE_HOST="146.235.224.99"
REMOTE_PATH="/var/www/html/maternidade_plus"
BRANCH="main"

echo ""
echo -e "${BLUE}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║${NC}  ${BOLD}🚀 Maternidade+ — Deploy Pipeline Produção${NC}        ${BLUE}║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════════════════╝${NC}"
echo ""

# 1. Compilar assets com Vite
echo -e "${YELLOW}[1/5]${NC} Compilando assets frontend (Vite)..."
npm run build
echo -e "${GREEN}  ✓ Assets compilados com sucesso${NC}"

# 2. Verificar alterações pendentes
echo -e "${YELLOW}[2/5]${NC} Verificando alterações no Git..."
if [[ -n $(git status --porcelain) ]]; then
    echo -e "${YELLOW}  ⚠ Alterações não comitadas encontradas. Realizando commit...${NC}"
    git add -A
    git commit -m "feat(deploy): atualização de produção Maternidade+"
    echo -e "${GREEN}  ✓ Alterações comitadas com sucesso${NC}"
else
    echo -e "${GREEN}  ✓ Sem alterações pendentes${NC}"
fi

# 3. Enviar para GitHub
echo -e "${YELLOW}[3/5]${NC} Enviando para o GitHub (${BRANCH})..."
git push origin "$BRANCH"
echo -e "${GREEN}  ✓ GitHub atualizado${NC}"

# 4. Executar comandos de deploy no servidor de produção
echo -e "${YELLOW}[4/5]${NC} Atualizando código, dependências e caches no servidor remoto (${REMOTE_HOST})..."
ssh -o StrictHostKeyChecking=no "${REMOTE_USER}@${REMOTE_HOST}" "
    cd ${REMOTE_PATH} &&
    sudo git config --global --add safe.directory ${REMOTE_PATH} &&
    echo 'Puxando atualizações do GitHub...' &&
    sudo -u ubuntu git pull origin ${BRANCH} &&
    echo 'Instalando dependências via Composer...' &&
    export COMPOSER_ALLOW_SUPERUSER=1 &&
    sudo -u ubuntu composer install --no-interaction --prefer-dist --optimize-autoloader &&
    echo 'Executando migrações da base de dados...' &&
    sudo -u ubuntu php artisan migrate --force &&
    echo 'Limpando e otimizando caches Laravel...' &&
    sudo -u ubuntu php artisan config:clear &&
    sudo -u ubuntu php artisan route:clear &&
    sudo -u ubuntu php artisan view:clear &&
    sudo -u ubuntu php artisan cache:clear &&
    sudo -u ubuntu php artisan config:cache &&
    sudo -u ubuntu php artisan route:cache
"
echo -e "${GREEN}  ✓ Servidor de produção atualizado com sucesso${NC}"

# 5. Conclusão
echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║${NC}  ${BOLD}✅ Deploy concluído com sucesso em Produção!${NC}     ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}  🌐 ${BLUE}http://146.235.224.99/maternidade_plus/${NC}         ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}  📅 $(date '+%d/%m/%Y às %H:%M:%S')                       ${GREEN}║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
