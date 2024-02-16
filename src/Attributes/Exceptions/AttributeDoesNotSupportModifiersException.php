<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Entities\Contracts\Entity;
use Throwable;

/**
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
