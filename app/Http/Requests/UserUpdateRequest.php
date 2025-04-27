<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'perfil_picture' => 'nullable|string|regex:/\.(jpg|jpeg|png|gif|bmp|svg)$/i',
            'biography' => 'nullable|string|max:1000',
            'role' => 'sometimes|string|exists:roles,name',
            'email' => 'sometimes|required|email|unique:users,email,' . $this->route('id'),
            'password' => 'sometimes|nullable|string|min:8|confirmed',
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
            'role.exists' => 'The role must exist in the system.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
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