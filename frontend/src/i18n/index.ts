import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import en from './locales/en.json';
import fa from './locales/fa.json';

const stored = localStorage.getItem('srms_locale') || 'en';

i18n.use(initReactI18next).init({
  resources: { en: { translation: en }, fa: { translation: fa } },
  lng: stored,
  fallbackLng: 'en',
  interpolation: { escapeValue: false },
});

document.documentElement.dir = stored === 'fa' ? 'rtl' : 'ltr';
document.documentElement.lang = stored;

export const changeLanguage = (lang: 'en' | 'fa') => {
  i18n.changeLanguage(lang);
  localStorage.setItem('srms_locale', lang);
  document.documentElement.dir = lang === 'fa' ? 'rtl' : 'ltr';
  document.documentElement.lang = lang;
};

export default i18n;
