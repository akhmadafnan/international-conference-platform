<?php

namespace App\Http\Controllers\Registration;

use App\Actions\Registration\CreateRegistrationIntentAction;
use App\Actions\Registration\ResolveRegistrationFeeRequirementAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\StoreParticipantRegistrationRequest;
use App\Models\ConferenceEdition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
                'package_id' => __('The selected participation package is not available for this Conference Edition.'),
            ]);
        }

        $alreadyRegistered = $edition->memberships()
            ->where('user_id', $user->id)
            ->whereHas('registration')
            ->exists();

        if ($alreadyRegistered) {
            throw ValidationException::withMessages([
                'package_id' => __('You already have a registration for this Conference Edition.'),
            ]);
        }

        $category = $request->validated('participant_category');

        $registration = $create->handle(
            $user,
            $edition,
            $package,
            is_string($category) && trim($category) !== ''
                ? trim($category)
                : null,
        );

        $resolveFee->handle($registration);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Registration created.'),
        ]);

        return to_route('editions.registration.show', $edition, 303);
    }
}
