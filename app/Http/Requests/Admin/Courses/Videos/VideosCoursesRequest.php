<?php

namespace App\Http\Requests\Admin\Courses\Videos;

use Illuminate\Foundation\Http\FormRequest;

class VideosCoursesRequest extends FormRequest
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
            'videos' => 'required|array',
            'videos.*' => 'required|file|mimes:mp4,mov,ogg|max:102400',
            'uuid' => 'required|uuid',
        ];
    }
}
