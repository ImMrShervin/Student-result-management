import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { api } from '@/lib/api';

export type User = {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  email: string;
  avatar_url?: string | null;
  locale: string;
  roles: string[];
  permissions?: string[];
  student?: any;
  teacher?: any;
};

type State = {
  token: string | null;
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  fetchMe: () => Promise<void>;
  hasRole: (role: string | string[]) => boolean;
};

export const useAuth = create<State>()(
  persist(
    (set, get) => ({
      token: null,
      user: null,
      loading: false,

      async login(email, password) {
        set({ loading: true });
        try {
          const { data } = await api.post('/auth/login', { email, password });
          localStorage.setItem('srms_token', data.token);
          set({ token: data.token, user: data.user });
        } finally {
          set({ loading: false });
        }
      },

      async logout() {
        try { await api.post('/auth/logout'); } catch {}
        localStorage.removeItem('srms_token');
        set({ token: null, user: null });
      },

      async fetchMe() {
        const { data } = await api.get('/auth/me');
        set({ user: data.data ?? data });
      },

      hasRole(role) {
        const roles = get().user?.roles ?? [];
        const arr = Array.isArray(role) ? role : [role];
        return arr.some((r) => roles.includes(r));
      },
    }),
    { name: 'srms-auth', partialize: (s) => ({ token: s.token, user: s.user }) },
  ),
);
