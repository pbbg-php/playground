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
use Playground\Attributes\Contracts\AttributeModifierProvider;
use Playground\Attributes\Contracts\AttributeProvider;
use Playground\Attributes\Contracts\CappedAttribute;
use Playground\Attributes\Contracts\ModifiableAttribute;
use Playground\Attributes\Events\AttributeAdded;
use Playground\Attributes\Events\AttributeModified;
use Playground\Attributes\Events\AttributeRecalculated;
use Playground\Attributes\Events\AttributeUnmodified;
use Playground\Attributes\Events\AttributeValueChanged;
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
     * @var array<string, array<\Playground\Attributes\Contracts\Attribute>>
     */
    private array $linked = [];

    /**
     * The stack of recalculating
     *
     * @var array<string>
     */
    private array $recalculating = [];

    /**
     * Attribute providers
     *
     * @var list<\Playground\Attributes\Contracts\AttributeProvider>|\Playground\Attributes\Contracts\AttributeProvider[]
     */
    private array $attributeProviders = [];

    /**
     * Attribute modifier providers
     *
     * @var list<\Playground\Attributes\Contracts\AttributeModifierProvider>|\Playground\Attributes\Contracts\AttributeModifierProvider[]
     */
    private array $modifierProviders = [];

    /**
     * Fire an event on the parent entity
     *
     * @template EClass of object
     *
     * @param object         $event
     *
     * @return object
     *
     * @psalm-param EClass   $event
     * @phpstan-param EClass $event
     *
     * @psalm-return EClass
     * @phpstan-return EClass
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     */
    private function fireEvent(object $event): object
    {
        return $this->getEntity()->events()->dispatch($event);
    }

    /**
     * @return list<\Playground\Attributes\Contracts\Attribute>|\Playground\Attributes\Contracts\Attribute[]
     */
    #[Override]
    public function attributes(): array
    {
        return array_values($this->attributes);
    }

    /**
     * @param \Playground\Attributes\Contracts\AttributeProvider|\Playground\Attributes\Contracts\AttributeModifierProvider $provider
     *
     * @return static
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     */
    #[Override]
    public function deregister(AttributeProvider|AttributeModifierProvider $provider): static
    {
        // Handle attribute modifiers first, in case the provider modifies
        // attributes it also provides
        if ($provider instanceof AttributeModifierProvider) {
            $index = array_search($provider, $this->modifierProviders, true);

            if ($index !== false) {
                unset($this->modifierProviders[$index]);
                $modifiers = $provider->getProvidedAttributeModifiers();

                foreach ($modifiers as $entry) {
                    // Some attribute modifier providers will be providing
                    // negative modifiers, and it's entirely possible that the
                    // negative modifier was cleansed in some manner, so we'll
                    // silence exceptions for modifiers that don't currently exist.
                    try {
                        [$attribute, $modifier] = $entry;

                        $this->unmodify($attribute, $modifier);
                    } catch (AttributeModifierNotFoundException) {
                    }
                }
            }
        }

        if ($provider instanceof AttributeProvider) {
            $index = array_search($provider, $this->attributeProviders, true);

            if ($index !== false) {
                unset($this->attributeProviders[$index]);
                $attributes = $provider->getProvidedAttributes();

                foreach ($attributes as $entry) {
                    [$attribute,] = $entry;
                    $this->remove($attribute);
                }
            }
        }

        return $this;
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
        if ($this->has($attribute)) {
            throw AttributeAlreadyExistsException::make($attribute, $this->getEntity());
        }

        // These have to happen here because PhpStan gets really confused at the
        // concept of a class implementing more than one interface.
        $this->attributes[$attribute->name()] = $attribute;
        $this->values[$attribute->name()]     = $this->baseValues[$attribute->name()] = $value;

        // If the attribute should be aware of the context its in, we'll provide
        // it with the context available to this collection.
        if ($attribute instanceof EntityAware) {
            $attribute->setEntity($this->getEntity());
        }

        // Some attributes are aware of others
        if ($attribute instanceof AttributeAware) {
            /**
             * @psalm-suppress UnnecessaryVarAnnotation
             * @var \Playground\Attributes\Contracts\Attribute&\Playground\Attributes\Contracts\AttributeAware $attribute
             */
            $this->link($attribute, $attribute->awareOf());
        }

        if ($attribute instanceof ModifiableAttribute) {
            $this->modifiers[$attribute->name()] = [];
        }

        /**
         * Unfortunately, we need this here, again, because PhpStan is wrong and
         * doing eccentric ludicrous stuff.
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         * @var \Playground\Attributes\Contracts\Attribute $attribute
         * @noinspection   PhpRedundantVariableDocTypeInspection
         */
        $this->fireEvent(new AttributeAdded($attribute));

        return $this;
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return bool
     */
    #[Override]
    public function has(Attribute $attribute): bool
    {
        return isset($this->attributes[$attribute->name()]);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute        $attribute
     * @param array<\Playground\Attributes\Contracts\Attribute> $attributes
     *
     * @return static
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function link(Attribute $attribute, array $attributes): static
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        foreach ($attributes as $linkedAttribute) {
            if (! $this->has($linkedAttribute)) {
                throw AttributeNotFoundException::make($linkedAttribute, $this->getEntity());
            }

            $this->linked[$linkedAttribute->name()][] = $attribute;
        }

        return $this;
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return bool
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function modified(Attribute $attribute, AttributeModifier $modifier): bool
    {
        if (! ($attribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        return isset($this->modifiers[$attribute->name()][$modifier->name()]);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
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
    public function modify(Attribute $attribute, AttributeModifier $modifier): float
    {
        if (! ($attribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        if ($this->modified($attribute, $modifier)) {
            throw AttributeModifierAlreadyExistsException::make($modifier, $attribute, $this->getEntity());
        }

        $this->modifiers[$attribute->name()][$modifier->name()] = $modifier;

        $this->fireEvent(new AttributeModified($attribute, $modifier));

        $this->recalculate($attribute);

        return $this->value($attribute);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return void
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    private function recalculate(Attribute $attribute): void
    {
        $value = $base = $this->baseValues[$attribute->name()];

        if (in_array($attribute->name(), $this->recalculating, true)) {
            throw AttributeRecursion::make($attribute, $this->getEntity());
        }

        $this->recalculating[] = $attribute->name();

        if ($attribute instanceof ModifiableAttribute) {
            $modifiers     = $this->modifiers[$attribute->name()];
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

        if ($attribute instanceof CappedAttribute) {
            $min = $attribute->minValue();
            $max = $attribute->maxValue();

            if ($min !== null) {
                $value = max($min, $value);
            }

            if ($max !== null) {
                $value = min($max, $value);
            }
        }

        $this->fireEvent(new AttributeRecalculated($attribute));

        $this->set($attribute, $value);

        array_pop($this->recalculating);
    }

    /**
     * @param \Playground\Attributes\Contracts\AttributeProvider|\Playground\Attributes\Contracts\AttributeModifierProvider $provider
     *
     * @return static
     *
     * @throws \Playground\Entities\Exceptions\EntityNotFound
     */
    #[Override]
    public function register(AttributeProvider|AttributeModifierProvider $provider): static
    {
        if ($provider instanceof AttributeProvider) {
            $this->attributeProviders[] = $provider;
            $attributes                 = $provider->getProvidedAttributes();

            foreach ($attributes as $entry) {
                [$attribute, $value] = $entry;
                $this->add($attribute, $value);
            }
        }

        if ($provider instanceof AttributeModifierProvider) {
            $this->modifierProviders[] = $provider;
            $modifiers                 = $provider->getProvidedAttributeModifiers();

            foreach ($modifiers as $entry) {
                [$attribute, $modifier] = $entry;
                $this->modify($attribute, $modifier);
            }
        }

        return $this;
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return \Playground\Attributes\Contracts\Attribute
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function remove(Attribute $attribute): Attribute
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        unset(
            $this->attributes[$attribute->name()],
            $this->values[$attribute->name()],
            $this->modifiers[$attribute->name()],
            $this->linked[$attribute->name()]
        );

        return $attribute;
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function reset(Attribute $attribute): float
    {
        if (! ($attribute instanceof ModifiableAttribute)) {
            throw AttributeDoesNotSupportModifiersException::make($attribute, $this->getEntity());
        }

        $this->modifiers[$attribute->name()] = [];

        $this->set($attribute, $this->baseValues[$attribute->name()], true);

        return $this->value($attribute);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     * @param float                                      $value
     *
     * @return static
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function set(Attribute $attribute, float $value, bool $resetting = false): static
    {
        if ($this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        $original                         = $this->values[$attribute->name()];
        $this->values[$attribute->name()] = $this->baseValues[$attribute->name()] = $value;

        // It's possible for an attribute to be set to its previous value, in
        // which case we don't want to update linked attributes, because it
        // won't change anything.
        if ($original !== $value) {
            $this->fireEvent(new AttributeValueChanged($attribute, $original, $value, $resetting));

            $linked = $this->linked[$attribute->name()] ?? [];

            if (! empty($linked)) {
                foreach ($linked as $linkedAttribute) {
                    $this->recalculate($linkedAttribute);
                }
            }
        }

        return $this;
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute         $attribute
     * @param \Playground\Attributes\Contracts\AttributeModifier $modifier
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Attributes\Exceptions\AttributeDoesNotSupportModifiersException If the attribute does not support modifiers
     * @throws \Playground\Attributes\Exceptions\AttributeModifierNotFoundException If the modifier wasn't found for the attribute
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function unmodify(Attribute $attribute, AttributeModifier $modifier): float
    {
        if (! $this->modified($attribute, $modifier)) {
            throw AttributeModifierNotFoundException::make($modifier, $attribute, $this->getEntity());
        }

        unset($this->modifiers[$attribute->name()][$modifier->name()]);

        $this->fireEvent(new AttributeUnmodified($attribute, $modifier));

        $this->recalculate($attribute);

        return $this->value($attribute);
    }

    /**
     * @param \Playground\Attributes\Contracts\Attribute $attribute
     *
     * @return float
     *
     * @throws \Playground\Attributes\Exceptions\AttributeNotFoundException If the attribute was not found
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is found
     */
    #[Override]
    public function value(Attribute $attribute): float
    {
        if (! $this->has($attribute)) {
            throw AttributeNotFoundException::make($attribute, $this->getEntity());
        }

        return $this->values[$attribute->name()];
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
