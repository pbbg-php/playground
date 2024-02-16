<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Throwable;

/**
 * @package Attributes
 */
final class AttributeModifierNotRegisteredException extends AttributeException
{
    public static function make(string $modifier, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : sprintf('The attribute modifier \'%s\' was not registered', $modifier),
            code    : $code,
            previous: $previous
        );
    }
}
