<?php
declare(strict_types=1);

namespace Playground\Utility;

/**
 * Rarity Types
 *
 * An enum class to provide the engines' rarity types.
 *
 * @package Utility
 */
enum Rarity: int
{
    case Worthless = 0;

    case Common = 1;

    case Uncommon = 2;

    case Rare = 3;

    case Epic = 4;

    case Legendary = 5;

    case Mythical = 6;

    case Arcane = 7;

    case Divine = 8;
}
