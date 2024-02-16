<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeAlreadyExistsException extends AttributeException
{
    public static function make(string $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' is already present', $attribute)),
            code    : $code,
            previous: $previous
        );
    }
}
