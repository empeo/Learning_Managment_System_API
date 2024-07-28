<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            "first_name"=>"required|min:6|max:15",
            "last_name"=>"required|min:6|max:20",
            "email"=>"required|email|unique:users,email",
            "password"=>"required|min:6",
            "password_confirmed"=>"required|min:6|same:password",
            "phone"=>"required|digits:11|unique:users,phone",
            "gender"=>"required|in:male,female",
            "image"=>"required|mimes:png,jpg,jpeg|max:2048",
        ];
    }
}
