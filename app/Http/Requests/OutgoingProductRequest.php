<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutgoingProductRequest extends FormRequest
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
            'process_plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'qty' => 'required|numeric',
        ];
    }
}
