<?php

namespace App\Http\Requests\Admin\Courses;

use Illuminate\Foundation\Http\FormRequest;

class EditCoursesRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'image' => 'sometimes|mimes:png,jpg,jpeg|max:2048',
            'price' => 'sometimes|numeric',
            'duration' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'level_id' => 'sometimes|exists:levels,id',
            'status' => 'sometimes|in:active,inactive,coming soon',
        ];
    }
}
