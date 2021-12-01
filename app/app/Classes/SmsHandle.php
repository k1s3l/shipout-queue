<?php

namespace App\Classes;

use App\Models\User;

class SmsHandle extends Handle
{
    public function handle(User $user): string
    {
        return substr($user->email, length: 1, offset: 0) == 'i' ? 'sms' : parent::handle($user);
    }
}
