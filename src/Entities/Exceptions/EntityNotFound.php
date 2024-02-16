<?php
declare(strict_types=1);

namespace Playground\Entities\Exceptions;

use Exception;

final class EntityNotFound extends Exception
{
    public static function make(): self
    {
        return new self('No entity is set');
    }
}
