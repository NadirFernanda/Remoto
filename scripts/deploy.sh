#!/bin/bash
#
# deploy.sh — Script de deploy para produção
# Uso: bash scripts/deploy.sh
#
cd /var/www/24horas
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
# Correr como www-data para evitar Permission denied em storage/framework/views/
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache

echo "🗑️  A limpar cache de aplicação (dados desatualizados após deploy)..."
sudo -u www-data php artisan cache:clear

echo "🗄️  A correr migrações..."
sudo -u www-data php artisan migrate --force

echo "� A garantir symlink de storage..."
sudo -u www-data php artisan storage:link 2>/dev/null || true

echo "📁 A garantir permissões em storage/app/livewire-tmp..."
sudo -u www-data mkdir -p storage/app/livewire-tmp
sudo chmod -R 775 storage bootstrap/cache

echo "�🔁 A reiniciar workers..."
sudo -u www-data php artisan queue:restart

echo "🔁 A reiniciar serviços..."
sudo systemctl restart php8.4-fpm 2>/dev/null || sudo systemctl restart php-fpm 2>/dev/null || true
sudo systemctl reload nginx

echo "🔥 A pré-aquecer cache (cold start prevention)..."
sudo -u www-data php artisan cache:warm

echo "✅ Deploy concluído com sucesso!"