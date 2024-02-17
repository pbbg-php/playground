<?php
declare(strict_types=1);

namespace Playground\Utility\Exceptions;

final class RegistryInstanceNotFoundException extends RegistryException
{
    public static function make(string $name, string $class): self
    {
        return new self(sprintf('The \'%s\' registry could not find anything registered under \'%s\'', $class, $name));
    }
}
