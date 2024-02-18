<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Attributes;

use Override;
use Playground\Attributes\Contracts\AttributeModifier;
use Playground\Core\BaseRegistry;

/**
 * Attribute Modifier Registry
 *
 * @extends \Playground\Core\BaseRegistry<\Playground\Attributes\Contracts\AttributeModifier>
 */
final class AttributeModifierRegistry extends BaseRegistry
{
    /**
     * @param object                                                     $object
     *
     * @return string
     *
     * @psalm-param \Playground\Attributes\Contracts\AttributeModifier   $object
     * @phpstan-param \Playground\Attributes\Contracts\AttributeModifier $object
     */
    #[Override]
    protected function getName(object $object): string
    {
        return $object->name();
    }

    #[Override]
    public function for(): string
    {
        return AttributeModifier::class;
    }
}
