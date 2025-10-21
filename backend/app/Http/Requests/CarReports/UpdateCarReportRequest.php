<?php

namespace App\Http\Requests\CarReports;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'car_model' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'damage_type' => ['sometimes', 'in:minor,moderate,severe,total_loss'],
            'photo' => ['sometimes', 'nullable', 'image', 'max:10240'],
            'status' => ['sometimes', 'in:pending,in_progress,completed,rejected'],
        ];
    }

    public function messages(): array
    {
        return [
            'damage_type.in' => 'Damage type must be one of: minor, moderate, severe, total_loss',
            'status.in' => 'Status must be one of: pending, in_progress, completed, rejected',
        ];
    }
}
