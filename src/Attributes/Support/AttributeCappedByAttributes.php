<?php
declare(strict_types=1);

namespace Playground\Attributes\Support;

use Override;
use Playground\Attributes\Attributes;
use Playground\Attributes\Contracts\AttributeAware;
use Playground\Attributes\Contracts\CappedAttribute;
use Playground\Entities\Concerns\RequiresAnEntity;
use Playground\Entities\Contracts\EntityAware;

/**
 * @implements \Playground\Entities\Contracts\EntityAware<\Playground\Entities\Contracts\Entity>
 */
abstract class AttributeCappedByAttributes implements CappedAttribute, AttributeAware, EntityAware
{
    /** @use \Playground\Entities\Concerns\RequiresAnEntity<\Playground\Entities\Contracts\Entity> */
    use RequiresAnEntity;

    /**
     * @return string
     */
    abstract protected function minAttribute(): string;

    /**
     * @return float|null
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     */
    #[Override]
    public function minValue(): ?float
    {
        return $this->getEntity()
                    ->as(Attributes::class)
                    ->value($this->minAttribute());
    }

    /**
     * @return string
     */
    abstract protected function maxAttribute(): string;

    /**
     * @return float|null
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     */
    #[Override]
    public function maxValue(): ?float
    {
        return $this->getEntity()
                    ->as(Attributes::class)
                    ->value($this->maxAttribute());
    }
}
