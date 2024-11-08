<?php

namespace App\Http\Requests\Admin\Courses\Videos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideosCoursesRequest extends FormRequest
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
            'video' => "required|file|mimes:mp4,mov,ogg|max:102400",
            'uuid' => "required|uuid"
        ];
    }
}
