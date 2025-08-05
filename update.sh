#!/bin/bash
echo "Atualizando Sistema Maternidade+..."
php artisan down
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
php artisan up
echo "Sistema atualizado com sucesso!"
