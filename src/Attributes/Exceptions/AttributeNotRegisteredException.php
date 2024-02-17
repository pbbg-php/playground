<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Attributes\Contracts\Attribute;
use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeNotRegisteredException extends AttributeException
{
    public static function make(string $attribute, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : sprintf('The attribute \'%s\' was not registered', $attribute),
            code    : $code,
            previous: $previous
        );
    }
}
