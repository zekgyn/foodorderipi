<?php

namespace App\Http\Requests;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        return [
            'add_item' => 'array',
            'add_item.*.menu_id' => ['required', 'string',  function ($attribute, $value, $fail) {
                if (!Menu::where([
                    ['id', '=', $value]
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                }
            }],
             'add_item.*.employee_id' => [
                'bail','required','string',
                function ($attribute, $value, $fail) {
                    if (!Employee::where([
                        ['id', '=', $value]
                    ])->exists()) {
                      return $fail("{$attribute} does not exist");
                 }
            }],
            'delete_item' => 'present|nullable|array',
            'delete_item.*' => 'required|distinct|exists:order_items,id'
        ];
    }
}
