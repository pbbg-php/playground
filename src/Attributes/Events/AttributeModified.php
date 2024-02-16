<?php
declare(strict_types=1);

namespace Playground\Attributes\Events;

use Playground\Attributes\Contracts\Attribute;
use Playground\Attributes\Contracts\AttributeModifier;

final readonly class AttributeModified
{
    public function __construct(
        public Attribute         $attribute,
        public AttributeModifier $modifier
    )
    {
    }
}
