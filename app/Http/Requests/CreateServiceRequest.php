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
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'service_type' => 'nullable|in:online,in-person',
            'status' => 'nullable|in:active,inactive',
         ];
    }
}
