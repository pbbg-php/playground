<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Attributes\Contracts\Attribute;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeAlreadyRegisteredException extends AttributeException
{
    public static function make(Attribute $attribute, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : sprintf('The attribute \'%s\' was already registered', $attribute->name()),
            code    : $code,
            previous: $previous
        );
    }
}
