<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class LotDateRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lot' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function startDate(): string
    {
        return $this->validated('start_date') ?? now()->subDays(30)->toDateString();
    }

    public function endDate(): string
    {
        return $this->validated('end_date') ?? now()->toDateString();
    }
}