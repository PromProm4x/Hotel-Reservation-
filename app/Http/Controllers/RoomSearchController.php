<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomSearchRequest;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\ReservationService;

class RoomSearchController extends Controller
{
    public function __construct(
        protected AvailabilityService $availability,
        protected ReservationService $reservations,
    ) {}

    /**
     * Search form + availability results.
     */
    public function index(RoomSearchRequest $request)
    {
        $roomTypes = RoomType::query()->active()->orderBy('base_price')->get();
        $results = null;
        $criteria = null;

        if ($request->filled(['check_in', 'check_out'])) {
            $criteria = $request->validated();

            $results = $this->availability
                ->searchRoomTypes(
                    $criteria['check_in'],
                    $criteria['check_out'],
                    (int) $criteria['guests'],
                    $criteria['room_type_id'] ?? null,
                )
                ->map(function (RoomType $roomType) use ($criteria) {
                    $roomType->setAttribute(
                        'quote',
                        $this->reservations->quote($roomType, $criteria['check_in'], $criteria['check_out']),
                    );

                    return $roomType;
                });
        }

        return view('rooms.search', compact('roomTypes', 'results', 'criteria'));
    }

    /**
     * Room type detail page.
     */
    public function show(RoomType $roomType)
    {
        abort_unless($roomType->is_active, 404);

        $reviews = $roomType->reviews()
            ->where('is_approved', true)
            ->with('guest')
            ->latest()
            ->take(6)
            ->get();

        return view('rooms.show', compact('roomType', 'reviews'));
    }
}
