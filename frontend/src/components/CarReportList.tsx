'use client';

import { useState, useEffect } from 'react';
import { CarReport } from '@/types';
import { carReportService } from '@/lib/carReports';
import { useFeatureFlag } from '@/hooks/useFeatureFlag';
import { Edit, Eye, Calendar, Car } from 'lucide-react';

interface CarReportListProps {
  onEdit: (report: CarReport) => void;
  onView: (report: CarReport) => void;
}

export default function CarReportList({ onEdit, onView }: CarReportListProps) {
  const [reports, setReports] = useState<CarReport[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const { enabled: premiumAnalyticsEnabled } = useFeatureFlag('premium_analytics');

  useEffect(() => {
    loadReports();
  }, []);

  const loadReports = async () => {
    try {
      setLoading(true);
      const data = await carReportService.getAllReports();
      setReports(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load reports');
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved':
        return 'bg-green-100 text-green-800';
      case 'rejected':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-yellow-100 text-yellow-800';
    }
  };

  const getDamageTypeColor = (damageType: string) => {
    switch (damageType) {
      case 'minor':
        return 'bg-blue-100 text-blue-800';
      case 'moderate':
        return 'bg-yellow-100 text-yellow-800';
      case 'severe':
        return 'bg-orange-100 text-orange-800';
      case 'total_loss':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-32">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {error}
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {premiumAnalyticsEnabled && (
        <div className="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-4">
          <div className="flex items-center">
            <div className="flex-shrink-0">
              <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <span className="text-purple-600 font-bold text-sm">★</span>
              </div>
            </div>
            <div className="ml-3">
              <h3 className="text-sm font-medium text-purple-900">Premium Analytics Enabled</h3>
              <p className="text-sm text-purple-700">
                Advanced reporting features are available for this session.
              </p>
            </div>
          </div>
        </div>
      )}

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        <ul className="divide-y divide-gray-200">
          {reports.map((report) => (
            <li key={report.id} className="px-6 py-4">
              <div className="flex items-center justify-between">
                <div className="flex-1">
                  <div className="flex items-center">
                    <Car className="h-5 w-5 text-gray-400 mr-2" />
                    <h3 className="text-lg font-medium text-gray-900">{report.car_model || 'Unknown Car Model'}</h3>
                    <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(report.status)}`}>
                      {report.status}
                    </span>
                    <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getDamageTypeColor(report.damage_type)}`}>
                      {report.damage_type?.replace('_', ' ') || 'Unknown'}
                    </span>
                  </div>
                  <p className="text-sm text-gray-500 mt-1">{report.description || 'No description provided'}</p>
                  <div className="mt-2 flex items-center text-sm text-gray-500">
                    <Calendar className="h-4 w-4 mr-1" />
                    <span>Created: {report.created_at ? new Date(report.created_at).toLocaleDateString() : 'Unknown date'}</span>
                    {report.photo_url && (
                      <span className="ml-4 text-green-600">📷 Photo attached</span>
                    )}
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <button
                    onClick={() => onView(report)}
                    className="text-gray-400 hover:text-gray-600"
                    title="View Details"
                  >
                    <Eye className="h-5 w-5" />
                  </button>
                  <button
                    onClick={() => onEdit(report)}
                    className="text-gray-400 hover:text-gray-600"
                    title="Edit"
                  >
                    <Edit className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </li>
          ))}
        </ul>
        {reports.length === 0 && (
          <div className="text-center py-8 text-gray-500">
            No car reports found. Submit your first report!
          </div>
        )}
      </div>
    </div>
  );
}
