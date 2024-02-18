<?php
declare(strict_types=1);

namespace Playground\Core\Exceptions;

final class RegistryInstanceInvalidException extends RegistryException
{
    public static function make(string $class): self
    {
        return new self(sprintf('The provided instance is not valid for the \'%s\' registry', $class));
    }
}
