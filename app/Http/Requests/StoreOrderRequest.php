<?php

namespace App\Http\Requests;

use App\Models\Menu;
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
            'menus' => 'bail|required|array',
            'menus.*.menu_id' => ['required', function ($attribute, $value, $fail) {
                // $menuid = request()->menu_id;
                if (!Menu::where([
                    ['id', '=', $value]
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                }
            }],
            'menus.*.name' => 'bail|required|string',

        ];
    }
}
