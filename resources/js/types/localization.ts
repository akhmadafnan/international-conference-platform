export type AppLocale = 'id' | 'en' | 'ar';

export type TextDirection = 'ltr' | 'rtl';

export type SupportedLocale = {
    code: AppLocale;
    label: string;
    direction: TextDirection;
};

export type Localization = {
    locale: AppLocale;
    direction: TextDirection;
    supportedLocales: SupportedLocale[];
};
