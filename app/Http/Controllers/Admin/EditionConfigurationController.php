<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Payment\ConfigurePaymentDestinationAction;
use App\Actions\Registration\ConfigureParticipationPackageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEditionDestinationRequest;
use App\Http\Requests\Admin\StoreEditionPackageRequest;
use App\Models\ConferenceEdition;
use App\Models\ParticipationPackage;
use App\Models\PaymentDestination;
use App\Models\User;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class EditionConfigurationController extends Controller
{
    public function show(Request $request, ConferenceEdition $edition): Response
    {
        $this->authorizeSuperAdmin($request);

        $destinations = $edition->paymentDestinations()
            ->orderByDesc('is_default')->orderBy('display_order')->orderBy('code')
            ->get();

        $packages = $edition->participationPackages()
            ->orderBy('display_order')->orderBy('code')
            ->get();

        return Inertia::render('admin/editions/Configuration', [
            'edition' => [
                'id' => $edition->id,
                'code' => $edition->edition_code,
                'status' => $edition->lifecycle_status,
            ],
            'destinations' => $destinations->map(fn (PaymentDestination $item): array => [
                'id' => $item->id,
                'code' => $item->code,
                'label' => $item->label,
                'bank' => $item->bank_name,
                'accountLast4' => mb_substr($item->account_number, -4),
                'isDefault' => $item->is_default,
                'active' => $item->active,
            ])->values()->all(),
            'packages' => $packages->map(fn (ParticipationPackage $item): array => [
                'id' => $item->id,
                'code' => $item->code,
                'names' => $item->name_i18n,
                'billingMode' => $item->billing_mode->value,
                'price' => $item->price,
                'currency' => $item->currency_code,
                'active' => $item->active,
            ])->values()->all(),
            'destinationCreated' => (bool) $request->session()->get('destination_created', false),
            'packageCreated' => (bool) $request->session()->get('package_created', false),
        ]);
    }

    public function storeDestination(
        StoreEditionDestinationRequest $request,
        ConferenceEdition $edition,
        ConfigurePaymentDestinationAction $action,
    ): RedirectResponse {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        try {
            $action->handle($edition, $actor, $request->validated());
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['code' => 'duplicate']);
        } catch (DomainException) {
            throw ValidationException::withMessages(['configuration' => 'invalid_destination']);
        }

        return to_route('admin.editions.configuration', $edition)->with('destination_created', true);
    }

    public function storePackage(
        StoreEditionPackageRequest $request,
        ConferenceEdition $edition,
        ConfigureParticipationPackageAction $action,
    ): RedirectResponse {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        try {
            $action->handle($edition, $actor, $request->validated());
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['code' => 'duplicate']);
        } catch (DomainException) {
            throw ValidationException::withMessages(['configuration' => 'invalid_package']);
        }

        return to_route('admin.editions.configuration', $edition)->with('package_created', true);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user() instanceof User && $request->user()->isSuperAdmin(), 403);
    }
}
