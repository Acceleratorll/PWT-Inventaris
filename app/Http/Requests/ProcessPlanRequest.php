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
            'order_type' => 'required|string',
            'code' => 'required|string',
            'desc' => 'nullable|string',
            'selected_products' => 'required|array'
        ];
    }
}
