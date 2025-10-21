'use client';

import { useState, useEffect } from 'react';
import { useFeatureFlag } from '@/hooks/useFeatureFlag';
import { AlertTriangle, RefreshCw } from 'lucide-react';

interface FeatureFlagGuardProps {
  flagKey: string;
  children: React.ReactNode;
  fallback?: React.ReactNode;
  onFeatureDisabled?: () => void;
}

export default function FeatureFlagGuard({ 
  flagKey, 
  children, 
  fallback, 
  onFeatureDisabled 
}: FeatureFlagGuardProps) {
  const { enabled, loading, refetch } = useFeatureFlag(flagKey, { fallback: false });
  const [wasEnabled, setWasEnabled] = useState<boolean | null>(null);
  const [showDisabledMessage, setShowDisabledMessage] = useState(false);

  useEffect(() => {
    if (!loading && wasEnabled === null) {
      setWasEnabled(enabled);
    } else if (!loading && wasEnabled === true && !enabled) {
      setShowDisabledMessage(true);
      onFeatureDisabled?.();
    }
  }, [enabled, loading, wasEnabled, onFeatureDisabled]);

  const handleRetry = () => {
    setShowDisabledMessage(false);
    refetch();
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center p-4">
        <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (showDisabledMessage) {
    return (
      <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div className="flex items-start">
          <AlertTriangle className="h-5 w-5 text-yellow-600 mt-0.5 mr-3" />
          <div className="flex-1">
            <h3 className="text-sm font-medium text-yellow-800">
              Feature Temporarily Unavailable
            </h3>
            <p className="text-sm text-yellow-700 mt-1">
              This feature has been temporarily disabled. Please try again later.
            </p>
            <button
              onClick={handleRetry}
              className="mt-2 inline-flex items-center text-sm text-yellow-800 hover:text-yellow-900"
            >
              <RefreshCw className="h-4 w-4 mr-1" />
              Check Again
            </button>
          </div>
        </div>
      </div>
    );
  }

  if (!enabled) {
    return fallback ? <>{fallback}</> : null;
  }

  return <>{children}</>;
}
