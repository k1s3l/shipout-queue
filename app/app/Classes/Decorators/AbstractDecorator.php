<?php

namespace App\Classes\Decorators;

abstract class AbstractDecorator implements OptionDecoratorInterface
{
    public function __construct(OptionDecoratorInterface $next)
    {
        $this->next = $next;
    }

    /**
     * @return OptionDecoratorInterface
     */
    public $next;

    public function setOption($option = []): array
    {
        return $this->next->setOption($option);
    }
}
