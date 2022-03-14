<?php

namespace App\Classes;

use App\Models\User;

interface HandleChannelInterface
{
    public function handle(User $user): string;

    public function setHandle(HandleChannelInterface $handleChannel): HandleChannelInterface;
}
