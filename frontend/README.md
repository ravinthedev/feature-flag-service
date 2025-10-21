# Feature Flag Service - Frontend

A Next.js 15 frontend application for the Feature Flag Service with admin panel and client interface.

## Features

### Admin Interface
- **Feature Flag Management**: Create, edit, delete, and view feature flags
- **Analytics Dashboard**: View feature flag usage statistics and decision logs
- **Role-based Access**: Admin-only access to management features

### Client Interface
- **Car Damage Reports**: Submit, view, edit, and manage car damage reports
- **Feature Flag Integration**: Components conditionally rendered based on feature flags
- **Advanced Search**: Premium search functionality (feature flagged)
- **Mobile App Integration**: Mobile app download prompts (feature flagged)

### Feature Flag Components
1. **Photo Upload**: File upload functionality for car reports
2. **AI Damage Assessment**: AI-powered damage analysis and cost estimation
3. **Premium Analytics**: Advanced reporting and analytics features
4. **Beta Dashboard**: Enhanced UI with latest features
5. **Mobile App Integration**: Mobile app download and QR code features

## Technology Stack

- **Next.js 15** with App Router
- **TypeScript** for type safety
- **Tailwind CSS** for styling
- **React Hook Form** with Zod validation
- **Axios** for API communication
- **Lucide React** for icons

## Getting Started

1. Install dependencies:
```bash
npm install
```

2. Set up environment variables:
```bash
# Create .env.local
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

3. Run the development server:
```bash
npm run dev
```

4. Open [http://localhost:3000](http://localhost:3000) in your browser.

## Authentication

### Demo Credentials
- **Admin**: admin@example.com / password
- **User**: user@example.com / password

### Role-based Access
- **Admin users** can access `/admin` for feature flag management
- **Regular users** can access `/reports` for car damage reports

## Feature Flag Strategy

### Handling Disabled Features
The application includes robust handling for when feature flags are disabled:

1. **Graceful Degradation**: Components show fallback UI when disabled
2. **Real-time Updates**: Feature flags are checked on each interaction
3. **Error Boundaries**: Proper error handling for flag-related failures
4. **User Feedback**: Clear messaging when features become unavailable

### Caching Strategy
- Feature flag states are cached client-side for performance
- Automatic cache invalidation on flag changes
- Fallback to default values when cache is unavailable

## API Integration

The frontend integrates with the Laravel backend through:

- **Authentication API**: Login/logout and user management
- **Feature Flag API**: Flag evaluation and management
- **Car Reports API**: CRUD operations for damage reports
- **Analytics API**: Usage statistics and decision logging

## Component Architecture

### Core Components
- `FeatureFlagGuard`: Handles feature flag state changes gracefully
- `useFeatureFlag`: React hook for feature flag evaluation
- `Layout`: Main application layout with navigation
- `CarReportForm`: Form for creating/editing car reports
- `FeatureFlagForm`: Admin form for managing feature flags

### Feature Flag Components
- `AdvancedSearch`: Premium search functionality
- `MobileAppIntegration`: Mobile app download features
- `CarReportDetail`: Enhanced report details with AI features

## Development

### Available Scripts
- `npm run dev`: Start development server
- `npm run build`: Build for production
- `npm run start`: Start production server
- `npm run lint`: Run ESLint

### Project Structure
```
src/
├── app/                 # Next.js app router pages
├── components/          # React components
├── hooks/              # Custom React hooks
├── lib/                # API and utility functions
├── types/              # TypeScript type definitions
└── config/             # Configuration files
```

## Production Deployment

1. Build the application:
```bash
npm run build
```

2. Start the production server:
```bash
npm run start
```

The application will be available at `http://localhost:3000`.