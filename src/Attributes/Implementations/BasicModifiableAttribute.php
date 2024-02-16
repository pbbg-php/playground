<?php
declare(strict_types=1);

namespace Playground\Attributes\Implementations;

use Override;
use Playground\Attributes\Contracts\ModifiableAttribute;

final class BasicModifiableAttribute implements ModifiableAttribute
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    #[Override]
    public function name(): string
    {
        return $this->name;
    }
}
