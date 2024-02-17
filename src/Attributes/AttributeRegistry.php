<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Attributes;

use Playground\Attributes\Contracts\Attribute;
use Playground\Attributes\Contracts\AttributeModifier;
use Playground\Attributes\Exceptions\AttributeAlreadyRegisteredException;
use Playground\Attributes\Exceptions\AttributeModifierAlreadyRegisteredException;
use Playground\Attributes\Exceptions\AttributeModifierNotRegisteredException;
use Playground\Attributes\Exceptions\AttributeNotRegisteredException;

/**
 * Attribute Manager
 */
final class AttributeRegistry
{
    /**
     * @var array<string, \Playground\Attributes\Contracts\Attribute>
     */
    private array $attributes = [];

    /**
     * @var array<string, \Playground\Attributes\Contracts\AttributeModifier>
     */
    private array $attributeModifiers = [];

    public function registeredAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function registerAttribute(Attribute $attribute): static
    {
        if ($this->registeredAttribute($attribute->name())) {
            throw AttributeAlreadyRegisteredException::make($attribute);
        }

        $this->attributes[$attribute->name()] = $attribute;

        return $this;
    }

    public function getAttribute(string $attribute): Attribute
    {
        if (! $this->registeredAttribute($attribute)) {
            throw AttributeNotRegisteredException::make($attribute);
        }

        return $this->attributes[$attribute];
    }

    public function registeredModifier(string $modifier): bool
    {
        return isset($this->attributeModifiers[$modifier]);
    }

    public function registerModifier(AttributeModifier $modifier): static
    {
        if ($this->registeredModifier($modifier->name())) {
            throw AttributeModifierAlreadyRegisteredException::make($modifier);
        }

        $this->attributeModifiers[$modifier->name()] = $modifier;

        return $this;
    }

    public function getModifier(string $modifier): AttributeModifier
    {
        if (! $this->registeredModifier($modifier)) {
            throw AttributeModifierNotRegisteredException::make($modifier);
        }

        return $this->attributeModifiers[$modifier];
    }
}
