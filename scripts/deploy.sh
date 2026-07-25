#!/bin/bash
# Script de mise à jour de l'API en production (LWS cPanel).
# À lancer depuis le Terminal cPanel, dans le dossier du projet :
#   cd /home/c2613905c/public_html/api-daoukro
#   bash scripts/deploy.sh
#
# Ne touche jamais à .env, storage/ ou vendor/ (non suivis par Git).
set -e

PHP=/opt/alt/php83/usr/bin/php
COMPOSER=/usr/local/bin/composer

echo "== 1. Récupération du code =="
git pull origin main

echo "== 2. Dépendances =="
# Le wrapper "composer" seul utilise le PHP 8.2 par défaut du serveur — on force
# le binaire PHP 8.3 en l'invoquant explicitement (voir piège n°1 du runbook).
$PHP $COMPOSER install --no-dev --optimize-autoloader

echo "== 3. Purge des caches (obligatoire avant toute vérif) =="
$PHP artisan config:clear
$PHP artisan cache:clear
$PHP artisan route:clear
$PHP artisan view:clear

echo "== 4. Migrations (si nouvelles tables/colonnes) =="
$PHP artisan migrate --force

echo "== 5. Mise en cache production =="
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

echo "== Terminé. =="
