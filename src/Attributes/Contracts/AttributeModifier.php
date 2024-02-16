<?php

namespace Playground\Attributes\Contracts;

/**
 * Attribute Modifier Contract
 *
 * This contract represents a modifier that can be applied to an attribute.
 *
 * @package Attributes
 */
interface AttributeModifier
{
    /**
     * Get the name of the attribute modifier
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the value of the attribute modifier
     *
     * @return float
     */
    public function value(): float;

    /**
     * Get the mode of the attribute modifier
     *
     * @return int
     */
    public function mode(): int;

    /**
     * Check whether the modifier modifies the base
     *
     * @return bool
     */
    public function modifiesBase(): bool;
}
