<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'supplier_id' => 'required',
            'selected_products' => 'required',
            'code' => 'required',
            'purchase_date' => 'required',
            'note' => '',
            'status' => 'nullable',
        ];
    }
}
