<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'perfil_picture' => 'nullable|string|regex:/\.(jpg|jpeg|png|gif|bmp|svg)$/i',
            'biography' => 'nullable|string|max:1000',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages() 
    {
        return [
            'username.required' => 'The username is required.',
            'lastname.required' => 'The lastname is required.',
            'perfil_picture.regex' => 'The perfil picture must have a valid image extension (jpg, jpeg, png, gif, bmp, svg).',
            'biography.string' => 'The biography must be a string.',
            'biography.max' => 'The biography must not exceed 1000 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}