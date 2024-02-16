<?php

namespace Playground\Attributes\Contracts;

/**
 * Attribute Contract
 *
 * This contract represents an attribute.
 * Attributes are float values that are utilised through the rest of the
 * playground to facilitate various purposes.
 * Examples of attributes may be max health, time bonus, etc.
 *
 * @package Attributes
 */
interface Attribute
{
    /**
     * Get the name of the attribute
     *
     * @return string
     */
    public function name(): string;
}
