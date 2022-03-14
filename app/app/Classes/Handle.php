<?php

namespace App\Classes;

use App\Models\User;

abstract class Handle implements HandleChannelInterface
{
    public $nextHandle;

    public function handle(User $user): string
    {
        return $this->nextHandle ? $this->nextHandle->handle($user) : 'undefined';
    }

    public function setHandle(HandleChannelInterface $handleChannel): HandleChannelInterface
    {
        return $this->nextHandle = $handleChannel;
    }
}
