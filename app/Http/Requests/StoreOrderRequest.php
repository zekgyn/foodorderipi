<?php

namespace App\Http\Requests;

use App\Models\Menu;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
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
        return [
            'items' => 'bail|required|array|min:1',
            'items.*.employee_id' => ['required','distinct', function ($attribute, $value, $fail) {
                if (!Employee::where([
                    ['id', '=', $value]
                ])->exists() || !Employee::where([
                    ['id', '=', $value],
                    ['is_active', '=', true]
                ])->exists()) {
                    return $fail("{$attribute} does not exist");
                }
            }],
            'items.*.menu' => 'bail|required|array',
            'items.*.menu.*.id' => [
                'required'
            //     ,Rule::forEach(function ($attribute) {
            //     return [
            //         Rule::distinct($attribute),
            //     ];
            // })
            , function ($attribute, $value, $fail) {
                if (!Menu::where([
                    ['id', '=', $value]
                ])->exists() || !Menu::where([
                    ['id', '=', $value],
                    ['is_active', '=', true]
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                }
            }],
            'items.*.menu.*.qty' => 'bail|required|numeric|min:1',
        ];
    }
}
