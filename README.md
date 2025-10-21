# Feature Flag Service

A car damage reporting system with feature flag management. Built with Laravel and Next.js.

## What is this?

This is a demo application showing how to implement feature flags in a real-world scenario. Users can submit car damage reports, and admins can toggle features on/off without deploying new code.

**Live Demo:** [https://54.190.56.245:3000](https://54.190.56.245:3000)

*Currently deployed on AWS EC2 with Docker. For actual production usage, recommend AWS ECS or similar container orchestration.*

## Quick Start

**Clone the repository:**
```bash
git clone https://github.com/ravinthedev/feature-flag-service.git
cd feature-flag-service
```

**One-click setup:**
```bash
chmod +x deploy-local.sh
./deploy-local.sh
```

That's it. Everything will be set up and running.

**Access:**
- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api

**Login:**
- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

## Manual Setup

If you prefer to set things up manually:

```bash
# Copy env files
cp environments/local/backend.env backend/.env
cp environments/local/frontend.env frontend/.env.local

# Start containers
cd environments/local
docker compose --env-file docker.env up -d

# Setup backend
docker exec feature-flag-backend php artisan key:generate
docker exec feature-flag-backend php artisan migrate
docker exec feature-flag-backend php artisan db:seed
docker exec feature-flag-backend php artisan storage:link
```

## Architecture

This project uses a domain-driven design approach. Here's how it's organized:

### Backend (Laravel)
```
app/
├── Domain/              # Business logic (framework-independent)
│   ├── FeatureFlags/    # Flag evaluation, entities
│   └── Reports/         # Car report entities
├── Infrastructure/      # Database, external services
│   └── Repositories/    # Data access layer
├── Http/
│   ├── Controllers/     # API endpoints
│   ├── Requests/        # Validation
│   ├── Resources/       # Response formatting
│   └── Middleware/      # Auth, CORS
└── DTOs/                # Data transfer objects
```

**Key components:**
- **Domain Layer**: Pure business logic, no Laravel dependencies
- **Repositories**: Handle database queries
- **Services**: Coordinate between domain and infrastructure
- **Controllers**: HTTP request handling
- **DTOs**: Type-safe data transfer

### Frontend (Next.js)
```
src/
├── app/                 # Pages (App Router)
├── components/          # React components
├── hooks/               # Custom hooks (useFeatureFlag)
├── lib/                 # API clients, utilities
└── types/               # TypeScript types
```

## Features

**Feature Flag Management:**
- Boolean (on/off)
- Percentage rollout (gradual release)
- User-specific (by email or role)
- Scheduled (time-based activation)

**Car Damage Reports:**
- Submit damage reports
- Upload photos (behind feature flag)
- Filter by status
- Admin moderation

**Other:**
- Redis caching (60s TTL)
- Role-based access control
- Decision logging & analytics
- Real-time flag evaluation

## How It Works

**Feature Flags:**
1. Admin creates/edits flags via admin panel
2. Flags are cached in Redis for 60 seconds
3. Frontend checks flag status before rendering
4. Every decision is logged for analytics

**Example:**
```javascript
const { enabled } = useFeatureFlag('upload_photos');

if (enabled) {
  // Show photo upload field
}
```

**Handling Disabled Features:**
When a user sees a feature and it gets disabled:
- Client validates flag status before actions
- Shows modal explaining feature is unavailable
- Prevents API calls to disabled features
- Graceful fallback to basic functionality

## Caching Strategy

**Redis caching with smart invalidation:**
- Feature flags cached for 60 seconds
- Cache keys include user context (role, email)
- Auto-invalidate when admin updates flags
- Reduces database load for high traffic

**Why 60 seconds?**
Balance between performance and real-time updates. For instant updates, reduce TTL or use WebSockets.

## API Endpoints

**Public (no auth):**
```
POST /api/feature-flags/{key}/check       # Check if flag is enabled
POST /api/feature-flags/{key}/evaluate    # Detailed evaluation
```

**Authenticated:**
```
POST /api/login                            # Get auth token
GET  /api/car-reports                      # List reports
POST /api/car-reports                      # Create report
```

**Admin only:**
```
GET    /api/admin/feature-flags            # List all flags
POST   /api/admin/feature-flags            # Create flag
PUT    /api/admin/feature-flags/{key}      # Update flag
DELETE /api/admin/feature-flags/{key}      # Delete flag
GET    /api/admin/feature-flags/{key}/analytics  # Usage stats
```

## Testing the System

**Try the live demo first:**
- Visit: [https://54.190.56.245:3000](https://54.190.56.245:3000)
- Login with `admin@example.com` / `password` or `user@example.com` / `password`
- Test feature flags and car reports

**Or test locally:**

**1. Login as admin:**
- Go to http://localhost:3000/login
- Use `admin@example.com` / `password`
- You'll see the admin dashboard

**2. Toggle a feature:**
- Go to Admin → Feature Flags
- Click Edit on "Photo Upload"
- Disable it and save
- Open a new report - photo upload is gone

**3. Try percentage rollout:**
- Edit "Advanced Search" flag
- Set rollout to 25%
- Refresh as different users - some see it, some don't

**4. Test user-specific flags:**
- Edit "Beta Dashboard" flag
- Add your email to allowed users
- Only you and other allowed users see it

## Requirements

- Docker & Docker Compose
- That's it!

## Stack

- **Backend:** Laravel 12 (PHP 8.3)
- **Frontend:** Next.js 15 (React 19)
- **Database:** PostgreSQL 16
- **Cache:** Redis 7
- **Auth:** Laravel Sanctum

## Stop Services

```bash
cd environments/local
docker compose down
```

To remove all data:
```bash
docker compose down -v
```

## Improvements

This was built in a day. Here's what could be added:

- Comprehensive test coverage (PHPUnit, Pest, Jest)
- WebSocket for real-time flag updates
- A/B testing metrics
- Flag dependency management
- Audit logs for compliance
- CI/CD pipeline
- Production deployment configs
- Rate limiting
- API documentation (OpenAPI)
- Monitoring & alerting

## License

MIT
