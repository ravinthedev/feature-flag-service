'use client';

import { CarReport } from '@/types';
import { useFeatureFlag } from '@/hooks/useFeatureFlag';
import { X, Calendar, Car, AlertTriangle, CheckCircle, XCircle } from 'lucide-react';

interface CarReportDetailProps {
  report: CarReport;
  onClose: () => void;
}

export default function CarReportDetail({ report, onClose }: CarReportDetailProps) {
  const { enabled: aiDamageAssessmentEnabled } = useFeatureFlag('ai_damage_assessment');
  const { enabled: premiumAnalyticsEnabled } = useFeatureFlag('premium_analytics');

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'approved':
        return <CheckCircle className="h-5 w-5 text-green-500" />;
      case 'rejected':
        return <XCircle className="h-5 w-5 text-red-500" />;
      default:
        return <AlertTriangle className="h-5 w-5 text-yellow-500" />;
    }
  };

  const getDamageSeverity = (damageType: string) => {
    switch (damageType) {
      case 'minor':
        return { level: 'Low', color: 'text-green-600', bg: 'bg-green-50' };
      case 'moderate':
        return { level: 'Medium', color: 'text-yellow-600', bg: 'bg-yellow-50' };
      case 'severe':
        return { level: 'High', color: 'text-orange-600', bg: 'bg-orange-50' };
      case 'total_loss':
        return { level: 'Critical', color: 'text-red-600', bg: 'bg-red-50' };
      default:
        return { level: 'Unknown', color: 'text-gray-600', bg: 'bg-gray-50' };
    }
  };

  const damageInfo = getDamageSeverity(report.damage_type);

  return (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div className="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-lg font-medium text-gray-900">Car Report Details</h3>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600"
          >
            <X className="h-6 w-6" />
          </button>
        </div>

        <div className="space-y-6">
          {/* Header Info */}
          <div className="flex items-center space-x-3">
            <Car className="h-8 w-8 text-blue-600" />
            <div>
              <h2 className="text-xl font-semibold text-gray-900">{report.car_model}</h2>
              <div className="flex items-center space-x-2 mt-1">
                {getStatusIcon(report.status)}
                <span className="text-sm text-gray-600 capitalize">{report.status}</span>
              </div>
            </div>
          </div>

          {/* Damage Assessment */}
          <div className={`p-4 rounded-lg ${damageInfo.bg}`}>
            <div className="flex items-center justify-between">
              <div>
                <h4 className="font-medium text-gray-900">Damage Assessment</h4>
                <p className={`text-sm font-medium ${damageInfo.color}`}>
                  {damageInfo.level} Severity
                </p>
              </div>
              <span className="text-sm text-gray-600 capitalize">
                {report.damage_type?.replace('_', ' ') || 'Unknown'}
              </span>
            </div>
          </div>

          {/* AI Assessment (Feature Flag) */}
          {aiDamageAssessmentEnabled && (
            <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span className="text-blue-600 font-bold text-sm">AI</span>
                  </div>
                </div>
                <div className="ml-3">
                  <h3 className="text-sm font-medium text-blue-900">AI Damage Assessment</h3>
                  <p className="text-sm text-blue-700">
                    Estimated repair cost: $2,500 - $4,200
                  </p>
                  <p className="text-xs text-blue-600 mt-1">
                    Confidence: 87% • Analysis completed 2 minutes ago
                  </p>
                </div>
              </div>
            </div>
          )}

          {/* Description */}
          <div>
            <h4 className="font-medium text-gray-900 mb-2">Description</h4>
            <p className="text-gray-700 bg-gray-50 p-3 rounded-lg">
              {report.description}
            </p>
          </div>

          {/* Photo */}
          {report.photo_url && (
            <div>
              <h4 className="font-medium text-gray-900 mb-2">Photo</h4>
              <div className="bg-gray-50 p-4 rounded-lg">
                <img
                  src={report.photo_url}
                  alt="Car damage"
                  className="max-w-full h-auto rounded-lg shadow-sm"
                />
              </div>
            </div>
          )}

          {/* Premium Analytics (Feature Flag) */}
          {premiumAnalyticsEnabled && (
            <div className="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
              <h4 className="font-medium text-purple-900 mb-2">Premium Analytics</h4>
              <div className="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span className="text-purple-700">Similar Cases:</span>
                  <span className="ml-2 font-medium text-purple-900">23 found</span>
                </div>
                <div>
                  <span className="text-purple-700">Avg. Processing Time:</span>
                  <span className="ml-2 font-medium text-purple-900">2.3 days</span>
                </div>
                <div>
                  <span className="text-purple-700">Success Rate:</span>
                  <span className="ml-2 font-medium text-purple-900">94%</span>
                </div>
                <div>
                  <span className="text-purple-700">Risk Score:</span>
                  <span className="ml-2 font-medium text-purple-900">Low</span>
                </div>
              </div>
            </div>
          )}

          {/* Metadata */}
          <div className="border-t pt-4">
            <div className="grid grid-cols-2 gap-4 text-sm text-gray-600">
              <div className="flex items-center">
                <Calendar className="h-4 w-4 mr-2" />
                <span>Created: {new Date(report.created_at).toLocaleString()}</span>
              </div>
              <div className="flex items-center">
                <Calendar className="h-4 w-4 mr-2" />
                <span>Updated: {new Date(report.updated_at).toLocaleString()}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
