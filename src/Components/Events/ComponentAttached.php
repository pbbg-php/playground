<?php
declare(strict_types=1);

namespace Playground\Components\Events;

use Playground\Components\Contracts\Component;

final readonly class ComponentAttached
{
    public function __construct(public Component $component)
    {
    }
}
