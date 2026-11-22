<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $roomId = $this->route('room')?->id;

        return [
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_number' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms', 'room_number')->ignore($roomId)->whereNull('deleted_at'),
            ],
            'floor' => ['required', 'integer', 'min:0', 'max:200'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
