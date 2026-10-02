<?php

use App\Models\User;
use Illuminate\Support\Str;

test('user primary key is UUIDv7', function () {
    $user = User::factory()->create();

    expect($user->getKey())
        ->toBeString()
        ->and(Str::isUuid($user->getKey(), 7))
        ->toBeTrue()
        ->and($user->getIncrementing())
        ->toBeFalse()
        ->and($user->getKeyType())
        ->toBe('string');
});
