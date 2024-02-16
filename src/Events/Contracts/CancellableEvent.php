<?php

namespace Playground\Events\Contracts;

interface CancellableEvent
{
    public function setCancelled(): void;

    public function isCancelled(): bool;
}
