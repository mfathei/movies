<?php

namespace Modules\Movies\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListMoviesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:genres,id'],
            'popular' => ['nullable'],
            'rated' => ['nullable'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
