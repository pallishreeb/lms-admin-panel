<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cover_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max size as needed
            'chapters' => 'nullable|array',
            'chapters.*.chapter_number' => 'required|integer|min:1',
            'chapters.*.title' => 'required|string|max:255',
            'chapters.*.description' => 'required|string',
            'chapters.*.video_file' => 'nullable|mimes:mp4|max:200000', // Adjust max size as needed
            'chapters.*.video_duration' => 'nullable|string|max:255',
            'chapters.*.video_type' => 'nullable|string|max:255',
            'chapters.*.attachment' => 'nullable|mimes:pdf|max:50000', // Adjust max size as needed
        ];
    }
}
