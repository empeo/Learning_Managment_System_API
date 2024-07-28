<?php

namespace App\Http\Requests\Admin\Courses;

use Illuminate\Foundation\Http\FormRequest;

class CreateCoursesRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'videos' => 'required|array',
            'videos.*' => 'required|mimes:mp4,mov,ogg|max:100000',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:levels,id',
            'status' => 'required|in:active,inactive,coming soon',
        ];
    }
}
