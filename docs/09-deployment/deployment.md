# 🚀 Deploy em Produção — Maternidade+

Instruções para implementação e atualização contínua no servidor de produção.

---

## 🌐 Dados do Servidor de Produção

- **URL de Produção**: `http://146.235.224.99/maternidade_plus/`
- **Endereço IP**: `146.235.224.99`
- **Diretório do Servidor**: `/var/www/html/maternidade_plus/`

---

## ⚙️ Script de Deploy Automatizado (`./deploy.sh`)

O deploy é acionado com um único comando na máquina local:

```bash
./deploy.sh
```

### O que o script realiza:
1. Compila os assets estáticos via Vite (`npm run build`).
2. Adiciona e comita alterações no Git (`git commit`).
3. Envia o código atualizado para o repositório remoto (`git push origin main`).
4. Conecta via SSH ao servidor de produção `146.235.224.99`.
5. Executa `git pull`, `composer install --no-dev`, `npm run build` remoto, `php artisan migrate --force` e limpa/otimiza os caches do Laravel (`route:cache`, `view:cache`, `config:cache`).
