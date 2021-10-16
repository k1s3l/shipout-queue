<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class JsonRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'type' => 'https://example.net/validation-error',
                'title' => "Your request parameters didn't validate",
                'invalid-params' => $errors,
            ], Response::HTTP_BAD_REQUEST),
        );
    }
}
