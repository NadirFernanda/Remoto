#!/bin/bash
#
# deploy.sh — Script de deploy para produção
# Uso: bash scripts/deploy.sh
#
set -e

echo "🔄 A fazer pull do repositório..."
git fetch origin main
git reset --hard origin/main

echo "📦 A instalar dependências PHP..."
composer install --no-dev --optimize-autoloader

echo "📦 A instalar e compilar assets..."
npm install --prefer-offline
npm run build

echo "🔧 A limpar e RECONSTRUIR todos os caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "🗑️  A limpar cache de aplicação (dados desatualizados após deploy)..."
php artisan cache:clear

echo "🗄️  A correr migrações..."
php artisan migrate --force

echo "🔁 A reiniciar workers..."
php artisan queue:restart

echo "🔁 A reiniciar serviços..."
sudo systemctl restart php8.4-fpm 2>/dev/null || sudo systemctl restart php-fpm 2>/dev/null || true
sudo systemctl reload nginx

echo "✅ Deploy concluído com sucesso!"
