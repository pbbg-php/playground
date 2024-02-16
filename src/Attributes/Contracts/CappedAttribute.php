<?php

namespace Playground\Attributes\Contracts;

/**
 * Capped Attribute Contract
 *
 * An attribute with a capped min value, max value, or both.
 *
 * @package Attributes
 */
interface CappedAttribute extends Attribute
{
    /**
     * Get the minimum value
     *
     * @return float|null
     */
    public function minValue(): ?float;

    /**
     * Get the maximum value
     *
     * @return float|null
     */
    public function maxValue(): ?float;
}
