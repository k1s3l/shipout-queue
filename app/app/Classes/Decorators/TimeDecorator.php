<?php

namespace App\Classes\Decorators;

class TimeDecorator extends AbstractDecorator
{
    private $time;

    public function __construct(OptionDecoratorInterface $next, int $time = null)
    {
        $this->time = $time ?? now()->getTimestamp();

        parent::__construct($next);
    }

    public function setOption($option = []): array
    {
        $option['time'] = $this->time;

        return parent::setOption($option);
    }
}
