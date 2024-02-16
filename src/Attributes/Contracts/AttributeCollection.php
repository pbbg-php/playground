<?php

namespace Playground\Attributes\Contracts;

use IteratorAggregate;

/**
 * Attribute Collection Contract
 *
 * This contract defines the requirements of a collection responsible for
 * holding instances of {@see \Playground\Attributes\Contracts\Attribute}.
 *
 * @package Attributes
 *
 * @extends IteratorAggregate<string, float>
 */
interface AttributeCollection extends IteratorAggregate
{
    /**
     * Get all attributes
     *
     * @return list<\Playground\Attributes\Contracts\Attribute>|\Playground\Attributes\Contracts\Attribute[]
     */
    public function attributes(): array;

    /**
     * Add an attribute to the collection
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     * @param float                                      $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeAlreadyExistsException If the attribute is already present
     */
    public function add(Attribute $attribute, float $value): static;

    /**
     * Get an attribute from the collection
     *
     * @param string $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function get(string $attribute): Attribute;

    /**
     * Check if the collection contains an attribute
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function has(string $attribute): bool;

    /**
     * Link an attribute with other attributes
     *
     * @param string $attribute
     * @param array<string>  $attributes
     *
     * @return static
     */
    public function link(string $attribute, array $attributes): static;

    /**
     * Check if an attribute has a modifier
     *
     * @param string $attribute
     * @param string $modifier
     *
     * @return bool
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     */
    public function modified(string $attribute, string $modifier): bool;

    /**
     * Add a modifier to an attribute
     *
     * @param string                                             $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierAlreadyExistsException If the modifier is already present for the attribute
     */
    public function modify(string $attribute, AttributeModifier $modifier): float;

    /**
     * Remove an attribute from the collection
     *
     * @param string $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function remove(string $attribute): Attribute;

    /**
     * Remove all modifiers from an attribute
     *
     * @param string $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     */
    public function reset(string $attribute): float;

    /**
     * Set the base value of an attribute
     *
     * @param string $attribute
     * @param float  $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function set(string $attribute, float $value): static;

    /**
     * Remove a modifier from an attribute
     *
     * @param string $attribute
     * @param string $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     */
    public function unmodify(string $attribute, string $modifier): float;

    /**
     * Get the value of an attribute
     *
     * @param string $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function value(string $attribute): float;

    /**
     * Get all attribute values
     *
     * @return array<string, float>
     */
    public function values(): array;
}
