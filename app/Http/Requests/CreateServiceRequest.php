<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'service_type' => 'nullable|in:online,in-person',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'status' => 'nullable|in:active,inactive,pending'
         ];
    }
}
