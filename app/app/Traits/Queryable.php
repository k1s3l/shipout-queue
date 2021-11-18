<?php

namespace App\Traits;

trait Queryable
{
    public function all($keys = null)
    {
        return parent::all() + $this->route()->parameters();
    }
}
