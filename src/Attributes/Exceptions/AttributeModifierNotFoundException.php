<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Components\Exceptions\ComponentException;
use Playground\Context\Contracts\Context;
use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * Attribute Modifier Not Found Exception
 *
 * Exception thrown when a specific attribute was unable to be found.
 *
 * @package Attributes
 */
final class AttributeModifierNotFoundException extends ComponentException
{
    public static function make(string $modifier, string $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' does not currently have the \'%s\' modifier', $attribute, $modifier)),
            code    : $code,
            previous: $previous
        );
    }
}
