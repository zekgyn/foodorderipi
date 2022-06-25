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
            'menu_id' => ['required', function ($attribute, $value, $fail) {
                $menuid = request()->menu_id;
                if (!Menu::where([
                    // ['title', '=', $value],
                    ['id', '=', $menuid]
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                }
            }],
            'phone' => 'bail|required|string',
            'location' => 'bail|required|string',

        ];
    }
}
