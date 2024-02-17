<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Playground\Events;

use Closure;
use Override;
use Playground\Entities\Concerns\RequiresAnEntity;
use Playground\Entities\Contracts\EntityAware;
use Playground\Events\Contracts\CancellableEvent;
use Playground\Events\Contracts\EventBus;

/**
 * @package Events
 *
 * @implements \Playground\Entities\Contracts\EntityAware<\Playground\Entities\Contracts\Entity>
 */
final class Events implements EventBus, EntityAware
{
    /** @use \Playground\Entities\Concerns\RequiresAnEntity<\Playground\Entities\Contracts\Entity> */
    use RequiresAnEntity;

    /**
     * The event listeners
     *
     * @var array<class-string, list<Closure(object): object>>
     */
    private array $listeners = [];

    /**
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
    #[Override]
    public function dispatch(object $event): object
    {
        $listeners = $this->listeners[$event::class];

        foreach ($listeners as $listener) {
            $listener($event);

            if ($event instanceof CancellableEvent && $event->isCancelled()) {
                break;
            }
        }

        return $event;
    }

    /**
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
    #[Override]
    public function listen(object $event, Closure $listener): static
    {
        $this->listeners[$event::class][] = $listener;

        return $this;
    }
}
