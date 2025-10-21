'use client';

import { useState, useEffect } from 'react';
import { featureFlagService } from '@/lib/featureFlags';
import { X } from 'lucide-react';

interface FeatureFlagAnalyticsProps {
  flagKey: string;
  onClose: () => void;
}

export default function FeatureFlagAnalytics({ flagKey, onClose }: FeatureFlagAnalyticsProps) {
  const [analytics, setAnalytics] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadAnalytics();
  }, [flagKey]);

  const loadAnalytics = async () => {
    try {
      setLoading(true);
      const data = await featureFlagService.getFlagAnalytics(flagKey, 24);
      setAnalytics(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load analytics');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div className="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-lg font-medium text-gray-900">
            Analytics: {flagKey}
          </h3>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600"
          >
            <X className="h-6 w-6" />
          </button>
        </div>

        {loading && (
          <div className="flex justify-center items-center h-32">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          </div>
        )}

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {error}
          </div>
        )}

        {analytics && (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-gray-50 p-4 rounded-lg">
                <h4 className="font-medium text-gray-900">Total Decisions</h4>
                <p className="text-2xl font-bold text-blue-600">
                  {analytics.total_decisions}
                </p>
              </div>
              <div className="bg-gray-50 p-4 rounded-lg">
                <h4 className="font-medium text-gray-900">Enabled Rate</h4>
                <p className="text-2xl font-bold text-green-600">
                  {analytics.enabled_percentage}%
                </p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="bg-green-50 p-4 rounded-lg">
                <h4 className="font-medium text-green-900">Enabled</h4>
                <p className="text-xl font-bold text-green-600">
                  {analytics.enabled_count}
                </p>
              </div>
              <div className="bg-red-50 p-4 rounded-lg">
                <h4 className="font-medium text-red-900">Disabled</h4>
                <p className="text-xl font-bold text-red-600">
                  {analytics.disabled_count}
                </p>
              </div>
            </div>

            {analytics.reasons && Object.keys(analytics.reasons).length > 0 && (
              <div>
                <h4 className="font-medium text-gray-900 mb-2">Decision Reasons</h4>
                <div className="space-y-2">
                  {Object.entries(analytics.reasons).map(([reason, count]) => (
                    <div key={reason} className="flex justify-between items-center bg-gray-50 p-2 rounded">
                      <span className="text-sm text-gray-700">{reason}</span>
                      <span className="text-sm font-medium text-gray-900">{count as number}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
