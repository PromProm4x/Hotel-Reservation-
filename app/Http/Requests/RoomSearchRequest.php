<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:10'],
            'room_type_id' => ['nullable', 'integer', 'exists:room_types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_out.after' => 'Check-out must be at least one night after check-in.',
        ];
    }
}
