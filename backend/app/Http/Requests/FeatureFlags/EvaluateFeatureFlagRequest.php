<?php

namespace App\Http\Requests\FeatureFlags;

use Illuminate\Foundation\Http\FormRequest;

class EvaluateFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'context' => ['array'],
            'context.user_id' => ['nullable'],
            'context.user_email' => ['nullable', 'email'],
            'context.user_role' => ['nullable', 'string'],
            'context.session_id' => ['nullable', 'string'],
        ];
    }
}
