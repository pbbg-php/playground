<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Throwable;

/**
 * @package Attributes
 */
final class AttributeAlreadyRegisteredException extends AttributeException
{
    public static function make(string $attribute, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : sprintf('The attribute \'%s\' was already registered', $attribute),
            code    : $code,
            previous: $previous
        );
    }
}
