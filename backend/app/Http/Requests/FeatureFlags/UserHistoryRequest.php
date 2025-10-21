<?php

namespace App\Http\Requests\FeatureFlags;

use Illuminate\Foundation\Http\FormRequest;

class UserHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'limit' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
