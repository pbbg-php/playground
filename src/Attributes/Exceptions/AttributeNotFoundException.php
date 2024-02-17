<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Attributes\Contracts\Attribute;
use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeNotFoundException extends AttributeException
{
    public static function make(Attribute $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' was not found', $attribute->name())),
            code    : $code,
            previous: $previous
        );
    }
}
