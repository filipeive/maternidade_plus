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

# 1. Compilação de Assets Vite locais se necessário
echo -e "${YELLOW}[1/5]${NC} Compilando assets frontend (Vite)..."
npm run build
echo -e "${GREEN}  ✓ Assets compilados com sucesso${NC}"

# 2. Verificar alterações locais pendentes
echo -e "${YELLOW}[2/5]${NC} Verificando alterações locais no Git..."
if [[ -n $(git status --porcelain) ]]; then
    echo -e "${YELLOW}  ⚠ Alterações não comitadas encontradas:${NC}"
    git status --short
    git add -A
    git commit -m "feat(deploy): atualização automática de deploy Maternidade+"
    echo -e "${GREEN}  ✓ Alterações comitadas automaticamente${NC}"
else
    echo -e "${GREEN}  ✓ Nenhuma alteração pendente localmente${NC}"
fi

# 3. Enviar alterações para o repositório remoto (GitHub)
echo -e "${YELLOW}[3/5]${NC} Enviando atualizações para o GitHub (${BRANCH})..."
git push origin "$BRANCH"
echo -e "${GREEN}  ✓ GitHub atualizado${NC}"

# 4. Executar script de deploy no servidor remoto via SSH
echo -e "${YELLOW}[4/5]${NC} Executando script de deploy no servidor remoto (${REMOTE_HOST})..."
ssh -o StrictHostKeyChecking=no "${REMOTE_USER}@${REMOTE_HOST}" \
    "bash ${REMOTE_PATH}/deploy.sh"

# 5. Conclusão
echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║${NC}  ${BOLD}✅ Deploy concluído com sucesso em Produção!${NC}     ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}  🌐 ${BLUE}http://146.235.224.99/maternidade_plus/${NC}         ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}  📅 $(date '+%d/%m/%Y às %H:%M:%S')                       ${GREEN}║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
