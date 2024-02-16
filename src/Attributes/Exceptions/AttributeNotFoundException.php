<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Context\Contracts\Context;
use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * Attribute Not Found Exception
 *
 * Exception thrown when a specific attribute was unable to be found.
 *
 * @package Attributes
 */
final class AttributeNotFoundException extends AttributeException
{
    public static function make(string $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' was not found', $attribute)),
            code    : $code,
            previous: $previous
        );
    }
}