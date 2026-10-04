<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use App\Support\Registration\ParticipantRegistrationView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LogicException;

final class ParticipantDashboardController extends Controller
{
    public function __invoke(
        Request $request,
        ParticipantRegistrationView $view,
    ): Response {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException(
                'Authenticated participant is required.',
            );
        }

        $registration = Registration::query()
            ->whereHas(
                'membership',
                fn ($query) => $query->where('user_id', $user->id),
            )
            ->latest('created_at')
            ->first();

        return Inertia::render('Dashboard', [
            'phase02' => [
                'registration' => $registration === null
                    ? null
                    : $view->build($registration),
            ],
        ]);
    }
}
