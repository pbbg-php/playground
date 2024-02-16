<?php
declare(strict_types=1);

namespace Playground\Attributes\Events;

use Playground\Attributes\Contracts\Attribute;

final readonly class AttributeAdded
{
    public function __construct(public Attribute $attribute)
    {
    }
}
