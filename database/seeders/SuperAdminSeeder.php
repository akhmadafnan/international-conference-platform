<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('foundation.super_admin.enabled')) {
            return;
        }

        $email = trim(
            (string) config('foundation.super_admin.email'),
        );

        Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255']],
        )->validate();

        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user instanceof User) {
            $changed = false;

            if (! $user->isSuperAdmin()) {
                $user->is_super_admin = true;
                $changed = true;
            }

            if ($user->email_verified_at === null) {
                $user->email_verified_at = now();
                $changed = true;
            }

            if (! $changed) {
                return;
            }

            $user->save();

            activity('security')
                ->performedOn($user)
                ->event('superadmin_promoted')
                ->withProperties([
                    'source' => 'foundation_seeder',
                ])
                ->log('Existing user promoted to global superadmin');

            return;
        }

        $name = trim(
            (string) config('foundation.super_admin.name'),
        );

        $password = (string) config(
            'foundation.super_admin.password',
        );

        Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => [
                    'required',
                    'string',
                    Password::min(12)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ],
        )->validate();

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->is_super_admin = true;
        $user->email_verified_at = now();

        $user->save();

        activity('security')
            ->performedOn($user)
            ->event('superadmin_bootstrapped')
            ->withProperties([
                'source' => 'foundation_seeder',
            ])
            ->log('Initial global superadmin provisioned');
    }
}
