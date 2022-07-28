<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Indicates whether validation should stop after the first rule failure.
     *
     * @var bool
     */
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
            'email' => [
                'bail', 'required', 'email',
                function ($attribute, $value, $fail) {
                    $user = User::where([
                        ['email', '=', strtolower($value)],
                    ])->exists();
                    if (!$user) {
                        $fail('Email does not exists!');
                    }
                },
            ],
            'password' => 'required|string',
        ];
    }
}
