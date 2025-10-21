import { useState, useEffect, useCallback, useRef } from 'react';
import { featureFlagService } from '@/lib/featureFlags';

interface UseFeatureFlagOptions {
  context?: Record<string, any>;
  fallback?: boolean;
}

const flagCache = new Map<string, { value: boolean; timestamp: number }>();
const CACHE_TTL = 30000;

export function useFeatureFlag(key: string, options: UseFeatureFlagOptions = {}) {
  const { context = {}, fallback = false } = options;
  const [enabled, setEnabled] = useState<boolean>(fallback);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const abortControllerRef = useRef<AbortController | null>(null);

  const checkFlag = useCallback(async () => {
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    
    const cacheKey = `${key}:${user?.id || 'anon'}`;
    const cached = flagCache.get(cacheKey);
    
    if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
      setEnabled(cached.value);
      setLoading(false);
      return;
    }

    if (abortControllerRef.current) {
      abortControllerRef.current.abort();
    }

    abortControllerRef.current = new AbortController();

    try {
      setLoading(true);
      
      const fullContext = {
        ...context,
        user_email: user?.email,
        user_role: user?.role,
        user_id: user?.id,
      };
      
      const result = await featureFlagService.checkFlag(key, fullContext);
      setEnabled(result);
      flagCache.set(cacheKey, { value: result, timestamp: Date.now() });
    } catch (err: any) {
      if (err.name !== 'AbortError') {
        setEnabled(fallback);
      }
    } finally {
      setLoading(false);
      abortControllerRef.current = null;
    }
  }, [key, context, fallback]);

  useEffect(() => {
    checkFlag();
    
    return () => {
      if (abortControllerRef.current) {
        abortControllerRef.current.abort();
      }
    };
  }, [checkFlag]);

  return {
    enabled,
    loading,
    error,
    refetch: checkFlag,
  };
}

export function useFeatureFlags(keys: string[], options: UseFeatureFlagOptions = {}) {
  const { context = {}, fallback = false } = options;
  const [flags, setFlags] = useState<Record<string, boolean>>({});
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  const checkFlags = useCallback(async () => {
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    
    const fullContext = {
      ...context,
      user_email: user?.email,
      user_role: user?.role,
      user_id: user?.id,
    };

    try {
      setLoading(true);
      setError(null);
      
      const results = await Promise.all(
        keys.map(async (key) => {
          try {
            const enabled = await featureFlagService.checkFlag(key, fullContext);
            return { key, enabled };
          } catch {
            return { key, enabled: fallback };
          }
        })
      );

      const flagMap = results.reduce((acc, { key, enabled }) => {
        acc[key] = enabled;
        return acc;
      }, {} as Record<string, boolean>);

      setFlags(flagMap);
    } catch (err) {
      const fallbackFlags = keys.reduce((acc, key) => {
        acc[key] = fallback;
        return acc;
      }, {} as Record<string, boolean>);
      setFlags(fallbackFlags);
    } finally {
      setLoading(false);
    }
  }, [keys, context, fallback]);

  useEffect(() => {
    checkFlags();
  }, [checkFlags]);

  return {
    flags,
    loading,
    error,
    refetch: checkFlags,
  };
}
