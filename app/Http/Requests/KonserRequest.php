<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KonserRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'lokasi' => 'required|string|max:255',
            'tiket_tersedia' => 'required|integer|min:0',
            'kontak' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'nama_sosmed' => 'required|string|max:255',
            'image' => 'required|file|mimes:jpg,png,jpeg|max:2048', // Batas ukuran 2MB
        ];
    }
}
