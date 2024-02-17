<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Attributes;

use Override;
use Playground\Attributes\Contracts\Attribute;
use Playground\Utility\BaseRegistry;

/**
 * Attribute Registry
 *
 * @extends \Playground\Utility\BaseRegistry<\Playground\Attributes\Contracts\Attribute>
 */
final class AttributeRegistry extends BaseRegistry
{
    /**
     * @param object                                             $object
     *
     * @return string
     *
     * @psalm-param \Playground\Attributes\Contracts\Attribute   $object
     * @phpstan-param \Playground\Attributes\Contracts\Attribute $object
     */
    #[Override]
    protected function getName(object $object): string
    {
        return $object->name();
    }

    #[Override]
    public function for(): string
    {
        return Attribute::class;
    }
}
