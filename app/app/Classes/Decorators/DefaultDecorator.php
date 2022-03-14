<?php

namespace App\Classes\Decorators;

class DefaultDecorator implements OptionDecoratorInterface
{
    public function setOption($option = []): array
    {
        return $option;
    }
}
