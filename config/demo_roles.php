<?php

return [
    // Manual, explicitly opted-in local/test fixture only; never DatabaseSeeder.
    'enabled' => env('ICHES_DEMO_ROLE_SEED_ENABLED', false),
    'edition_code' => env('ICHES_DEMO_EDITION_CODE'),
    'password' => 'password',
];
