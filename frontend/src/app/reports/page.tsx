'use client';

import { useState } from 'react';
import { CarReport } from '@/types';
import CarReportList from '@/components/CarReportList';
import CarReportForm from '@/components/CarReportForm';
import CarReportDetail from '@/components/CarReportDetail';
import Layout from '@/components/Layout';
import FeatureDisabled from '@/components/FeatureDisabled';
import { useFeatureFlag } from '@/hooks/useFeatureFlag';
import { Plus } from 'lucide-react';

export default function ReportsPage() {
  const [showForm, setShowForm] = useState(false);
  const [selectedReport, setSelectedReport] = useState<CarReport | null>(null);
  const [editingReport, setEditingReport] = useState<CarReport | null>(null);
  const [showFeatureDisabled, setShowFeatureDisabled] = useState<string | null>(null);

  const { enabled: betaDashboardEnabled } = useFeatureFlag('beta_dashboard');
  const { enabled: mobileAppEnabled } = useFeatureFlag('mobile_app_integration');
  const { enabled: premiumAnalyticsEnabled } = useFeatureFlag('premium_analytics');

  const handleCreate = () => {
    setEditingReport(null);
    setShowForm(true);
  };

  const handleEdit = (report: CarReport) => {
    setEditingReport(report);
    setShowForm(true);
  };

  const handleView = (report: CarReport) => {
    setSelectedReport(report);
  };

  const handleFormSuccess = () => {
    setShowForm(false);
    setEditingReport(null);
  };

  const handleFormCancel = () => {
    setShowForm(false);
    setEditingReport(null);
  };


  const handleCloseDetail = () => {
    setSelectedReport(null);
  };

  return (
    <Layout>
      <div className="max-w-6xl mx-auto p-6">
        
        {betaDashboardEnabled && (
          <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded">
            <strong>Beta Dashboard:</strong> You have access to beta features
          </div>
        )}

        {mobileAppEnabled && (
          <div className="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
            <div className="flex justify-between items-center">
              <div>
                <strong>Mobile App Available</strong>
                <p className="text-sm text-gray-600">Download our mobile app for faster reporting</p>
              </div>
              <button 
                onClick={() => !mobileAppEnabled && setShowFeatureDisabled('Mobile App')}
                className="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm"
              >
                Download
              </button>
            </div>
          </div>
        )}

        {premiumAnalyticsEnabled && (
          <div className="mb-4 p-4 bg-white border rounded">
            <strong className="block mb-2">Advanced Search</strong>
            <div className="grid grid-cols-3 gap-3">
              <select className="p-2 border rounded text-sm">
                <option>All Types</option>
                <option>Minor</option>
                <option>Moderate</option>
                <option>Severe</option>
              </select>
              <select className="p-2 border rounded text-sm">
                <option>All Time</option>
                <option>This Week</option>
                <option>This Month</option>
              </select>
              <select className="p-2 border rounded text-sm">
                <option>All Status</option>
                <option>Pending</option>
                <option>Approved</option>
              </select>
            </div>
          </div>
        )}

        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">Car Damage Reports</h1>
          <button
            onClick={handleCreate}
            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            <Plus className="inline w-4 h-4 mr-1" />
            New Report
          </button>
        </div>

        {showForm && (
          <div className="mb-6 p-4 bg-white border rounded">
            <h2 className="text-lg font-semibold mb-4">
              {editingReport ? 'Edit Report' : 'Create New Report'}
            </h2>
            <CarReportForm
              onSuccess={handleFormSuccess}
              onCancel={handleFormCancel}
            />
          </div>
        )}

        <CarReportList
          onEdit={handleEdit}
          onView={handleView}
        />
      </div>

      {selectedReport && (
        <CarReportDetail
          report={selectedReport}
          onClose={handleCloseDetail}
        />
      )}

      {showFeatureDisabled && (
        <FeatureDisabled
          feature={showFeatureDisabled}
          onClose={() => setShowFeatureDisabled(null)}
        />
      )}
    </Layout>
  );
}
