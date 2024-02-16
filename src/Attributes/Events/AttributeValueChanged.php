<?php
declare(strict_types=1);

namespace Playground\Attributes\Events;

use Playground\Attributes\Contracts\Attribute;

final readonly class AttributeValueChanged
{
    public function __construct(
        public Attribute $attribute,
        public float     $oldValue,
        public float     $currentValue,
        public bool      $reset
    )
    {
    }
}
