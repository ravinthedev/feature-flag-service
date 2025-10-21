'use client';

import { useState } from 'react';
import { carReportService } from '@/lib/carReports';
import { useFeatureFlag } from '@/hooks/useFeatureFlag';
import { Camera } from 'lucide-react';
import { FEATURE_FLAGS } from '@/lib/constants';

interface CarReportFormData {
  car_model: string;
  description: string;
  damage_type: 'minor' | 'moderate' | 'severe' | 'total_loss';
  photo?: File | null;
}

interface CarReportFormProps {
  onSuccess: () => void;
  onCancel: () => void;
}

export default function CarReportForm({ onSuccess, onCancel }: CarReportFormProps) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [photoPreview, setPhotoPreview] = useState<string | null>(null);

  const { enabled: uploadPhotosEnabled } = useFeatureFlag(FEATURE_FLAGS.UPLOAD_PHOTOS);

  const [formData, setFormData] = useState<CarReportFormData>({
    car_model: '',
    description: '',
    damage_type: 'minor',
    photo: null,
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const handlePhotoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        const result = e.target?.result as string;
        setPhotoPreview(result);
      };
      reader.readAsDataURL(file);
      setFormData({ ...formData, photo: file });
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    if (errors[name]) {
      setErrors({ ...errors, [name]: '' });
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.car_model.trim()) {
      newErrors.car_model = 'Car model is required';
    }
    
    if (!formData.description.trim()) {
      newErrors.description = 'Description is required';
    } else if (formData.description.trim().length < 10) {
      newErrors.description = 'Description must be at least 10 characters';
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
      await carReportService.createReport(formData);
      onSuccess();
    } catch (err) {
      setError('Failed to submit report');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={onSubmit} className="space-y-6">
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      )}

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Car Model *
        </label>
        <input
          name="car_model"
          value={formData.car_model}
          onChange={handleInputChange}
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="e.g., Toyota Camry 2020"
        />
        {errors.car_model && (
          <p className="text-red-500 text-sm mt-1">{errors.car_model}</p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Damage Type *
        </label>
        <select
          name="damage_type"
          value={formData.damage_type}
          onChange={handleInputChange}
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="minor">Minor - Small scratches, dents</option>
          <option value="moderate">Moderate - Significant damage, repairable</option>
          <option value="severe">Severe - Major damage, expensive repair</option>
          <option value="total_loss">Total Loss - Beyond repair</option>
        </select>
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Description *
        </label>
        <textarea
          name="description"
          value={formData.description}
          onChange={handleInputChange}
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          rows={4}
          placeholder="Describe the damage in detail..."
        />
        {errors.description && (
          <p className="text-red-500 text-sm mt-1">{errors.description}</p>
        )}
      </div>

      {uploadPhotosEnabled && (
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Photo Upload (Optional)
          </label>
          <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
            <div className="space-y-1 text-center">
              {photoPreview ? (
                <div className="space-y-2">
                  <img
                    src={photoPreview}
                    alt="Preview"
                    className="mx-auto h-32 w-auto rounded-lg object-cover"
                  />
                  <button
                    type="button"
                    onClick={() => {
                      setPhotoPreview(null);
                      setFormData({ ...formData, photo: null });
                    }}
                    className="text-sm text-red-600 hover:text-red-500"
                  >
                    Remove Photo
                  </button>
                </div>
              ) : (
                <div>
                  <Camera className="mx-auto h-12 w-12 text-gray-400" />
                  <div className="flex text-sm text-gray-600">
                    <label
                      htmlFor="photo-upload"
                      className="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500"
                    >
                      <span>Upload a photo</span>
                      <input
                        id="photo-upload"
                        type="file"
                        accept="image/*"
                        onChange={handlePhotoChange}
                        className="sr-only"
                      />
                    </label>
                    <p className="pl-1">or drag and drop</p>
                  </div>
                  <p className="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      <div className="flex justify-end space-x-3">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={loading}
          className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
        >
          {loading ? 'Submitting...' : 'Submit Report'}
        </button>
      </div>
    </form>
  );
}
