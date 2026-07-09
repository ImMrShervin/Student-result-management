import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useAuth } from '@/stores/auth';
import { useTranslation } from 'react-i18next';
import { changeLanguage } from '@/i18n';
import toast from 'react-hot-toast';
import { GraduationCap } from 'lucide-react';

const schema = z.object({ email: z.string().email(), password: z.string().min(6) });
type FormData = z.infer<typeof schema>;

export default function Login() {
  const { t, i18n } = useTranslation();
  const nav = useNavigate();
  const login = useAuth((s) => s.login);
  const loading = useAuth((s) => s.loading);
  const [showDemo, setShowDemo] = useState(true);
  const { register, handleSubmit, setValue, formState: { errors } } = useForm<FormData>({ resolver: zodResolver(schema) });

  const onSubmit = async (v: FormData) => {
    try { await login(v.email, v.password); nav('/dashboard'); }
    catch (e: any) { toast.error(e?.response?.data?.message || 'Login failed'); }
  };

  const fill = (email: string) => { setValue('email', email); setValue('password', 'password'); setShowDemo(false); };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-600 to-indigo-800 p-4">
      <div className="w-full max-w-md">
        <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
          <div className="flex items-center justify-center mb-6">
            <div className="p-3 bg-brand-100 dark:bg-brand-900 rounded-2xl">
              <GraduationCap className="w-10 h-10 text-brand-600 dark:text-brand-400" />
            </div>
          </div>
          <h1 className="text-2xl font-bold text-center">{t('app_name')}</h1>
          <p className="text-center text-slate-500 text-sm mb-6">{t('app_tagline')}</p>

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div>
              <label className="label">{t('auth.email')}</label>
              <input {...register('email')} type="email" className="input" placeholder="admin@srms.local" />
              {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email.message}</p>}
            </div>
            <div>
              <label className="label">{t('auth.password')}</label>
              <input {...register('password')} type="password" className="input" placeholder="••••••••" />
              {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password.message}</p>}
            </div>
            <button type="submit" className="btn-primary w-full justify-center" disabled={loading}>
              {loading ? t('auth.signing_in') : t('auth.login')}
            </button>
          </form>

          {showDemo && (
            <div className="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
              <p className="text-xs text-slate-500 mb-2">Demo accounts (password: <code>password</code>):</p>
              <div className="grid grid-cols-2 gap-2 text-xs">
                <button onClick={() => fill('admin@srms.local')} className="btn-secondary text-xs justify-center">Admin</button>
                <button onClick={() => fill('teacher@srms.local')} className="btn-secondary text-xs justify-center">Teacher</button>
                <button onClick={() => fill('student@srms.local')} className="btn-secondary text-xs justify-center">Student</button>
                <button onClick={() => fill('staff@srms.local')} className="btn-secondary text-xs justify-center">Staff</button>
              </div>
            </div>
          )}

          <div className="mt-6 text-center text-xs text-slate-500">
            <button onClick={() => changeLanguage(i18n.language === 'fa' ? 'en' : 'fa')} className="underline">
              {i18n.language === 'fa' ? 'English' : 'فارسی'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
