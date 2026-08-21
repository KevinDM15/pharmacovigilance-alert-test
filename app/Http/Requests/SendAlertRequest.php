<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lot' => ['required', 'string', 'exists:medications,lot_number'],
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
        ];
    }
}
