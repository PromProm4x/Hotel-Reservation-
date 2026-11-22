<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomSearchRequest;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\ReservationService;

class AvailabilityController extends Controller
{
    public function __construct(
        protected AvailabilityService $availability,
        protected ReservationService $reservations,
    ) {}

    /**
     * GET /api/v1/availability
     */
    public function __invoke(RoomSearchRequest $request)
    {
        $criteria = $request->validated();

        $results = $this->availability
            ->searchRoomTypes(
                $criteria['check_in'],
                $criteria['check_out'],
                (int) $criteria['guests'],
                $criteria['room_type_id'] ?? null,
            )
            ->map(fn (RoomType $roomType) => [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'slug' => $roomType->slug,
                'base_price' => (float) $roomType->base_price,
                'capacity_adults' => $roomType->capacity_adults,
                'capacity_children' => $roomType->capacity_children,
                'amenities' => $roomType->amenities,
                'photos' => $roomType->photos,
                'available_rooms' => $roomType->available_rooms_count,
                'quote' => $this->reservations->quote($roomType, $criteria['check_in'], $criteria['check_out']),
            ]);

        return response()->json([
            'criteria' => $criteria,
            'results' => $results,
        ]);
    }
}
