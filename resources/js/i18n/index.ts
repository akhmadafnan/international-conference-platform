import { createI18n } from 'vue-i18n';
import ar from '@/i18n/messages/ar';
import en from '@/i18n/messages/en';
import id from '@/i18n/messages/id';
import type { AppLocale, TextDirection } from '@/types';

export const supportedLocales = ['id', 'en', 'ar'] as const;

export function normalizeLocale(value: string | null | undefined): AppLocale {
    const normalized = (value ?? '').toLowerCase().split('-')[0];

    return supportedLocales.includes(normalized as AppLocale)
        ? (normalized as AppLocale)
        : 'en';
}

export function directionFor(locale: AppLocale): TextDirection {
    return locale === 'ar' ? 'rtl' : 'ltr';
}

const initialLocale = normalizeLocale(
    typeof document === 'undefined' ? 'en' : document.documentElement.lang,
);

export const i18n = createI18n({
    legacy: false,
    locale: initialLocale,
    fallbackLocale: 'en',
    messages: {
        id,
        en,
        ar,
    },
});

export function applyClientLocale(locale: AppLocale): void {
    i18n.global.locale.value = locale;

    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.lang = locale;
    document.documentElement.dir = directionFor(locale);
}
