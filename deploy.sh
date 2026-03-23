#!/bin/bash

set -e

PROJECT_DIR="/var/www/saproweb"
BRANCH="main"

echo "==> Indo para o projeto..."
cd $PROJECT_DIR

echo "==> Verificando status do git..."
git status

echo "==> Baixando atualizações do GitHub..."
git fetch origin

echo "==> Resetando para a branch remota..."
git reset --hard origin/$BRANCH

echo "==> Instalando dependências do Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Limpando caches antigos..."
php artisan optimize:clear

echo "==> Recriando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Reiniciando PHP-FPM..."
systemctl restart php8.3-fpm

echo "==> Deploy finalizado com sucesso!"