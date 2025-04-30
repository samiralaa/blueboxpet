<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


public function prepareForValidation()
{
    $this->merge([
        'product_id' => $this->normalizeToArray($this->product_id),
        'quantity'   => $this->normalizeToArray($this->quantity),
        'price'      => $this->normalizeToArray($this->price),
    ]);
}

private function normalizeToArray($value)
{
    if (is_array($value)) {
        // Handle array with a single string like ["13, 21"]
        if (count($value) === 1 && is_string($value[0]) && str_contains($value[0], ',')) {
            return array_map('intval', explode(',', $value[0]));
        }

        return array_map('intval', $value);
    }

    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_map('intval', $decoded);
        }

        return array_map('intval', explode(',', $value));
    }

    return [(int) $value];
}
 
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_name'   => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'phone_number'  => 'required|string|max:15',
             'another_phone'  => 'required|string|max:15',
             'delivery_fees'=>'nullable|string|max:15',
             'note'=>'nullable|string',
            'order_status'      => 'required|in:1,2,3,4,5',
            'another_phone' => 'nullable|string|max:15',
            'item'          => 'required|array|max:255',
            'quantity'      => 'required|array|min:1',
            'due' =>'nullable|string|max:15',
           'duenow' => 'nullable|boolean',
            "user_id" =>'required|integer|min:1',
            'product_id'    => 'required|array|min:1',
            'price'    => 'required|array|min:0',
        ];

    }
}
