<?php

namespace App\Http\Requests;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if (Menu::where([
                        ['title', '=', strtolower($value)]
                    ])->exists()) {
                        return $fail("{$attribute} already exists");
                    }
                }
            ],
            'price' => 'required|regex:/^\d{1,16}+(\.\d{1,2})?$/'
            // 'image' => 'present|nullable',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'A title is required',
            'price.required' => 'price is required',
        ];
    }
}
