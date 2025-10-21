<?php

namespace App\Http\Requests\CarReports;

use Illuminate\Foundation\Http\FormRequest;

class CreateCarReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'car_model' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'damage_type' => ['required', 'in:minor,moderate,severe,total_loss'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'car_model.required' => 'Car model is required',
            'description.required' => 'Description is required',
            'damage_type.required' => 'Damage type is required',
            'damage_type.in' => 'Damage type must be one of: minor, moderate, severe, total_loss',
        ];
    }
}
