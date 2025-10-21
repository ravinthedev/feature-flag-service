'use client';

interface FeatureDisabledProps {
  feature: string;
  onClose?: () => void;
}

export default function FeatureDisabled({ feature, onClose }: FeatureDisabledProps) {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white p-6 rounded-lg max-w-md">
        <h3 className="text-lg font-semibold mb-2">Feature Not Available</h3>
        <p className="text-gray-600 mb-4">
          The {feature} feature is currently disabled. Please try again later.
        </p>
        <button
          onClick={onClose}
          className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        >
          OK
        </button>
      </div>
    </div>
  );
}
