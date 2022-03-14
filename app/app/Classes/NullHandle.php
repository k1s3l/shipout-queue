<?php

namespace App\Classes;

use App\Models\User;

class NullHandle extends Handle
{
    public function handle(User $user): string
    {
        return $user->phone ? 'sms' : $this->nextHandle->handle($user);
    }
}
