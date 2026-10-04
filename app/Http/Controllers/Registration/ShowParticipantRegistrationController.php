<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\ConferenceEdition;
use App\Models\User;
use App\Support\Registration\ParticipantRegistrationView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LogicException;

final class ShowParticipantRegistrationController extends Controller
{
    public function __invoke(
        Request $request,
        ConferenceEdition $edition,
        ParticipantRegistrationView $view,
    ): Response {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException(
                'Authenticated participant is required.',
            );
        }

        $edition->loadMissing('series');

        $membership = $edition->memberships()
            ->where('user_id', $user->id)
            ->first();

        $registration = $membership?->registration()->first();

        $packages = $edition->participationPackages()
            ->where('active', true)
            ->orderBy('display_order')
            ->orderBy('code')
            ->get();

        return Inertia::render('registration/Show', [
            'edition' => [
                'id' => $edition->id,
                'code' => $edition->edition_code,
                'year' => $edition->year,
                'seriesName' => $edition->series?->name,
                'theme' => $edition->theme_i18n,
            ],
            'packages' => $packages->map(
                static fn ($package): array => [
                    'id' => $package->id,
                    'code' => $package->code,
                    'name' => $package->name_i18n,
                    'description' => $package->description_i18n,
                    'billingMode' => $package->billing_mode->value,
                    'price' => $package->price,
                    'currencyCode' => $package->currency_code,
                ],
            )->values(),
            'registration' => $registration === null
                ? null
                : $view->build($registration),
            'routes' => [
                'store' => route('editions.registration.store', $edition),
                'dashboard' => route('dashboard'),
            ],
        ]);
    }
}
