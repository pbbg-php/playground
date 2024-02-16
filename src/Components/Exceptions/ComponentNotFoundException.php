<?php
declare(strict_types=1);

namespace Playground\Components\Exceptions;

use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * Component Not Found Exception
 *
 * Exception thrown when a specific component was unable to be found.
 *
 * @package Components
 */
final class ComponentNotFoundException extends ComponentException
{
    public static function make(string $component, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The component \'%s\' was not found', $component)),
            code    : $code,
            previous: $previous
        );
    }
}
