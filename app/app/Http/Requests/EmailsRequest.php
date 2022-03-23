<?php

namespace App\Http\Requests;

class EmailsRequest extends JsonRequest
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
     * @return array
     */
    public function rules()
    {
        return collect($this->request->get('emails'))
            ->mapWithKeys(static function ($item, $key) {
                return ["emails.{$key}" => 'email:rfc,dns,spoof'];
            })
            ->toArray() + ['emails' => 'array|required'];
    }

    protected function passedValidation()
    {
        collect($this->request->get('emails'))->transform(static fn ($item) => [
            'email' => $item,
        ]);
    }
}
