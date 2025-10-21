import api from './api';
import { AuthResponse, User } from '@/types';

export const authService = {
  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/login', {
      email,
      password,
    });
    
    const { user, token } = response.data;
    localStorage.setItem('auth_token', token);
    localStorage.setItem('user', JSON.stringify(user));
    
    return response.data;
  },

  async logout(): Promise<void> {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    
    try {
      await api.post('/logout');
    } catch (error) {
      console.log('Logout API call failed, but user is logged out locally');
    }
  },

  async getCurrentUser(): Promise<User> {
    const response = await api.get<{ data: User }>('/me');
    return response.data.data;
  },

  getStoredUser(): User | null {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  },

  isAuthenticated(): boolean {
    return !!localStorage.getItem('auth_token');
  },

  isAdmin(): boolean {
    const user = this.getStoredUser();
    return user?.role === 'admin';
  },
};
