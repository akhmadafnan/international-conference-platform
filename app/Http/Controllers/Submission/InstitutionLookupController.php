<?php

namespace App\Http\Controllers\Submission;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Support\Scholarly\Ror\RorOrganizationClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class InstitutionLookupController extends Controller
{
    public function search(Request $request, RorOrganizationClient $ror): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:120'],
        ]);
        $query = trim($data['q']);
        if (mb_strlen($query) < 3) {
            throw ValidationException::withMessages(['q' => 'Minimum three characters required.']);
        }

        // Server searches cached local selections before consulting external ROR.
        $escaped = addcslashes($query, '%_\\');
        $local = Institution::query()->where('preferred_name', 'like', '%'.$escaped.'%')
            ->limit(8)->get()->map(fn (Institution $item): array => [
                'source' => 'local',
                'id' => $item->id,
                'ror_uri' => $item->ror_uri,
                'name' => $item->preferred_name,
                'country_code' => $item->country_code,
                'country' => null,
                'city' => null,
            ])->all();

        $remote = $ror->search($query);
        $knownRor = array_filter(array_column($local, 'ror_uri'));
        $suggestions = collect($remote ?? [])->reject(fn (array $item) => in_array($item['ror_uri'], $knownRor, true))
            ->map(fn (array $item): array => [
                'source' => 'ror',
                'id' => null,
                ...$item,
            ])->values()->all();

        return response()->json([
            'items' => array_slice(array_merge($local, $suggestions), 0, 12),
            'ror_available' => $remote !== null,
            'manual_allowed' => true,
        ]);
    }

    public function resolve(Request $request, RorOrganizationClient $ror): JsonResponse
    {
        $data = $request->validate([
            'ror_uri' => ['required', 'string', 'regex:~^https://ror\.org/0[a-hj-km-np-tv-z0-9]{6}[0-9]{2}$~'],
        ]);

        // Look up the canonical record server-side: never trust a client-provided
        // organization name or allow arbitrary remote URL fetching (SSRF).
        $organization = $ror->find($data['ror_uri']);
        if ($organization === null) {
            throw ValidationException::withMessages(['ror_uri' => 'ROR_ORGANIZATION_UNAVAILABLE']);
        }

        $institution = DB::transaction(function () use ($organization): Institution {
            return Institution::query()->firstOrCreate(
                ['ror_uri' => $organization['ror_uri']],
                [
                    'preferred_name' => $organization['name'],
                    'country_code' => $organization['country_code'],
                    'source' => 'ROR',
                ],
            );
        });

        return response()->json([
            'id' => $institution->id,
            'name' => $institution->preferred_name,
            'country_code' => $institution->country_code,
            'ror_uri' => $institution->ror_uri,
        ]);
    }
}
