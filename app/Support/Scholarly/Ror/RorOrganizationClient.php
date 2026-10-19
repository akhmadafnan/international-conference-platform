<?php

namespace App\Support\Scholarly\Ror;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class RorOrganizationClient
{
    private const BASE = 'https://api.ror.org/v2/organizations';

    /** @return array<int, array{ror_uri: string, name: string, country_code: string|null, country: string|null, city: string|null}>|null */
    public function search(string $query): ?array
    {
        $key = 'ror:search:'.hash('sha256', mb_strtolower($query));

        return Cache::remember($key, now()->addHours(3), function () use ($query): ?array {
            try {
                $response = Http::acceptJson()->timeout(6)->get(self::BASE, ['query' => $query]);
                if (! $response->successful()) {
                    return null;
                }
                $items = $response->json('items');
                if (! is_array($items)) {
                    return null;
                }

                return collect($items)->take(10)->map(fn ($item) => $this->normalize($item))
                    ->filter()->values()->all();
            } catch (\Throwable) {
                // An external outage must never prevent a truthful manual affiliation.
                return null;
            }
        });
    }

    /** @return array{ror_uri: string, name: string, country_code: string|null, country: string|null, city: string|null}|null */
    public function find(string $rorUri): ?array
    {
        if (! preg_match('~^https://ror\.org/(0[a-hj-km-np-tv-z0-9]{6}[0-9]{2})$~', $rorUri, $match)) {
            return null;
        }
        $key = 'ror:record:'.$match[1];

        return Cache::remember($key, now()->addDay(), function () use ($match): ?array {
            try {
                $response = Http::acceptJson()->timeout(6)->get(self::BASE.'/'.$match[1]);

                return $response->successful() ? $this->normalize($response->json()) : null;
            } catch (\Throwable) {
                return null;
            }
        });
    }

    /** @return array{ror_uri: string, name: string, country_code: string|null, country: string|null, city: string|null}|null */
    private function normalize(mixed $raw): ?array
    {
        if (! is_array($raw) || ($raw['status'] ?? 'active') !== 'active') {
            return null;
        }
        $ror = $raw['id'] ?? null;
        if (! is_string($ror) || ! preg_match('~^https://ror\.org/0[a-hj-km-np-tv-z0-9]{6}[0-9]{2}$~', $ror)) {
            return null;
        }
        $names = $raw['names'] ?? [];
        if (! is_array($names)) {
            return null;
        }
        $display = null;
        foreach ($names as $entry) {
            if (is_array($entry) && is_string($entry['value'] ?? null) &&
                in_array('ror_display', (array) ($entry['types'] ?? []), true)) {
                $display = trim($entry['value']);
                break;
            }
        }
        if ($display === null) {
            foreach ($names as $entry) {
                if (is_array($entry) && is_string($entry['value'] ?? null)) {
                    $display = trim($entry['value']);
                    break;
                }
            }
        }
        if ($display === null || $display === '') {
            return null;
        }

        $geo = $raw['locations'][0]['geonames_details'] ?? [];
        $countryCode = is_string($geo['country_code'] ?? null) ? $geo['country_code'] : null;
        $country = is_string($geo['country_name'] ?? null) ? $geo['country_name'] : null;
        $city = is_string($geo['name'] ?? null) ? $geo['name'] : null;

        return [
            'ror_uri' => $ror,
            'name' => $display,
            'country_code' => $countryCode,
            'country' => $country,
            'city' => $city,
        ];
    }
}
