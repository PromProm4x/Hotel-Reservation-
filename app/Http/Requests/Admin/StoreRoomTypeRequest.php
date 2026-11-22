<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomTypeRequest extends FormRequest
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
        $roomTypeId = $this->route('room_type')?->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('room_types', 'name')->ignore($roomTypeId)->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'capacity_adults' => ['required', 'integer', 'min:1', 'max:10'],
            'capacity_children' => ['required', 'integer', 'min:0', 'max:10'],
            'size_sqm' => ['nullable', 'integer', 'min:0'],
            'amenities' => ['nullable', 'string', 'max:2000'], // comma-separated in the form
            'photos' => ['nullable', 'string', 'max:5000'], // newline-separated URLs in the form
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Normalized payload with amenities/photos as arrays.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->validated();

        $data['amenities'] = collect(explode(',', (string) ($data['amenities'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $data['photos'] = collect(preg_split('/\r?\n/', (string) ($data['photos'] ?? '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
