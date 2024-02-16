<?php
declare(strict_types=1);

namespace Playground\Events\Concerns;

/**
 * @package Events
 *
 * @requires \Playground\Events\Contracts\CancellableEvent
 *
 * @mixin \Playground\Events\Contracts\CancellableEvent
 */
trait IsCancellable
{
    /**
     * @var bool
     */
    private bool $cancelled = false;

    public function setCancelled(): void
    {
        $this->cancelled = true;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}
