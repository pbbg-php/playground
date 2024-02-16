<?php

namespace Playground\Entities\Contracts;

/**
 * Entity-Aware Contract
 *
 * Some classes need to be made aware of the entity that they belong to, which
 * is where this interface comes in.
 *
 * @template EClass of \Playground\Entities\Contracts\Entity
 *
 * @package Entities
 */
interface EntityAware
{
    /**
     * Set the current entity
     *
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
    public function setEntity(Entity $entity): static;

    /**
     * Get the current entity
     *
     * @return \Playground\Entities\Contracts\Entity|null
     *
     * @psalm-return EClass|null
     * @phpstan-return EClass|null
     */
    public function getEntity(): ?Entity;
}
