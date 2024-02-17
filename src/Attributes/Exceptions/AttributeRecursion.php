<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;


use Playground\Attributes\Contracts\Attribute;
use Playground\Entities\Concerns\ExceptionIsAwareOfEntity;
use Playground\Entities\Contracts\Entity;

final class AttributeRecursion extends AttributeException
{
    use ExceptionIsAwareOfEntity;

    public static function make(Attribute $attribute, Entity $entity): self
    {
        return new self(
            self::getContextualMessage($entity, sprintf('Recursion detected when recalculating the attribute \'%s\'', $attribute->name()))
        );
    }
}
