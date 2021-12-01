<?php

namespace App\Http\Requests;

use App\Models\ConfirmToken;
use App\Rules\Compare;
use App\Traits\Queryable;
use Illuminate\Validation\Rule;

class ConfirmTokenRequest extends JsonRequest
{
    use Queryable;

    private $confirmToken;

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
            'code' => ['required', Rule::exists('confirm_tokens')->where(function ($query) {
                return $query->where('code', $this->code);
            })],
            'token' => [
                Rule::exists('confirm_tokens')->where(function ($query) {
                    return $this->confirmToken = $query->where('token', $this->token)->first();
                }),
            ],

//            Rule::when(['expired_at' => $this->confirmToken->expired_at], ['expired_at' => static function ($value) {
//                return now()->greaterThanOrEqualTo($value);
//            }]),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            dd($this->confirmToken);
        });
    }
}
