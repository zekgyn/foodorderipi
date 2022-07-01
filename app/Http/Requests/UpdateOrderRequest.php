<?php

namespace App\Http\Requests;

use App\Models\Menu;
use App\Models\Order;
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
            'add_menus' => 'array',
            'add_menus.*.menu_id' => ['required',  function ($attribute, $value, $fail) {
                if (!Menu::where([
                    ['id', '=', $value]
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                }
            }],
            'add_menus.*.name' => 'bail|required|string',
            'delete_menus' => 'present|nullable|array',
            'delete_menus.*' => 'required|distinct|exists:order_items,id'
        ];
    }
}
