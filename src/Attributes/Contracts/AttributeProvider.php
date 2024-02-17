<?php

namespace Playground\Attributes\Contracts;

/**
 * Attribute Provider Contract
 *
 * Used with classes that "provide attributes".
 *
 * What this means will depend entirely on how you're using the attributes
 * and various components, but generally it means that if an
 * {@see \Playground\Entities\Contracts\Entity} implementation has this class,
 * it'll have these attributes.
 *
 * @package Attributes
 */
interface AttributeProvider
{
    /**
     * Get the provided attributes
     *
     * @return list<array{\Playground\Attributes\Contracts\Attribute, int}>
     */
    public function getProvidedAttributes(): array;
}
