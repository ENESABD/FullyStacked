#!/bin/bash

# GiftList Automated Deployment Script
# Based on Module 2 Lecture Guidelines

set -e # Exit on error

# Configuration
DB_NAME="laravel_app"
DB_USER="laravel_user"
DB_PASS="password123"

echo "========================================"
echo "   GiftList One-Command Deployment"
echo "========================================"

# 1. Database Setup
echo "[1/5] Setting up Database..."
if command -v mysql &> /dev/null; then
    echo "To ensure the database and user exist, we need MySQL root access."
    echo "If you are on WSL/Ubuntu, you likely need to use sudo (leave password empty)."
    echo -n "Enter MySQL root password (leave empty to use 'sudo mysql'): "
    read -s MYSQL_ROOT_PASS
    echo ""

    SQL_COMMANDS="CREATE DATABASE IF NOT EXISTS $DB_NAME; CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS'; GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

    if [ -z "$MYSQL_ROOT_PASS" ]; then
        echo "Attempting to create database using sudo..."
        # We use sudo mysql to execute the commands
        sudo mysql -e "$SQL_COMMANDS"
    else
        echo "Attempting to create database using provided password..."
        mysql -u root -p"$MYSQL_ROOT_PASS" -e "$SQL_COMMANDS"
    fi
    echo "Database configured."
else
    echo "MySQL client not found. Skipping DB creation."
fi

# 2. Install Dependencies
echo "[2/5] Installing Composer Dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. Environment Configuration
echo "[3/5] Configuring Environment..."
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
    php artisan key:generate
fi

# Force DB credentials in .env
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
sed -i "s/DB_HOST=.*/DB_HOST=127.0.0.1/" .env
sed -i "s/DB_PORT=.*/DB_PORT=3306/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# 4. Migrations & Seeding
echo "[4/5] Running Migrations and Seeders..."
php artisan migrate:fresh --seed --force

# 5. Start Server
echo "[5/5] Starting Server..."
echo "----------------------------------------"
echo "Application will be available at: http://localhost:8000"
echo "Press Ctrl+C to stop."
echo "----------------------------------------"

php artisan serve --host=0.0.0.0
