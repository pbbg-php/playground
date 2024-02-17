<?php
declare(strict_types=1);

namespace Playground\Resources;

use Override;
use Playground\Resources\Contracts\Resource;
use Playground\Utility\BaseRegistry;

/**
 * Resource Registry
 *
 * @extends \Playground\Utility\BaseRegistry<\Playground\Resources\Contracts\Resource>
 */
final class ResourceRegistry extends BaseRegistry
{
    /**
     * @param object                                           $object
     *
     * @return string
     *
     * @psalm-param \Playground\Resources\Contracts\Resource   $object
     * @phpstan-param \Playground\Resources\Contracts\Resource $object
     */
    #[Override]
    protected function getName(object $object): string
    {
        return $object->name();
    }

    #[Override]
    public function for(): string
    {
        return Resource::class;
    }
}
