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
     * Deregister an attribute or attribute modifier provider
     *
     * @param \Playground\Attributes\Contracts\AttributeProvider|\Playground\Attributes\Contracts\AttributeModifierProvider $provider
     *
     * @return static
     */
    public function deregister(AttributeProvider|AttributeModifierProvider $provider): static;

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
     * Check if the collection contains an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return bool
     */
    public function has(Attribute $attribute): bool;

    /**
     * Link an attribute with other attributes
     *
     * @param \Playground\Attributes\Contracts\Attribute        $attribute
     * @param array<\Playground\Attributes\Contracts\Attribute> $attributes
     *
     * @return static
     */
    public function link(Attribute $attribute, array $attributes): static;

    /**
     * Check if an attribute has a modifier
     *
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return bool
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     */
    public function modified(Attribute $attribute, AttributeModifier $modifier): bool;

    /**
     * Add a modifier to an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierAlreadyExistsException If the modifier is already present for the attribute
     */
    public function modify(Attribute $attribute, AttributeModifier $modifier): float;

    /**
     * Register an attribute or attribute modifier provider
     *
     * @param \Playground\Attributes\Contracts\AttributeProvider|\Playground\Attributes\Contracts\AttributeModifierProvider $provider
     *
     * @return static
     */
    public function register(AttributeProvider|AttributeModifierProvider $provider): static;

    /**
     * Remove an attribute from the collection
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function remove(Attribute $attribute): Attribute;

    /**
     * Remove all modifiers from an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     */
    public function reset(Attribute $attribute): float;

    /**
     * Set the base value of an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     * @param float                                      $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function set(Attribute $attribute, float $value): static;

    /**
     * Remove a modifier from an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     */
    public function unmodify(Attribute $attribute, AttributeModifier $modifier): float;

    /**
     * Get the value of an attribute
     *
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     */
    public function value(Attribute $attribute): float;

    /**
     * Get all attribute values
     *
     * @return array<string, float>
     */
    public function values(): array;
}
