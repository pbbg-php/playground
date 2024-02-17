<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Attributes\Contracts\Attribute;
use Playground\Attributes\Contracts\AttributeModifier;
use Playground\Components\Exceptions\ComponentException;

use Playground\Entities\Contracts\Entity;
use Throwable;

/**
 * @package Attributes
 */
final class AttributeModifierNotFoundException extends ComponentException
{
    public static function make(AttributeModifier $modifier, Attribute $attribute, Entity $entity, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(
            message : self::getContextualMessage($entity, sprintf('The attribute \'%s\' does not currently have the \'%s\' modifier', $attribute->name(), $modifier->name())),
            code    : $code,
            previous: $previous
        );
    }
}
