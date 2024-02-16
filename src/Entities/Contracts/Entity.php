<?php

namespace Playground\Entities\Contracts;

use Playground\Components\Contracts\Component;
use Playground\Components\Contracts\ComponentCollection;

/**
 * @package Entities
 */
interface Entity
{
    /**
     * Get the entity's name
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the entity's identity
     *
     * @return string|null
     */
    public function identity(): ?string;

    /**
     * Get the entities components
     *
     * @return \Playground\Components\Contracts\ComponentCollection
     */
    public function components(): ComponentCollection;

    /**
     * Act as a component
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
     * @throws \Playground\Components\Exceptions\ComponentNotFoundException If the component was not found
     */
    public function as(string $component): Component;
}
