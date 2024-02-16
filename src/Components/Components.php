<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Components;

use ArrayIterator;
use Override;
use Playground\Components\Contracts\Component;
use Playground\Components\Contracts\ComponentCollection;
use Playground\Components\Contracts\FixedComponent;
use Playground\Components\Exceptions\ComponentAlreadyExistsException;
use Playground\Components\Exceptions\ComponentIsFixedException;
use Playground\Components\Exceptions\ComponentNotFoundException;
use Playground\Entities\Concerns\RequiresAnEntity;
use Playground\Entities\Contracts\EntityAware;
use Traversable;

/**
 * Components Collection
 *
 * A collection of components.
 *
 * @package Components
 *
 * @implements \Playground\Entities\Contracts\EntityAware<\Playground\Entities\Contracts\Entity>
 */
final class Components implements ComponentCollection, EntityAware
{
    /**
     * @use \Playground\Entities\Concerns\RequiresAnEntity<\Playground\Entities\Contracts\Entity>
     */
    use RequiresAnEntity;

    /**
     * The components
     *
     * @var array<class-string<\Playground\Components\Contracts\Component>, \Playground\Components\Contracts\Component>
     */
    private array $components = [];

    #[Override]
    public function all(): array
    {
        return array_values($this->components);
    }

    /**
     * @template CClass of \Playground\Components\Contracts\Component
     *
     * @param \Playground\Components\Contracts\Component $component
     *
     * @return \Playground\Components\Contracts\Component
     *
     * @psalm-param CClass                               $component
     * @phpstan-param CClass                             $component
     *
     * @psalm-return CClass
     * @phpstan-return CClass
     *
     * @throws \Playground\Components\Exceptions\ComponentAlreadyExistsException If the component is already present
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function attach(Component $component): Component
    {
        if ($this->has($component::class)) {
            throw ComponentAlreadyExistsException::make($component::class, $this->getEntity());
        }

        $this->components[$component::class] = $component;

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if ($component instanceof EntityAware) {
            $component->setEntity($this->getEntity());
        }

        return $component;
    }

    /**
     * @template CClass of \Playground\Components\Contracts\Component
     *
     * @param class-string<\Playground\Components\Contracts\Component> $component
     *
     * @return \Playground\Components\Contracts\Component
     *
     * @psalm-param class-string<CClass>                               $component
     * @phpstan-param class-string<CClass>                             $component
     *
     * @psalm-return CClass
     * @phpstan-return CClass
     *
     * @throws \Playground\Components\Exceptions\ComponentNotFoundException If no such component is present
     * @throws \Playground\Components\Exceptions\ComponentIsFixedException If the component implements {@see \Playground\Components\Contracts\FixedComponent}
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function detach(string $component): Component
    {
        $realComponent = $this->get($component);

        if ($realComponent instanceof FixedComponent) {
            throw ComponentIsFixedException::make($component, $this->getEntity());
        }

        unset($this->components[$component]);

        return $realComponent;
    }

    #[Override]
    public function filter(callable $criteria): array
    {
        return array_filter($this->all(), $criteria);
    }

    /**
     * @template CClass of \Playground\Components\Contracts\Component
     *
     * @param class-string<\Playground\Components\Contracts\Component> $component
     *
     * @return \Playground\Components\Contracts\Component
     *
     * @psalm-param class-string<CClass>                               $component
     * @phpstan-param class-string<CClass>                             $component
     *
     * @psalm-return CClass
     * @phpstan-return CClass
     *
     * @throws \Playground\Components\Exceptions\ComponentNotFoundException If no such component is present
     * @throws \Playground\Entities\Exceptions\EntityNotFound If no context is set
     */
    #[Override]
    public function get(string $component): Component
    {
        if (! isset($this->components[$component])) {
            throw ComponentNotFoundException::make($component, $this->getEntity());
        }

        /**
         * I hate that this completely pointless variable is here, but psalm cries
         * if it isn't.
         *
         * @var CClass $realComponent
         */
        $realComponent = $this->components[$component];

        return $realComponent;
    }

    #[Override]
    public function has(string $component): bool
    {
        return isset($this->components[$component]);
    }

    /**
     * @return \Traversable<int<0, max>, \Playground\Components\Contracts\Component>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}
