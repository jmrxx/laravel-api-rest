<?php

namespace App\Http\Validate;

use Illuminate\Foundation\Http\FormRequest;

class UserStore extends FormRequest {
    public function authorize() {
        return true;
    }

    
    public function rules() {
        return [
            'username' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'perfil_picture' => 'nullable|string',
            'biography' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }
}