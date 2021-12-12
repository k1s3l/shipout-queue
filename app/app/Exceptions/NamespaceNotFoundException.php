<?php

namespace App\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NamespaceNotFoundException extends Exception implements NotFoundExceptionInterface {}
