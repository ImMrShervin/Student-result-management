import { Link, NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '@/stores/auth';
import { useTranslation } from 'react-i18next';
import { changeLanguage } from '@/i18n';
import {
  LayoutDashboard, Users, GraduationCap, BookOpen, ClipboardList,
  Award, FileText, BarChart3, Building2, School, LogOut, Moon, Sun, Languages,
} from 'lucide-react';
import { useEffect, useState } from 'react';

const nav = [
  { to: '/dashboard', icon: LayoutDashboard, label: 'nav.dashboard' },
  { to: '/students', icon: Users, label: 'nav.students', roles: ['super_admin', 'admin', 'dean', 'department_head', 'teacher'] },
  { to: '/teachers', icon: GraduationCap, label: 'nav.teachers', roles: ['super_admin', 'admin', 'dean', 'department_head'] },
  { to: '/faculties', icon: Building2, label: 'nav.faculties' },
  { to: '/departments', icon: School, label: 'nav.departments' },
  { to: '/courses', icon: BookOpen, label: 'nav.courses' },
  { to: '/enrollments', icon: ClipboardList, label: 'nav.enrollments' },
  { to: '/grades', icon: Award, label: 'nav.grades', roles: ['super_admin', 'admin', 'teacher', 'department_head'] },
  { to: '/transcripts', icon: FileText, label: 'nav.transcripts' },
  { to: '/reports', icon: BarChart3, label: 'nav.reports', roles: ['super_admin', 'admin', 'dean', 'department_head'] },
];

export default function DashboardLayout() {
  const { t, i18n } = useTranslation();
  const { user, logout, hasRole } = useAuth();
  const navigate = useNavigate();
  const [dark, setDark] = useState(() => localStorage.getItem('srms_theme') === 'dark');

  useEffect(() => {
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('srms_theme', dark ? 'dark' : 'light');
  }, [dark]);

  return (
    <div className="h-screen flex overflow-hidden bg-slate-50 dark:bg-slate-900">
      <aside className="w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col shrink-0">
        <div className="p-5 border-b border-slate-200 dark:border-slate-700">
          <Link to="/dashboard" className="flex items-center gap-2">
            <div className="p-2 bg-brand-600 rounded-lg text-white"><GraduationCap className="w-5 h-5" /></div>
            <div>
              <div className="font-bold">{t('app_name')}</div>
              <div className="text-xs text-slate-500">{t('app_tagline')}</div>
            </div>
          </Link>
        </div>

        <nav className="flex-1 overflow-y-auto py-4">
          {nav.map((n) => (
            (!n.roles || hasRole(n.roles)) && (
              <NavLink
                key={n.to}
                to={n.to}
                className={({ isActive }) =>
                  `flex items-center gap-3 px-5 py-2.5 mx-2 rounded-lg text-sm ${
                    isActive
                      ? 'bg-brand-50 text-brand-700 dark:bg-brand-900/30 dark:text-brand-300 font-medium'
                      : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50'
                  }`
                }
              >
                <n.icon className="w-4 h-4" />
                {t(n.label)}
              </NavLink>
            )
          ))}
        </nav>

        <div className="p-4 border-t border-slate-200 dark:border-slate-700 shrink-0">
          <div className="flex items-center gap-2 mb-3">
            <div className="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900 flex items-center justify-center font-medium text-brand-700 dark:text-brand-300">
              {user?.first_name?.[0]}{user?.last_name?.[0]}
            </div>
            <div className="flex-1 min-w-0">
              <div className="text-sm font-medium truncate">{user?.full_name}</div>
              <div className="text-xs text-slate-500 truncate">{user?.roles?.[0]}</div>
            </div>
          </div>
          <div className="flex gap-1">
            <button onClick={() => setDark(!dark)} className="btn-secondary flex-1 text-xs justify-center py-1.5" title="Theme">
              {dark ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
            </button>
            <button onClick={() => changeLanguage(i18n.language === 'fa' ? 'en' : 'fa')} className="btn-secondary flex-1 text-xs justify-center py-1.5" title="Language">
              <Languages className="w-4 h-4" />
            </button>
            <button onClick={async () => { await logout(); navigate('/login'); }} className="btn-secondary flex-1 text-xs justify-center py-1.5" title="Logout">
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </aside>

      <main className="flex-1 overflow-y-auto">
        <div className="p-6 max-w-7xl mx-auto">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
