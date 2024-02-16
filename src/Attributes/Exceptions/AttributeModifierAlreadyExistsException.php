<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeModifierAlreadyExistsException extends AttributeModifierException
{
    public static function make(string $modifier, string $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' already has the \'%s\' modifier', $attribute, $modifier)),
            code    : $code,
            previous: $previous
        );
    }
}
