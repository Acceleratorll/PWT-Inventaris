<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'order_type_id' => 'required|string',
            'code' => 'required|string',
            'outed_date' => 'required|date',
            'desc' => 'nullable',
            'selected_products' => 'required|array',
            'selected_products.*.location_ids.*.amount' => 'gte:selected_products.*.location_ids.*.oriAmount',
        ];
    }

    public function messages()
    {
        return [
            'selected_products.*.location_ids.*.amount.gte' => 'The amount must not exceed the original amount (oriAmount).',
        ];
    }
}
