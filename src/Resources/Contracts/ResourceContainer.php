<?php

namespace Playground\Resources\Contracts;

/**
 * @template RClass of \Playground\Resources\Contracts\Resource
 */
interface ResourceContainer
{
    /**
     * Get the resource within the container
     *
     * @return \Playground\Resources\Contracts\Resource
     *
     * @psalm-return RClass
     * @phpstan-return RClass
     */
    public function resource(): Resource;

    /**
     * Get the capacity of the container
     *
     * @return int|null
     */
    public function capacity(): ?int;

    /**
     * Deposit an amount into the container
     *
     * This method should return the true amount deposited within the container,
     * based on the amount provided, and its capacity.
     *
     * @param int  $amount
     * @param bool $pretend
     *
     * @return int
     */
    public function deposit(int $amount, bool $pretend = false): int;

    /**
     * Withdrawn an amount from the container
     *
     * This method should return the true amount withdrawn from the container,
     * based on the amount provided, and its capacity.
     *
     * @param int  $amount
     * @param bool $pretend
     *
     * @return int
     */
    public function withdraw(int $amount, bool $pretend = false): int;

    /**
     * Get the remaining capacity of the container
     *
     * @return int
     */
    public function remaining(): int;

    /**
     * Get the amount stored in the container
     *
     * @return int
     */
    public function utilised(): int;

    /**
     * Check if the container is full
     *
     * @return bool
     */
    public function isFull(): bool;

    /**
     * Check if the container is empty
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
