'use client';

import { useState, useEffect } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import { authService } from '@/lib/auth';
import { User } from '@/types';
import { LogOut, Settings, FileText, Flag } from 'lucide-react';

interface LayoutProps {
  children: React.ReactNode;
}

export default function Layout({ children }: LayoutProps) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();
  const pathname = usePathname();

  useEffect(() => {
    const storedUser = authService.getStoredUser();
    if (storedUser) {
      setUser(storedUser);
    } else if (pathname !== '/login') {
      router.push('/login');
    }
    setLoading(false);
  }, [pathname, router]);

  const handleLogout = async () => {
    try {
      await authService.logout();
      setUser(null);
      router.push('/login');
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-2 border-blue-600 border-t-transparent"></div>
      </div>
    );
  }

  if (pathname === '/login') {
    return <>{children}</>;
  }

  if (!user) {
    return null;
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white border-b">
        <div className="max-w-6xl mx-auto px-6">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Flag className="h-8 w-8 text-blue-600" />
              <Link href="/" className="ml-2 text-xl font-bold text-black hover:text-blue-600">
                Feature Flag Service
              </Link>
              <div className="ml-8 flex space-x-6">
                {user.role === 'admin' && (
                  <Link
                    href="/admin"
                    className={`px-3 py-2 rounded text-sm ${
                      pathname === '/admin'
                        ? 'bg-blue-100 text-blue-700'
                        : 'text-gray-600 hover:text-black'
                    }`}
                  >
                    <Settings className="inline w-4 h-4 mr-1" />
                    Admin
                  </Link>
                )}
                <Link
                  href="/reports"
                  className={`px-3 py-2 rounded text-sm ${
                    pathname === '/reports'
                      ? 'bg-blue-100 text-blue-700'
                      : 'text-gray-600 hover:text-black'
                  }`}
                >
                  <FileText className="inline w-4 h-4 mr-1" />
                  Reports
                </Link>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <div className="text-sm">
                <p className="text-black font-medium">{user.name}</p>
                <p className="text-gray-600 text-xs capitalize">{user.role}</p>
              </div>
              <button
                onClick={handleLogout}
                className="text-gray-600 hover:text-black p-2"
              >
                <LogOut className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </nav>
      <main>{children}</main>
    </div>
  );
}
