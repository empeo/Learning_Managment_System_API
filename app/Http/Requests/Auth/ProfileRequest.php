<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        $userId = $this->user()->id;
        return [
            "first_name"=>"sometimes|min:6|max:15",
            "last_name"=>"sometimes|min:6|max:20",
            "email"=>"sometimes|email|unique:users,email,".$userId,
            "password"=>"sometimes|min:6",
            "password_confirmed"=>"sometimes|min:6|same:password",
            "phone"=>"sometimes|digits:11|unique:users,phone,".$userId,
            "gender"=>"sometimes|in:male,female",
            "image"=>"sometimes|mimes:png,jpg,jpeg",
        ];
    }
}
