<?php
declare(strict_types=1);

namespace Playground\Entities\Concerns;

use Playground\Entities\Contracts\Entity;

/**
 * Exception Is Aware Of Entity
 *
 * Helper trait for use on exceptions that are aware of the entity.
 *
 * @package Entities
 *
 * @requires \Exception
 */
trait ExceptionIsAwareOfEntity
{
    protected static function getContextualMessage(Entity $entity, string $message): string
    {
        $prefix   = $entity->name();
        $identity = $entity->identity();

        if ($identity !== null) {
            $prefix .= '[' . $identity . ']';
        }

        return sprintf('%s: %s', $prefix, $message);
    }
}
