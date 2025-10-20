# Feature Flag Service

Laravel 12 + Next.js 15 monorepo for feature flag management.

## Requirements

- Docker & Docker Compose

## Setup

**Easiest way:**
```bash
./deploy-local.sh
```

**Manual setup:**
1. Copy environment files:
   ```bash
   cp environments/local/backend.env backend/.env
   cp environments/local/frontend.env frontend/.env.local
   ```

2. Start services:
   ```bash
   cd environments/local
   docker compose --env-file docker.env up -d
   ```

3. Access:
   - Frontend: http://localhost:3000
   - Backend: http://localhost:8000

## Stop

```bash
cd environments/local
docker compose down
```