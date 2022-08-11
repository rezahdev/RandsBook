<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strip_tags($this['name']),
            'description' => strip_tags($this['description']),
            'total_pages' => strip_tags($this['total_pages']),
            'pages_read' => strip_tags($this['pages_read']),
            'comment' => strip_tags($this['comment'])
        ]);
    }
}
