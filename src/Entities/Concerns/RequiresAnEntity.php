<?php
declare(strict_types=1);

namespace Playground\Entities\Concerns;

use Playground\Entities\Contracts\Entity;
use Playground\Entities\Exceptions\EntityAlreadySetException;
use Playground\Entities\Exceptions\EntityNotFound;

/**
 * @template EClass of \Playground\Entities\Contracts\Entity
 *
 * @requires \Playground\Entities\Contracts\EntityAware
 *
 * @package Entities
 */
trait RequiresAnEntity
{
    /**
     * The entity
     *
     * @var \Playground\Entities\Contracts\Entity
     *
     * @psalm-var EClass
     * @phpstan-var EClass
     */
    private Entity $entity;

    /**
     * @template NEClass of \Playground\Entities\Contracts\Entity
     *
     * @param \Playground\Entities\Contracts\Entity $entity
     *
     * @return static
     *
     * @psalm-param NEClass                         $entity
     * @phpstan-param NEClass                       $entity
     *
     * @psalm-this-out static<NEClass>
     */
    public function setEntity(Entity $entity): static
    {
        if (isset($this->entity)) {
            throw EntityAlreadySetException::make();
        }

        $this->entity = $entity;

        return $this;
    }

    /**
     * @return \Playground\Entities\Contracts\Entity
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     *
     * @psalm-return EClass
     * @phpstan-return EClass
     */
    public function getEntity(): Entity
    {
        if (! isset($this->entity)) {
            throw EntityNotFound::make();
        }

        return $this->entity;
    }
}
