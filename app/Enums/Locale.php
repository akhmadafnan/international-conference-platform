<?php

namespace App\Enums;

enum Locale: string
{
    case Indonesian = 'id';
    case English = 'en';
    case Arabic = 'ar';

    public function direction(): string
    {
        return $this === self::Arabic ? 'rtl' : 'ltr';
    }

    public function nativeLabel(): string
    {
        return match ($this) {
            self::Indonesian => 'Bahasa Indonesia',
            self::English => 'English',
            self::Arabic => 'العربية',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $locale): string => $locale->value,
            self::cases(),
        );
    }
}
