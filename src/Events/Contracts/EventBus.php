<?php

namespace Playground\Events\Contracts;

use Closure;

/**
 * Event Bus Contract
 *
 * This contract represents a bus that handles the dispatching of events to
 * their listeners.
 *
 * @package Events
 */
interface EventBus
{
    /**
     * Dispatches an event to its listeners
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
     */
    public function dispatch(object $event): object;

    /**
     * Listen to an event
     *
     * @template EClass of object
     *
     * @param object                                 $event
     * @param Closure(object $event): object         $listener
     *
     * @return static
     *
     * @psalm-param EClass                           $event
     * @phpstan-param EClass                         $event
     * @psalm-param Closure(EClass $event): EClass   $event
     * @phpstan-param Closure(EClass $event): EClass $event
     */
    public function listen(object $event, Closure $listener): static;
}
