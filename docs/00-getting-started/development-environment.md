# 💻 Ambiente de Desenvolvimento — Maternidade+

Guia passo a passo para configuração do ambiente local de desenvolvimento.

---

## 📋 Pré-requisitos
- **PHP**: >= 8.2 ou 8.3 com extensões `pdo`, `mbstring`, `openssl`, `curl`, `gd`.
- **Composer**: >= 2.x
- **Node.js & npm**: Node 18.x ou 20.x, npm >= 9.x
- **Base de Dados**: MySQL 8.0+ ou MariaDB 10.5+

---

## 🚀 Instalação Passo a Passo

```bash
# 1. Clonar o repositório
git clone git@github.com:filipeive/maternidade_plus.git
cd maternidade_plus

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências JavaScript & CSS
npm install

# 4. Configurar arquivo de ambiente (.env)
cp .env.example .env
php artisan key:generate

# 5. Configurar credenciais do banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maternidade_plus
DB_USERNAME=root
DB_PASSWORD=

# 6. Executar migrações e seeders de teste
php artisan migrate --seed

# 7. Compilar assets frontend
npm run dev

# 8. Iniciar o servidor local
php artisan serve
```

O servidor local ficará acessível em: `http://127.0.0.1:8000`.
