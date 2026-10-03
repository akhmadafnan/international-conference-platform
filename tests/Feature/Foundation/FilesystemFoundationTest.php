<?php

use Illuminate\Support\Facades\Storage;

test('application storage defaults to the canonical private disk', function () {
    expect(config('filesystems.default'))
        ->toBe('private')
        ->and(config('filesystems.disks.private.driver'))
        ->toBe('local')
        ->and(config('filesystems.disks.private.root'))
        ->toBe(storage_path('app/private'))
        ->and(config('filesystems.disks.private.visibility'))
        ->toBe('private')
        ->and(array_key_exists('serve', config('filesystems.disks.private')))
        ->toBeFalse();
});

test('laravel local compatibility disk remains private', function () {
    expect(config('filesystems.disks.local.root'))
        ->toBe(storage_path('app/private'))
        ->and(config('filesystems.disks.local.visibility'))
        ->toBe('private');
});

test('public filesystem is explicitly separated from protected storage', function () {
    $links = config('filesystems.links', []);

    expect(config('filesystems.disks.public.root'))
        ->toBe(storage_path('app/public'))
        ->and(config('filesystems.disks.public.visibility'))
        ->toBe('public')
        ->and($links[public_path('storage')] ?? null)
        ->toBe(storage_path('app/public'))
        ->and(array_values($links))
        ->not->toContain(storage_path('app/private'))
        ->and(array_key_exists('url', config('filesystems.disks.private')))
        ->toBeFalse();
});

test('private and public disks remain independent storage boundaries', function () {
    Storage::fake('private');
    Storage::fake('public');

    Storage::disk('private')->put('protected/foundation-probe.txt', 'protected');
    Storage::disk('public')->put('published/foundation-probe.txt', 'published');

    expect(Storage::disk('private')->exists('protected/foundation-probe.txt'))
        ->toBeTrue()
        ->and(Storage::disk('private')->exists('published/foundation-probe.txt'))
        ->toBeFalse()
        ->and(Storage::disk('public')->exists('published/foundation-probe.txt'))
        ->toBeTrue()
        ->and(Storage::disk('public')->exists('protected/foundation-probe.txt'))
        ->toBeFalse();
});
