#!/bin/bash

set -e

echo "Setting up Feature Flag Service for Production..."

echo "Copying environment files..."
cp environments/production/backend.env backend/.env
cp environments/production/frontend.env frontend/.env.local

echo "Starting Docker services..."
cd environments/local
docker compose --env-file docker.env up -d

echo "Waiting for services to be ready..."
sleep 10

echo "Running backend setup..."

if ! docker exec feature-flag-backend test -d /var/www/html/vendor; then
  echo "Installing PHP dependencies..."
  docker exec feature-flag-backend composer install --no-dev --optimize-autoloader
fi

docker exec feature-flag-backend php artisan key:generate

sleep 5

echo "Setting up database..."
docker exec feature-flag-backend php artisan migrate:fresh --seed --force

echo "Creating storage link..."
docker exec feature-flag-backend php artisan storage:link

echo "Setting up Nginx..."
if command -v nginx &> /dev/null; then
    sudo cp nginx.conf /etc/nginx/sites-available/feature-flag-service
    sudo ln -sf /etc/nginx/sites-available/feature-flag-service /etc/nginx/sites-enabled/
    sudo rm -f /etc/nginx/sites-enabled/default
    sudo nginx -t && sudo systemctl restart nginx
    echo "Nginx configured successfully"
else
    echo "Nginx not found. Install with: sudo apt install nginx"
fi

echo ""
echo "Production setup complete!"
echo ""
echo "Access the application:"
echo "  App: http://44.249.12.142"
echo "  Direct Frontend: http://44.249.12.142:3000"
echo "  Direct Backend: http://44.249.12.142:8000/api"
echo ""
echo "Login credentials:"
echo "  Admin: admin@example.com / password"
echo "  User: user@example.com / password"
echo ""
echo "To stop: cd environments/local && docker compose down"
