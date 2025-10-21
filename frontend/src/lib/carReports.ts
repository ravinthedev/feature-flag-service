import api from './api';
import { CarReport, ApiResponse } from '@/types';

export const carReportService = {
  async getAllReports(): Promise<CarReport[]> {
    const response = await api.get<ApiResponse<CarReport[]>>('/car-reports');
    return response.data.data;
  },

  async getReport(id: number): Promise<CarReport> {
    const response = await api.get<ApiResponse<CarReport>>(`/car-reports/${id}`);
    return response.data.data;
  },

  async createReport(report: {
    car_model: string;
    description: string;
    damage_type: 'minor' | 'moderate' | 'severe' | 'total_loss';
    photo?: File | null;
  }): Promise<CarReport> {
    const formData = new FormData();
    formData.append('car_model', report.car_model);
    formData.append('description', report.description);
    formData.append('damage_type', report.damage_type);
    
    if (report.photo) {
      formData.append('photo', report.photo);
    }

    const response = await api.post<ApiResponse<CarReport>>('/car-reports', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data.data;
  },

  async updateReport(id: number, report: Partial<CarReport & { photo?: File | null }>): Promise<CarReport> {
    const formData = new FormData();
    
    if (report.car_model) formData.append('car_model', report.car_model);
    if (report.description) formData.append('description', report.description);
    if (report.damage_type) formData.append('damage_type', report.damage_type);
    if (report.status) formData.append('status', report.status);
    if (report.photo) formData.append('photo', report.photo);

    const response = await api.put<ApiResponse<CarReport>>(`/car-reports/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data.data;
  },

  async deleteReport(id: number): Promise<void> {
    await api.delete(`/admin/car-reports/${id}`);
  },
};
