'use client';

import { useState } from 'react';
import { FeatureFlag } from '@/types';
import FeatureFlagList from '@/components/FeatureFlagList';
import FeatureFlagForm from '@/components/FeatureFlagForm';
import FeatureFlagAnalytics from '@/components/FeatureFlagAnalytics';
import Layout from '@/components/Layout';
import { Plus } from 'lucide-react';

export default function AdminPage() {
  const [showForm, setShowForm] = useState(false);
  const [editingFlag, setEditingFlag] = useState<FeatureFlag | undefined>();
  const [analyticsFlag, setAnalyticsFlag] = useState<string | null>(null);
  const [refreshKey, setRefreshKey] = useState(0);

  const handleCreate = () => {
    setEditingFlag(undefined);
    setShowForm(true);
  };

  const handleEdit = (flag: FeatureFlag) => {
    setEditingFlag(flag);
    setShowForm(true);
  };

  const handleFormSuccess = () => {
    setShowForm(false);
    setEditingFlag(undefined);
    setRefreshKey(prev => prev + 1);
  };

  const handleFormCancel = () => {
    setShowForm(false);
    setEditingFlag(undefined);
  };


  const handleViewAnalytics = (key: string) => {
    setAnalyticsFlag(key);
  };

  const handleCloseAnalytics = () => {
    setAnalyticsFlag(null);
  };

  return (
    <Layout>
      <div className="max-w-6xl mx-auto p-6">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold text-black">Feature Flag Management</h1>
          <button
            onClick={handleCreate}
            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            <Plus className="inline w-4 h-4 mr-1" />
            Create Feature Flag
          </button>
        </div>

        {showForm && (
          <div className="mb-6 p-4 bg-white border rounded">
            <h2 className="text-lg font-semibold text-black mb-4">
              {editingFlag ? 'Edit Feature Flag' : 'Create Feature Flag'}
            </h2>
            <FeatureFlagForm
              flag={editingFlag}
              onSuccess={handleFormSuccess}
              onCancel={handleFormCancel}
            />
          </div>
        )}

        <FeatureFlagList
          key={refreshKey}
          onEdit={handleEdit}
          onViewAnalytics={handleViewAnalytics}
        />
      </div>

      {analyticsFlag && (
        <FeatureFlagAnalytics
          flagKey={analyticsFlag}
          onClose={handleCloseAnalytics}
        />
      )}
    </Layout>
  );
}
