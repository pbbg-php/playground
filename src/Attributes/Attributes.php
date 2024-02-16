<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Attributes;

use ArrayIterator;
use Override;
use Playground\Attributes\Contracts\Attribute;
use Playground\Attributes\Contracts\AttributeAware;
use Playground\Attributes\Contracts\AttributeCollection;
use Playground\Attributes\Contracts\AttributeModifier;
use Playground\Attributes\Contracts\CappedAttribute;
use Playground\Attributes\Contracts\ModifiableAttribute;
use Playground\Attributes\Exceptions\AttributeAlreadyExistsException;
use Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException;
use Playground\Attributes\Exceptions\AttributeModifierAlreadyExistsException;
use Playground\Attributes\Exceptions\AttributeModifierNotFoundException;
use Playground\Attributes\Exceptions\AttributeNotFoundException;
use Playground\Attributes\Exceptions\AttributeRecursion;
use Playground\Components\Contracts\Component;
use Playground\Entities\Concerns\RequiresAnEntity;
use Playground\Entities\Contracts\EntityAware;
use Traversable;

/**
 * @package Attributes
 *
 * @implements \Playground\Entities\Contracts\EntityAware<\Playground\Entities\Contracts\Entity>
 */
final class Attributes implements AttributeCollection, EntityAware, Component
{
    /**
     * @use \Playground\Entities\Concerns\RequiresAnEntity<\Playground\Entities\Contracts\Entity>
     */
    use RequiresAnEntity;

    /**
     * The attributes
     *
     * @var array<string, \Playground\Attributes\Contracts\Attribute>
     */
    private array $attributes = [];

    /**
     * The attribute values
     *
     * @var array<string, float>
     */
    private array $values = [];

    /**
     * The attribute base values
     *
     * @var array<string, float>
     */
    private array $baseValues = [];

    /**
     * The attribute modifiers
     *
     * @var array<string, array<string, \Playground\Attributes\Contracts\AttributeModifier>>
     */
    private array $modifiers = [];

    /**
     * Linked attributes
     *
     * @var array<string, array<string>>
     */
    private array $linked = [];

    /**
     * The stack of recalculating
     *
     * @var array<string>
     */
    private array $recalculating = [];

    /**
     * @return list<\Playground\Attributes\Contracts\Attribute>|\Playground\Attributes\Contracts\Attribute[]
     */
    #[Override]
    public function attributes(): array
    {
        return array_values($this->attributes);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     * @param float                                      $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeAlreadyExistsException If the attribute is already present
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function add(Attribute $attribute, float $value): static
    {
        if ($this->has($attribute::class)) {
            throw AttributeAlreadyExistsException::make($attribute::class, $this->getEntity());
        }

        // This also has to be created, rather than just using this method where
        // necessary because the name() method doesn't exist on ContextAware.
        $name = $attribute->name();

        // This has to happen here because PhpStan will read the following checks
        // and assume it's actually an instance of ContextAware OR Attribute, not both.
        $this->attributes[$name] = $attribute;

        // If the attribute should be aware of the context its in, we'll provide
        // it with the context available to this collection.
        if ($attribute instanceof EntityAware) {
            $attribute->setEntity($this->getEntity());
        }

        // Some attributes are aware of others
        if ($attribute instanceof AttributeAware) {
            $this->link($name, $attribute->awareOf());
        }

        $this->values[$name] = $this->baseValues[$name] = $value;

        if ($attribute instanceof ModifiableAttribute) {
            $this->modifiers[$name] = [];
        }

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function get(string $attribute): Attribute
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        return $this->attributes[$attribute];
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    #[Override]
    public function has(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * @param string        $attribute
     * @param array<string> $attributes
     *
     * @return static
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function link(string $attribute, array $attributes): static
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        foreach ($attributes as $linkedAttribute) {
            if (! $this->has($linkedAttribute)) {
                throw AttributeNotFoundException::make($linkedAttribute, $this->getEntity());
            }

            $this->linked[$linkedAttribute][] = $attribute;
        }

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $modifier
     *
     * @return bool
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function modified(string $attribute, string $modifier): bool
    {
        $realAttribute = $this->get($attribute);

        if (! ($realAttribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        return isset($this->modifiers[$attribute][$modifier]);
    }

    /**
     * @param string                                             $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierAlreadyExistsException If the modifier is already present for the attribute
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function modify(string $attribute, AttributeModifier $modifier): float
    {
        $realAttribute = $this->get($attribute);

        if (! ($realAttribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        if ($this->modified($attribute, $modifier->name())) {
            throw AttributeModifierAlreadyExistsException::make($attribute, $modifier->name(), $this->getEntity());
        }

        $this->modifiers[$attribute][$modifier->name()] = $modifier;

        $this->recalculate($attribute);

        return $this->value($attribute);
    }

    /**
     * @param string $attribute
     *
     * @return void
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     * @throws \Playground\Attributes\Exceptions\AttributeRecursion If an attribute recalculation is recursive
     */
    private function recalculate(string $attribute): void
    {
        $realAttribute = $this->get($attribute);
        $value         = $base = $this->baseValues[$attribute];

        if (in_array($attribute, $this->recalculating, true)) {
            throw AttributeRecursion::make($attribute, $this->getEntity());
        }

        $this->recalculating[] = $attribute;

        if ($realAttribute instanceof ModifiableAttribute) {
            $modifiers     = $this->modifiers[$attribute];
            $baseModifiers = $runningModifiers = [];

            foreach ($modifiers as $modifier) {
                if ($modifier->modifiesBase()) {
                    $baseModifiers[] = $modifier;
                } else {
                    $runningModifiers[] = $modifier;
                }
            }

            foreach ($baseModifiers as $modifier) {
                if (AttributeModifierMode::Multiplicative->is($modifier->mode())) {
                    $value += $base * $modifier->value();
                }
            }

            foreach ($runningModifiers as $modifier) {
                if (AttributeModifierMode::Multiplicative->is($modifier->mode())) {
                    $value += $value * $modifier->value();
                } else if (AttributeModifierMode::Additive->is($modifier->mode())) {
                    $value += $modifier->value();
                }
            }
        }

        if ($realAttribute instanceof CappedAttribute) {
            $min = $realAttribute->minValue();
            $max = $realAttribute->maxValue();

            if ($min !== null) {
                $value = max($min, $value);
            }

            if ($max !== null) {
                $value = min($max, $value);
            }
        }

        $this->set($attribute, $value);

        array_pop($this->recalculating);
    }

    /**
     * @param string $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function remove(string $attribute): Attribute
    {
        $realAttribute = $this->get($attribute);

        unset($this->attributes[$attribute], $this->values[$attribute], $this->modifiers[$attribute]);

        return $realAttribute;
    }

    /**
     * @param string $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function reset(string $attribute): float
    {
        $realAttribute = $this->get($attribute);

        if (! ($realAttribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        $this->modifiers[$attribute] = [];

        return $this->set($attribute, $this->baseValues[$attribute])->value($attribute);
    }

    /**
     * @param string $attribute
     * @param float  $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function set(string $attribute, float $value): static
    {
        if ($this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        $original                 = $this->values[$attribute];
        $this->values[$attribute] = $this->baseValues[$attribute] = $value;

        // It's possible for an attribute to be set to its previous value, in
        // which case we don't want to update linked attributes, because it
        // won't change anything.
        if ($original !== $value) {
            $linked = $this->linked[$attribute] ?? [];

            if (! empty($linked)) {
                foreach ($linked as $linkedAttribute) {
                    $this->recalculate($linkedAttribute);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function unmodify(string $attribute, string $modifier): float
    {
        if (! $this->modified($attribute, $modifier)) {
            throw AttributeModifierNotFoundException::make($attribute, $modifier, $this->getEntity());
        }

        unset($this->modifiers[$attribute][$modifier]);

        $this->recalculate($attribute);

        return $this->value($attribute);
    }

    /**
     * @param string $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function value(string $attribute): float
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        return $this->values[$attribute];
    }

    /**
     * @return array<string, float>
     */
    #[Override]
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @return \Traversable<string, float>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }
}
