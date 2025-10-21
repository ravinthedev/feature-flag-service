<?php

namespace App\Http\Requests\FeatureFlags;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_enabled' => ['sometimes', 'boolean'],
            'rollout_type' => ['sometimes', Rule::in(['boolean', 'percentage', 'scheduled', 'user_list'])],
            'rollout_value' => ['nullable', 'array'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'rollout_type.in' => 'Rollout type must be one of: boolean, percentage, scheduled, user_list',
            'ends_at.after' => 'End date must be after start date',
        ];
    }
}
