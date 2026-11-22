<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $guests = Guest::query()
            ->withCount('reservations')
            ->withMax('reservations', 'check_in')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where(function ($query) use ($term) {
                    $query->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }

    public function show(Guest $guest)
    {
        $guest->load(['reservations' => fn ($q) => $q->with(['roomType', 'room'])->orderByDesc('check_in')]);

        return view('admin.guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('admin.guests.edit', compact('guest'));
    }

    /**
     * Update the guest profile (full edit form) or just CRM notes/preferences
     * (inline form on the show page). Identity fields use `sometimes` so both
     * forms can post to this action.
     */
    public function update(Request $request, Guest $guest)
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => [
                'sometimes', 'required', 'email', 'max:255',
                Rule::unique('guests', 'email')->ignore($guest->id)->whereNull('deleted_at'),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'preferences' => ['nullable', 'string', 'max:2000'], // comma-separated
        ]);

        if ($request->exists('preferences')) {
            $data['preferences'] = collect(explode(',', (string) ($data['preferences'] ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        $guest->update($data);

        return redirect()
            ->route('admin.guests.show', $guest)
            ->with('status', 'Guest profile updated.');
    }

    /**
     * Soft-delete a guest profile. Guests with an upcoming or in-house
     * reservation cannot be deleted; past bookings are kept for the audit trail.
     */
    public function destroy(Guest $guest)
    {
        $hasActiveStays = $guest->reservations()
            ->whereIn('status', ReservationStatus::blockingValues())
            ->where('check_out', '>=', now()->toDateString())
            ->exists();

        if ($hasActiveStays) {
            return back()->with('status', 'This guest has an upcoming or in-house reservation and cannot be deleted.');
        }

        $guest->delete();

        return redirect()
            ->route('admin.guests.index')
            ->with('status', 'Guest profile deleted. Past bookings are kept for the audit trail.');
    }
}
