<?php
declare(strict_types=1);

namespace Playground\Utility\Exceptions;

final class RegistryInstanceAlreadyRegisteredException extends RegistryException
{
    public static function make(string $name, string $class): self
    {
        return new self(sprintf('The \'%s\' registry already has something registered under \'%s\'', $class, $name));
    }
}
