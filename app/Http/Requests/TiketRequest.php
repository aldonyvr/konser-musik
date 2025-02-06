<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TiketRequest extends FormRequest
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
            // 'users_id' => '',
            'konsers_id' => '',
            'harga_vip' => '',
            'harga_regular' => '',
            'reguler' => '',
            'vip' => '',
            'opengate' => '',
            'closegate' => '',
            'gate_a_capacity' => '',
            'gate_b_capacity' => '',
            'gate_c_capacity' => '',
            'gate_d_capacity' => '',
            'gate_e_capacity' => '',
            
        ];
    }
}
