import { createI18n } from 'vue-i18n'
import ar from './ar'
import en from './en'
import id from './id'

export const supportedLocales = ['id', 'en', 'ar'] as const

export type SupportedLocale = (typeof supportedLocales)[number]
export type LocaleDirection = 'ltr' | 'rtl'

export const defaultLocale: SupportedLocale = 'en'

export function isSupportedLocale(locale: string): locale is SupportedLocale {
  return supportedLocales.includes(locale as SupportedLocale)
}

export function getLocaleDirection(locale: string): LocaleDirection {
  return locale === 'ar' ? 'rtl' : 'ltr'
}

export function syncDocumentLocale(locale: string): void {
  const normalized = isSupportedLocale(locale) ? locale : defaultLocale

  document.documentElement.lang = normalized
  document.documentElement.dir = getLocaleDirection(normalized)
}

export const i18n = createI18n({
  legacy: false,
  locale: defaultLocale,
  fallbackLocale: 'en',
  messages: {
    id,
    en,
    ar,
  },
})
