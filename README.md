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

## Architecture

- **Backend**: Laravel 12 with Domain-Driven Design
  - `app/Domain/FeatureFlags/` - Core business logic
  - `app/Infrastructure/` - Database repositories
  - `app/Http/Controllers/Api/` - REST API endpoints
- **Frontend**: Next.js 15 with TypeScript and Tailwind CSS
- **Database**: PostgreSQL 16 with migrations and seeders
- **Cache**: Redis 7 with 60-second TTL

## Authentication

**Admin User:**
- Email: `admin@example.com`
- Password: `password`

**Regular User:**
- Email: `user@example.com`
- Password: `password`

## API Endpoints

**Authentication:**
```
POST   /api/login                   # Login (returns token)
POST   /api/logout                  # Logout (requires auth)
GET    /api/me                      # Get current user (requires auth)
```

**Public (no auth required):**
```
POST   /api/feature-flags/{key}/evaluate  # Detailed evaluation
POST   /api/feature-flags/{key}/check     # Simple enabled check
GET    /api/feature-flags/active          # Get active flags for context
```

**Admin only (requires admin role):**
```
# Feature Flag Management
GET    /api/admin/feature-flags           # List all flags
POST   /api/admin/feature-flags           # Create new flag
GET    /api/admin/feature-flags/{key}     # Get specific flag
PUT    /api/admin/feature-flags/{key}     # Update flag
DELETE /api/admin/feature-flags/{key}     # Delete flag
GET    /api/admin/feature-flags/{key}/analytics # Get flag analytics
GET    /api/admin/feature-flags/analytics/user-history # Get user flag history

# Car Report Management
GET    /api/admin/car-reports             # List all reports
POST   /api/admin/car-reports             # Create new report
GET    /api/admin/car-reports/{id}        # Get specific report
PUT    /api/admin/car-reports/{id}        # Update report
DELETE /api/admin/car-reports/{id}        # Delete report
GET    /api/admin/car-reports/status/{status} # Filter by status
```

## Rollout Types

- **Boolean**: Simple on/off flags
- **Percentage**: Gradual rollout (0-100%)
- **Scheduled**: Time-based activation
- **User List**: Specific users/roles

## Feature Flag Strategy

### Handling Flagged Components After Disable

When a user sees a flagged component and the flag is later disabled:

1. **Client-Side Validation**: Check flag status before rendering
2. **Graceful Degradation**: Show fallback UI when disabled
3. **Real-time Updates**: WebSocket/polling for flag changes
4. **Error Boundaries**: Handle flag-related errors

```javascript
const handleFeatureAction = async () => {
  const isEnabled = await checkFeatureFlag('premium_feature');
  if (!isEnabled) {
    showUpgradePrompt();
    return;
  }
  // Proceed with feature
};
```

### Caching Strategy

- Redis caching with 60-second TTL
- Context-aware cache keys
- Automatic cache invalidation
- High-traffic ready

### Monitoring & Analytics

- Decision logging to database
- Analytics endpoints for usage tracking
- User flag history monitoring
- Real-time enable/disable stats

## Production Ready

- Domain-driven architecture
- Laravel Sanctum authentication
- Role-based access control
- Redis caching with TTL
- Database migrations and seeders
- Clean API design
- Feature flag decision logging
- Analytics and monitoring