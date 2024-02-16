<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Context\Contracts\Context;
use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * Attribute Does Not Support Modifiers Exception
 *
 * Exception thrown when an attempt to add a {@see \Playground\Attributes\Contracts\AttributeModifier} to
 * an {@see \Playground\Attributes\Contracts\Attribute} that does not implement
 * {@see \Playground\Attributes\Contracts\ModifiableAttribute} is made.
 *
 * @package Attributes
 */
final class AttributeDoesNotSupportModifiersException extends AttributeException
{
    public static function make(string $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' does not support modifiers', $attribute)),
            code    : $code,
            previous: $previous
        );
    }
}
