<?php
declare(strict_types=1);

namespace Playground\Components\Exceptions;

use Playground\Entities\Concerns\ExceptionIsAwareOfEntity;
use Playground\Entities\Contracts\Entity;
use RuntimeException;
use Throwable;

/**
 * Component Already Exists Exception
 *
 * Exception thrown when a component is already present.
 *
 * @package Components
 */
final class ComponentAlreadyExistsException extends RuntimeException
{
    use ExceptionIsAwareOfEntity;

    public static function make(string $component, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The component \'%s\' is already present', $component)),
            code    : $code,
            previous: $previous
        );
    }
}
