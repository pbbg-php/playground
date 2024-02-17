<?php

namespace Playground\Attributes\Contracts;

/**
 * Attribute Modifier Provider Contract
 *
 * Used with classes that "provide attribute modifiers".
 *
 * What this means will depend entirely on how you're using the attributes
 * and various components, but generally it means that if an
 * {@see \Playground\Entities\Contracts\Entity} implementation has this class,
 * it'll have these attribute modifiers.
 *
 * @package Attributes
 */
interface AttributeModifierProvider
{
    /**
     * Get the provided attribute modifiers
     *
     * @return array<string, string>
     */
    public function getProvidedAttributeModifiers(): array;
}
