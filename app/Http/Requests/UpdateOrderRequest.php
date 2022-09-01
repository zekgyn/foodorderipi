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
            'add_items' => 'array',
            'add_items.*.employee_id' => ['required', 'string',  function ($attribute, $value, $fail) {
                if (!Employee::where([
                    ['id', '=', $value]
                ])->exists()) {
                    return $fail("{$attribute} does not exist");
                }
            }],
            'add_items.*.menu' => 'bail|required|array',
            'add_items.*.menu.*.id' => [
                'bail','required'
                , function ($attribute, $value, $fail) {
                    if (!Menu::where([
                        ['id', '=', $value]
                    ])->exists() || !Menu::where([
                        ['id', '=', $value],
                        ['is_active', '=', true]
                    ])->exists()) {
                        return $fail("{$attribute} does not exist");
                    }
                }
            ],
            'add_items.*.menu.*.qty' => 'bail|required|numeric|min:1',
            'delete_items' => 'present|nullable|array',
            'delete_items.*' => 'required|distinct|exists:order_items,id'
        ];
    }
}
