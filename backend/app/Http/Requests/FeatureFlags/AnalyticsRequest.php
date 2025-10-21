<?php

namespace App\Http\Requests\FeatureFlags;

use Illuminate\Foundation\Http\FormRequest;

class AnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'hours' => ['integer', 'min:1', 'max:168'],
        ];
    }
}
