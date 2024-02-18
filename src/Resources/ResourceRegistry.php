<?php
declare(strict_types=1);

namespace Playground\Resources;

use Override;
use Playground\Core\BaseRegistry;
use Playground\Resources\Contracts\Resource;

/**
 * Resource Registry
 *
 * @extends \Playground\Core\BaseRegistry<\Playground\Resources\Contracts\Resource>
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
