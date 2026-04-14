<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');
        return $this->user()
            && $this->user()->isOrganizer()
            && $event
            && $event->organizer_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['required', 'string'],
            'date'                  => ['required', 'date'],
            'location'              => ['required', 'string', 'max:255'],
            'image'                 => ['nullable', 'url', 'max:2048'],
            'status'                => ['required', 'in:draft,published'],
            'categories'            => ['nullable', 'array'],
            'categories.*.id'       => ['nullable', 'integer', 'exists:ticket_categories,id'],
            'categories.*.name'     => ['required_with:categories', 'string', 'max:100'],
            'categories.*.price'    => ['required_with:categories', 'numeric', 'min:0'],
            'categories.*.capacity' => ['required_with:categories', 'integer', 'min:1'],
        ];
    }
}
