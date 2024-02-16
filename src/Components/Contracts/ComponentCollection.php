<?php

namespace Playground\Components\Contracts;

use IteratorAggregate;

/**
 * Component Collection Contract
 *
 * This contract defines the requirements of a collection responsible for
 * holding instances of {@see \Playground\Components\Contracts\Component}.
 *
 * @package Components
 *
 * @extends \IteratorAggregate<int<0, max>, \Playground\Components\Contracts\Component>
 */
interface ComponentCollection extends IteratorAggregate
{
    /**
     * Get all components from the collection
     *
     * @return list<\Playground\Components\Contracts\Component>|\Playground\Components\Contracts\Component[]
     */
    public function all(): array;

    /**
     * Attach a component to the collection
     *
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
     */
    public function attach(Component $component): Component;

    /**
     * Detach a component from the collection
     *
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
     */
    public function detach(string $component): Component;

    /**
     * Get a filtered list of all components from the collection
     *
     * @param callable(\Playground\Components\Contracts\Component): bool $criteria
     *
     * @return list<\Playground\Components\Contracts\Component>|\Playground\Components\Contracts\Component[]
     */
    public function filter(callable $criteria): array;

    /**
     * Get a component from the collection
     *
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
     */
    public function get(string $component): Component;

    /**
     * Check if the collection contains a component
     *
     * @template CClass of \Playground\Components\Contracts\Component
     *
     * @param class-string<\Playground\Components\Contracts\Component> $component
     *
     * @return bool
     *
     * @psalm-param class-string<CClass>                               $component
     * @phpstan-param class-string<CClass>                             $component
     */
    public function has(string $component): bool;
}
