<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPlanningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required',
            'nota_dinas_id' => 'required',
            'requirement_amount' => 'required',
            'product_amount ' => 'required',
            'procurement_plan_amount ' => 'required',
        ];
    }
}
