<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:'.config('attribute.title.min'),
                'max:'.config('attribute.title.max')
            ],
            'content' => [
                'required',
                'string',
                'min:'.config('attribute.content.min'),
                'max:'.config('attribute.content.max')
            ],
            'slug' => [
                'nullable',
                'string',
                'max:'.config('attribute.title.max')
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:'.join(",", config('file.allowed_extensions')),
                'max:'.config('file.max_size')
            ]
        ];
    }
}
