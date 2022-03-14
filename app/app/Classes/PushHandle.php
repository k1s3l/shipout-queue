<?php

namespace App\Classes;

use App\Models\User;

class PushHandle extends Handle
{
    public function handle(User $user): string
    {
        return substr($user->email, length: 1, offset: 0) == 'k' ? 'push' : parent::handle($user);
    }
}
