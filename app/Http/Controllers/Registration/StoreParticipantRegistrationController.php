<?php

namespace App\Http\Controllers\Registration;

use App\Actions\Registration\CreateRegistrationIntentAction;
use App\Actions\Registration\ResolveRegistrationFeeRequirementAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\StoreParticipantRegistrationRequest;
use App\Models\ConferenceEdition;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use LogicException;

final class StoreParticipantRegistrationController extends Controller
{
    public function __invoke(
        StoreParticipantRegistrationRequest $request,
        ConferenceEdition $edition,
        CreateRegistrationIntentAction $create,
        ResolveRegistrationFeeRequirementAction $resolveFee,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException(
                'Authenticated participant is required.',
            );
        }

        $package = $edition->participationPackages()
            ->whereKey((string) $request->validated('package_id'))
            ->where('active', true)
            ->first();

        if ($package === null) {
            throw ValidationException::withMessages([
                'package_id' => 'PACKAGE_UNAVAILABLE',
            ]);
        }

        $alreadyRegistered = $edition->memberships()
            ->where('user_id', $user->id)
            ->whereHas('registration')
            ->exists();

        if ($alreadyRegistered) {
            throw ValidationException::withMessages([
                'package_id' => 'ALREADY_REGISTERED',
            ]);
        }

        $category = $request->validated('participant_category');

        try {
            DB::transaction(function () use (
                $user,
                $edition,
                $package,
                $category,
                $create,
                $resolveFee,
            ): void {
                $registration = $create->handle(
                    $user,
                    $edition,
                    $package,
                    is_string($category) && trim($category) !== ''
                        ? trim($category)
                        : null,
                );

                $resolveFee->handle($registration);
            });
        } catch (DomainException) {
            throw ValidationException::withMessages([
                'package_id' => 'REGISTRATION_UNAVAILABLE',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Registration created.'),
        ]);

        return to_route('editions.registration.show', $edition, 303);
    }
}
