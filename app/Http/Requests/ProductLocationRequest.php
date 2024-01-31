<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductLocationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'location_id' => 'required',
            // 'amount' => 'required',
            // 'expired' => 'required',
            'rpp_id' => '',
            'purchase_date' => '',
            'transaction_id' => '',
            'selected_products' => 'required',
        ];
    }
}
