<?php
declare(strict_types=1);

namespace Playground\Components\Exceptions;

use Playground\Entities\Contracts\Entity;
use Throwable;

final class ComponentIsFixedException extends ComponentException
{
    public static function make(string $component, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The component \'%s\' is fixed and cannot be detached', $component)),
            code    : $code,
            previous: $previous
        );
    }
}
