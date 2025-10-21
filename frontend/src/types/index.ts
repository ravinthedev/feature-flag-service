export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

export interface FeatureFlag {
  key: string;
  name: string;
  description?: string;
  is_enabled: boolean;
  rollout_type: 'boolean' | 'percentage' | 'scheduled' | 'user_list';
  rollout_value?: any;
  starts_at?: string;
  ends_at?: string;
}

export interface CarReport {
  id: number;
  car_model: string;
  description: string;
  damage_type: 'minor' | 'moderate' | 'severe' | 'total_loss';
  photo_url?: string;
  status: 'pending' | 'approved' | 'rejected';
  created_at: string;
  updated_at: string;
}

export interface EvaluationResult {
  enabled: boolean;
  reason: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}
