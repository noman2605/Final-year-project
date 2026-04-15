<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAttendee();
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
        ];
    }
}
