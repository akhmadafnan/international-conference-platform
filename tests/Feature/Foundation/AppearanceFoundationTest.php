<?php

test('first visit defaults to light appearance', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee(
            "const appearance = 'light';",
            false,
        )
        ->assertDontSee(
            '<html lang="en" dir="ltr" class="dark">',
            false,
        );
});

test('explicit dark appearance remains persistent', function () {
    $this->withUnencryptedCookie('appearance', 'dark')
        ->get('/login')
        ->assertOk()
        ->assertSee(
            "const appearance = 'dark';",
            false,
        )
        ->assertSee(
            '<html lang="en" dir="ltr" class="dark">',
            false,
        );
});

test('explicit light appearance remains persistent', function () {
    $this->withUnencryptedCookie('appearance', 'light')
        ->get('/login')
        ->assertOk()
        ->assertSee(
            "const appearance = 'light';",
            false,
        )
        ->assertDontSee(
            '<html lang="en" dir="ltr" class="dark">',
            false,
        );
});

test('invalid appearance cookie falls back to light', function () {
    $this->withUnencryptedCookie('appearance', 'invalid')
        ->get('/login')
        ->assertOk()
        ->assertSee(
            "const appearance = 'light';",
            false,
        )
        ->assertDontSee(
            '<html lang="en" dir="ltr" class="dark">',
            false,
        );
});
