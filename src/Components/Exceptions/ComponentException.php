<?php
declare(strict_types=1);

namespace Playground\Components\Exceptions;

use Playground\Entities\Concerns\ExceptionIsAwareOfEntity;
use RuntimeException;

abstract class ComponentException extends RuntimeException
{
    use ExceptionIsAwareOfEntity;
}
