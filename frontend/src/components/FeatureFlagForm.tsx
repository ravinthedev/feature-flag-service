'use client';

import { useState } from 'react';
import { FeatureFlag } from '@/types';
import { featureFlagService } from '@/lib/featureFlags';

interface FeatureFlagFormData {
  name: string;
  key: string;
  description?: string;
  is_enabled: boolean;
  rollout_type: 'boolean' | 'percentage' | 'scheduled' | 'user_list';
  rollout_value?: any;
  starts_at?: string;
  ends_at?: string;
}

interface FeatureFlagFormProps {
  flag?: FeatureFlag;
  onSuccess: () => void;
  onCancel: () => void;
}

export default function FeatureFlagForm({ flag, onSuccess, onCancel }: FeatureFlagFormProps) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const [formData, setFormData] = useState<FeatureFlagFormData>(flag ? {
    name: flag.name,
    key: flag.key,
    description: flag.description || '',
    is_enabled: flag.is_enabled,
    rollout_type: flag.rollout_type,
    rollout_value: flag.rollout_value,
    starts_at: flag.starts_at ? new Date(flag.starts_at).toISOString().slice(0, 16) : '',
    ends_at: flag.ends_at ? new Date(flag.ends_at).toISOString().slice(0, 16) : '',
  } : {
    name: '',
    key: '',
    description: '',
    is_enabled: false,
    rollout_type: 'boolean',
    rollout_value: {},
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value, type } = e.target;
    const checked = (e.target as HTMLInputElement).checked;
    
    if (name.includes('.')) {
      const [parent, child] = name.split('.');
      setFormData({
        ...formData,
        [parent]: {
          ...formData.rollout_value,
          [child]: type === 'number' ? parseInt(value) || 0 : value,
        },
      });
    } else {
      setFormData({
        ...formData,
        [name]: type === 'checkbox' ? checked : value,
      });
    }
    
    if (errors[name]) {
      setErrors({ ...errors, [name]: '' });
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }
    
    if (!formData.key.trim()) {
      newErrors.key = 'Key is required';
    } else if (!/^[a-zA-Z0-9_-]+$/.test(formData.key)) {
      newErrors.key = 'Key must contain only letters, numbers, underscores, and hyphens';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setLoading(true);
    setError(null);

    try {
      if (flag) {
        await featureFlagService.updateFlag(flag.key, formData);
      } else {
        await featureFlagService.createFlag(formData);
      }
      onSuccess();
    } catch (err) {
      setError('Failed to save feature flag');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={onSubmit} className="space-y-4">
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      )}

      <div>
        <label className="block text-sm font-medium text-black mb-1">
          Name *
        </label>
        <input
          name="name"
          value={formData.name}
          onChange={handleInputChange}
          className="w-full p-2 border rounded"
          placeholder="Feature Flag Name"
        />
        {errors.name && (
          <p className="text-red-500 text-sm mt-1">{errors.name}</p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-black mb-1">
          Key *
        </label>
        <input
          name="key"
          value={formData.key}
          onChange={handleInputChange}
          disabled={!!flag}
          className="w-full p-2 border rounded disabled:bg-gray-100"
          placeholder="feature_flag_key"
        />
        {errors.key && (
          <p className="text-red-500 text-sm mt-1">{errors.key}</p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-black mb-1">
          Description
        </label>
        <textarea
          name="description"
          value={formData.description}
          onChange={handleInputChange}
          className="w-full p-2 border rounded"
          rows={3}
          placeholder="Describe what this feature flag controls"
        />
      </div>

      <div className="flex items-center">
        <input
          name="is_enabled"
          type="checkbox"
          checked={formData.is_enabled}
          onChange={handleInputChange}
          className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label className="ml-2 block text-sm text-black">
          Enabled
        </label>
      </div>

      <div>
        <label className="block text-sm font-medium text-black mb-1">
          Rollout Type *
        </label>
        <select
          name="rollout_type"
          value={formData.rollout_type}
          onChange={handleInputChange}
          className="w-full p-2 border rounded"
        >
          <option value="boolean">Boolean (On/Off)</option>
          <option value="percentage">Percentage</option>
          <option value="scheduled">Scheduled</option>
          <option value="user_list">User List</option>
        </select>
      </div>

      {formData.rollout_type === 'percentage' && (
        <div>
          <label className="block text-sm font-medium text-black mb-1">
            Percentage (0-100)
          </label>
          <input
            name="rollout_value.percentage"
            type="number"
            min="0"
            max="100"
            value={formData.rollout_value?.percentage || ''}
            onChange={handleInputChange}
            className="w-full p-2 border rounded"
          />
        </div>
      )}

      {formData.rollout_type === 'user_list' && (
        <div>
          <label className="block text-sm font-medium text-black mb-1">
            User IDs (comma-separated)
          </label>
          <input
            name="rollout_value.user_ids"
            value={formData.rollout_value?.user_ids || ''}
            onChange={handleInputChange}
            className="w-full p-2 border rounded"
            placeholder="1,2,3,4"
          />
        </div>
      )}

      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="block text-sm font-medium text-black mb-1">
            Start Date
          </label>
          <input
            name="starts_at"
            type="datetime-local"
            value={formData.starts_at}
            onChange={handleInputChange}
            className="w-full p-2 border rounded"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-black mb-1">
            End Date
          </label>
          <input
            name="ends_at"
            type="datetime-local"
            value={formData.ends_at}
            onChange={handleInputChange}
            className="w-full p-2 border rounded"
          />
        </div>
      </div>

      <div className="flex justify-end space-x-3">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={loading}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
        >
          {loading ? 'Saving...' : flag ? 'Update' : 'Create'}
        </button>
      </div>
    </form>
  );
}
