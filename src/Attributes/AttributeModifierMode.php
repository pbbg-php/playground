<?php
declare(strict_types=1);

namespace Playground\Attributes;

enum AttributeModifierMode: int
{
    case Additive = 1;

    case Multiplicative = 2;

    public function is(int $mode): bool
    {
        return $this->value === $mode;
    }
}
