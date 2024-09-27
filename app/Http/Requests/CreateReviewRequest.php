<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
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
            'service_provider_id' => 'required|exists:service_providers,id',
            'rating'              => 'required|integer|min:1|max:5',
            'qualities'           => 'nullable|array|max:3',
            'qualities.*'         => 'string|in:Friendly,Timeliness,Professionalism,Cleanliness',
            'comment'            => 'nullable|string|max:1000',
            'images'              => 'nullable|array|max:3',
            'images.*'            => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}