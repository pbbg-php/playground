<?php
declare(strict_types=1);

namespace Playground\Attributes\Exceptions;

use Playground\Context\Contracts\Context;
use Playground\Entities\Concerns\ExceptionIsAwareOfEntity;
use Playground\Entities\Contracts\Entity;

final class AttributeRecursion extends AttributeException
{
    use ExceptionIsAwareOfEntity;

    public static function make(string $attribute, Entity $entity): self
    {
        return new self(
            self::getContextualMessage($entity, sprintf('Recursion detected when recalculating the attribute \'%s\'', $attribute))
        );
    }
}
