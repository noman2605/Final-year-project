<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isOrganizer();
    }

    public function rules(): array
    {
        return [
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['required', 'string'],
            'date'                 => ['required', 'date', 'after:now'],
            'location'             => ['required', 'string', 'max:255'],
            'image'                => ['nullable', 'url', 'max:2048'],
            'status'               => ['required', 'in:draft,published'],
            'categories'           => ['required', 'array', 'min:1'],
            'categories.*.name'    => ['required', 'string', 'max:100'],
            'categories.*.price'   => ['required', 'numeric', 'min:0'],
            'categories.*.capacity'=> ['required', 'integer', 'min:1'],
        ];
    }
}
