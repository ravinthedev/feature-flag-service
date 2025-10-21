'use client';

import { useState, useEffect } from 'react';
import { FeatureFlag } from '@/types';
import { featureFlagService } from '@/lib/featureFlags';
import { Edit, Trash2, BarChart3 } from 'lucide-react';

interface FeatureFlagListProps {
  onEdit: (flag: FeatureFlag) => void;
  onViewAnalytics: (key: string) => void;
}

export default function FeatureFlagList({ onEdit, onViewAnalytics }: FeatureFlagListProps) {
  const [flags, setFlags] = useState<FeatureFlag[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadFlags();
  }, []);

  const loadFlags = async () => {
    try {
      setLoading(true);
      const data = await featureFlagService.getAllFlags();
      setFlags(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load feature flags');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (key: string) => {
    if (confirm('Are you sure you want to delete this feature flag?')) {
      try {
        await featureFlagService.deleteFlag(key);
        setFlags(flags.filter(flag => flag.key !== key));
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to delete feature flag');
      }
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-32">
        <div className="animate-spin rounded-full h-8 w-8 border-2 border-blue-600 border-t-transparent"></div>
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
    <div className="bg-white border rounded">
      {flags.map((flag) => (
        <div key={flag.key} className="p-4 border-b last:border-b-0">
          <div className="flex items-center justify-between">
            <div className="flex-1">
              <div className="flex items-center">
                <h3 className="text-lg font-medium text-black">{flag.name}</h3>
                <span className={`ml-2 px-2 py-1 rounded text-xs ${
                  flag.is_enabled 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                }`}>
                  {flag.is_enabled ? 'Enabled' : 'Disabled'}
                </span>
              </div>
              <p className="text-sm text-gray-600 mt-1">
                <span className="font-mono">{flag.key}</span>
                {flag.description && (
                  <span className="ml-2">• {flag.description}</span>
                )}
              </p>
              <div className="mt-2 flex items-center text-sm text-gray-600">
                <span className="capitalize">{flag.rollout_type}</span>
                {flag.starts_at && (
                  <span className="ml-4">
                    Starts: {new Date(flag.starts_at).toLocaleDateString()}
                  </span>
                )}
                {flag.ends_at && (
                  <span className="ml-4">
                    Ends: {new Date(flag.ends_at).toLocaleDateString()}
                  </span>
                )}
              </div>
            </div>
            <div className="flex items-center space-x-2">
              <button
                onClick={() => onViewAnalytics(flag.key)}
                className="text-gray-400 hover:text-gray-600"
              >
                <BarChart3 className="h-5 w-5" />
              </button>
              <button
                onClick={() => onEdit(flag)}
                className="text-gray-400 hover:text-gray-600"
              >
                <Edit className="h-5 w-5" />
              </button>
              <button
                onClick={() => handleDelete(flag.key)}
                className="text-gray-400 hover:text-red-600"
              >
                <Trash2 className="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      ))}
      {flags.length === 0 && (
        <div className="text-center py-8 text-gray-600">
          No feature flags found. Create your first one!
        </div>
      )}
    </div>
  );
}
