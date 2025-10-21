<?php

namespace App\Http\Requests\FeatureFlags;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:feature_flags,key'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_enabled' => ['boolean'],
            'rollout_type' => ['required', Rule::in(['boolean', 'percentage', 'scheduled', 'user_list'])],
            'rollout_value' => ['nullable', 'array'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Feature flag key is required',
            'key.unique' => 'Feature flag key must be unique',
            'name.required' => 'Feature flag name is required',
            'rollout_type.in' => 'Rollout type must be one of: boolean, percentage, scheduled, user_list',
            'ends_at.after' => 'End date must be after start date',
        ];
    }
}
