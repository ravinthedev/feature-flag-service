import api from './api';
import { FeatureFlag, EvaluationResult, ApiResponse } from '@/types';

export const featureFlagService = {
  async getActiveFlags(): Promise<FeatureFlag[]> {
    const response = await api.get<ApiResponse<FeatureFlag[]>>('/feature-flags/active');
    return response.data.data;
  },

  async checkFlag(key: string, context: Record<string, any> = {}): Promise<boolean> {
    const response = await api.post<{ key: string; enabled: boolean }>(
      `/feature-flags/${key}/check`,
      { context }
    );
    return response.data.enabled;
  },

  async evaluateFlag(key: string, context: Record<string, any> = {}): Promise<EvaluationResult> {
    const response = await api.post<ApiResponse<EvaluationResult>>(
      `/feature-flags/${key}/evaluate`,
      { context }
    );
    return response.data.data;
  },

  async getAllFlags(): Promise<FeatureFlag[]> {
    const response = await api.get<ApiResponse<FeatureFlag[]>>('/admin/feature-flags');
    return response.data.data;
  },

  async getFlag(key: string): Promise<FeatureFlag> {
    const response = await api.get<ApiResponse<FeatureFlag>>(`/admin/feature-flags/${key}`);
    return response.data.data;
  },

  async createFlag(flag: Omit<FeatureFlag, 'key'> & { key: string }): Promise<FeatureFlag> {
    const response = await api.post<ApiResponse<FeatureFlag>>('/admin/feature-flags', flag);
    return response.data.data;
  },

  async updateFlag(key: string, flag: Partial<FeatureFlag>): Promise<FeatureFlag> {
    const response = await api.put<ApiResponse<FeatureFlag>>(`/admin/feature-flags/${key}`, flag);
    return response.data.data;
  },

  async deleteFlag(key: string): Promise<void> {
    await api.delete(`/admin/feature-flags/${key}`);
  },

  async getFlagAnalytics(key: string, hours: number = 24): Promise<any> {
    const response = await api.get<ApiResponse<any>>(`/admin/feature-flags/${key}/analytics?hours=${hours}`);
    return response.data.data;
  },
};
