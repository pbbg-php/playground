<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Attributes\Contracts\AttributeModifier;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeModifierAlreadyRegisteredException extends AttributeModifierException
{
    public static function make(AttributeModifier $modifier, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : sprintf('The attribute modifier \'%s\' was already registered', $modifier->name()),
            code    : $code,
            previous: $previous
        );
    }
}
