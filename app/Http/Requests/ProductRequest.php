<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'material_id' => 'required',
            'product_type_id' => 'required',
            'qualifier_id' => 'required',
            'category_product_id' => 'required',
            'product_code' => 'required',
            'name' => 'required',
            'minimal_amount' => 'required',
            'total_amount' => 'required',
            'note' => '',
        ];
    }
}
