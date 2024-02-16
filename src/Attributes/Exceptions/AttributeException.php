<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Entities\Concerns\ExceptionIsAwareOfEntity;
use RuntimeException;

abstract class AttributeException extends RuntimeException
{
    use ExceptionIsAwareOfEntity;
}
