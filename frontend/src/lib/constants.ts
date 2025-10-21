export const HTTP_STATUS = {
  OK: 200,
  CREATED: 201,
  BAD_REQUEST: 400,
  UNAUTHORIZED: 401,
  FORBIDDEN: 403,
  NOT_FOUND: 404,
  UNPROCESSABLE_ENTITY: 422,
  INTERNAL_SERVER_ERROR: 500,
} as const;

export const FEATURE_FLAGS = {
  UPLOAD_PHOTOS: 'upload_photos',
  PREMIUM_ANALYTICS: 'premium_analytics',
  BETA_DASHBOARD: 'beta_dashboard',
  AI_DAMAGE_ASSESSMENT: 'ai_damage_assessment',
  MOBILE_APP_INTEGRATION: 'mobile_app_integration',
} as const;

export const USER_ROLES = {
  ADMIN: 'admin',
  USER: 'user',
} as const;

export const REPORT_STATUS = {
  PENDING: 'pending',
  IN_PROGRESS: 'in_progress',
  COMPLETED: 'completed',
  REJECTED: 'rejected',
} as const;

export const DAMAGE_TYPES = {
  MINOR: 'minor',
  MODERATE: 'moderate',
  SEVERE: 'severe',
  TOTAL_LOSS: 'total_loss',
} as const;

export const CACHE_TTL = {
  FEATURE_FLAGS: 30000, // 30 seconds
  ACTIVE_FLAGS: 60000,  // 60 seconds
} as const;

export const ROUTES = {
  HOME: '/',
  LOGIN: '/login',
  REPORTS: '/reports',
  ADMIN: '/admin',
} as const;
