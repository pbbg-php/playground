<?php
declare(strict_types=1);

namespace Playground\Entities\Exceptions;

use RuntimeException;

/**
 * @package Entities
 */
final class EntityAlreadySetException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Unable to set the entity, entity is already set');
    }
}
