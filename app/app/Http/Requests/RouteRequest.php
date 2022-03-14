<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Класс для валидации параметров роута
 */
class RouteRequest extends FormRequest
{
    public function validationData()
    {
        return $this->route()->parameters() + parent::validationData();
    }
}
