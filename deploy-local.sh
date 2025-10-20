#!/bin/bash

echo "Setting up Feature Flag Service locally..."

# Copy environment files
echo "Copying environment files..."
cp environments/local/backend.env backend/.env
cp environments/local/frontend.env frontend/.env.local

echo "Starting Docker services..."
cd environments/local
docker compose --env-file docker.env up -d

echo ""
echo "Setup complete!"
echo ""
echo "Access your applications:"
echo "   Frontend: http://localhost:3000"
echo "   Backend:  http://localhost:8000"
echo ""
echo "To stop services, run:"
echo "   cd environments/local && docker compose down"
