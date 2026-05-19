<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|string|min:8|max:255',

            'phone' => 'required|string|min:10|max:20',

            'role' => 'required|in:customer,driver,mechanic'
        ];
    }
}