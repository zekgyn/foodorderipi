<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class SendOrderRequest extends FormRequest
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
            'order_id' => ['required', 'distinct', function ($attribute, $value, $fail) {
                if (!Order::where([
                    ['id', '=', $value],
                ])->exists()) {
                    return $fail("{$attribute} does not exist in the menu");
                } elseif (Order::where([
                    ['id', '=', $value],
                    ['is_placed', '=', true],
                ])->exists()) {
                    return $fail("{$attribute} has already been sent");
                }
            }]

        ];
    }
}
