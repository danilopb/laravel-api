<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkPostRequest extends FormRequest
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
    public function rules()
    {
        $data = $this->validationData();
        $sizeArray = $data['title'] ? count($data['title']) : 1;

        return [
            'title' => ['required', 'array', 'min:1'],
            'content' => ['required', 'array', 'min:'.$sizeArray, 'max:'.$sizeArray],
            'title.*' => [
                'required',
                'string',
                'min:'.config('attribute.title.min'),
                'max:'.config('attribute.title.max')
            ],
            'content.*' => [
                'required',
                'string',
                'min:'.config('attribute.content.min'),
                'max:'.config('attribute.content.max')
            ]
        ];
    }
}
