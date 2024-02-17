<?php

namespace Playground\Resources\Contracts;

use Playground\Utility\Rarity;

/**
 * Resource Contract
 *
 * @package Resources
 */
interface Resource
{
    /**
     * Get the name of the resource
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the rarity of the resource
     *
     * @return \Playground\Utility\Rarity
     */
    public function rarity(): Rarity;
}
