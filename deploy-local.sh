#!/bin/bash

set -e

echo "Setting up Feature Flag Service..."
echo ""

echo "Copying environment files..."
cp environments/local/backend.env backend/.env
cp environments/local/frontend.env frontend/.env.local

echo "Starting Docker services..."
cd environments/local
docker compose --env-file docker.env up -d

echo "Waiting for services to be ready..."
sleep 10

echo "Running backend setup..."

# Check if vendor directory exists, if not install dependencies
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

echo ""
echo "Setup complete!"
echo ""
echo "Access the application:"
echo "  Frontend: http://localhost:3000"
echo "  Backend API: http://localhost:8000/api"
echo ""
echo "Login credentials:"
echo "  Admin: admin@example.com / password"
echo "  User: user@example.com / password"
echo ""
echo "To stop: cd environments/local && docker compose down"
