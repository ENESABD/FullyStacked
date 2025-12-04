#!/bin/bash
set -e

# Colors
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Starting Docker deployment...${NC}"

# Copy Docker environment file
cp .env.docker .env

# Build and start containers
echo -e "${GREEN}Building and starting containers...${NC}"
docker compose up -d --build

# Wait for MySQL
echo -e "${GREEN}Waiting for MySQL to be ready...${NC}"
sleep 15

# Install dependencies
echo -e "${GREEN}Installing Composer dependencies...${NC}"
docker exec laravel-php composer install

# Generate key
echo -e "${GREEN}Generating application key...${NC}"
docker exec laravel-php php artisan key:generate

# Fix permissions
echo -e "${GREEN}Fixing permissions...${NC}"
docker exec -u root laravel-php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Clear config cache
echo -e "${GREEN}Clearing config cache...${NC}"
docker exec laravel-php php artisan config:clear

# Run migrations and seeders
echo -e "${GREEN}Running migrations and seeders...${NC}"
docker exec laravel-php php artisan migrate:fresh --seed

echo -e "${GREEN}🌐 Your Laravel application is ready!${NC}"
echo -e "${GREEN}   Visit: http://localhost:8080${NC}"
