import axios from 'axios';

export const api = axios.create({
  baseURL: '/api/v1',
  withCredentials: true,
  headers: { Accept: 'application/json' },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('srms_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  const locale = localStorage.getItem('srms_locale');
  if (locale) config.headers['X-Locale'] = locale;
  return config;
});

api.interceptors.response.use(
  (r) => r,
  (err) => {
    if (err.response?.status === 401 && location.pathname !== '/login') {
      localStorage.removeItem('srms_token');
      location.href = '/login';
    }
    return Promise.reject(err);
  },
);
