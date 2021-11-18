<?php

namespace App\Http\Requests;

use App\Models\ConfirmToken;
use App\Rules\Compare;
use App\Traits\Queryable;
use Illuminate\Validation\Rule;

class ConfirmTokenRequest extends JsonRequest
{
    private $confirmToken;

    use Queryable;

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
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', new Compare($this->confirmToken->code)],
            'token' => [
                Rule::exists('confirm_tokens')->where(function ($query) {
                    return $this->confirmToken = $query->where('token', $this->token)->first();
                }),
            ],

            Rule::when(['expired_at' => $this->confirmToken->expired_at], ['expired_at' => static function ($value) {
                return now()->greaterThanOrEqualTo($value);
            }]),
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            'code.compare' => 'Неверный код подтверждения',
            'expired_at' => 'Время жизни кода истекло'
        ];
    }
}
