<?php

namespace Playground\Attributes\Contracts;

/**
 * Attribute Aware Contract
 *
 * Some attributes need to be aware of other attributes, and those that do will
 * implement this interface.
 *
 * @package Attributes
 */
interface AttributeAware
{
    /**
     * Attributes that this attribute is aware of
     *
     * @return array<string>
     */
    public function awareOf(): array;
}
