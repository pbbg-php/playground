<?php

namespace Playground\Utility\Contracts;

/**
 * Registry Contract
 *
 * Registries are responsible for registering the various different elements
 * within playground.
 *
 * @template RClass of object
 */
interface Registry
{
    /**
     * Get the class the registry is for
     *
     * @return class-string<RClass>
     */
    public function for(): string;

    /**
     * Register an instance with the registry
     *
     * @param object         $object
     *
     * @return static
     *
     * @psalm-param RClass   $object
     * @phpstan-param RClass $object
     *
     * @throws \Playground\Utility\Exceptions\RegistryInstanceAlreadyRegisteredException If the object is already registered
     */
    public function register(object $object): static;

    /**
     * Check if there's a registered object for the given name
     *
     * @param string $name
     *
     * @return bool
     */
    public function registered(string $name): bool;

    /**
     * Get a registered instance by its name
     *
     * @param string $name
     *
     * @return object
     *
     * @throws \Playground\Utility\Exceptions\RegistryInstanceNotFoundException If nothing is registered for that name
     */
    public function get(string $name): object;
}
