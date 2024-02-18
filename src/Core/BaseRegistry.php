<?php
declare(strict_types=1);

namespace Playground\Core;

use Override;
use Playground\Core\Contracts\Registry;
use Playground\Core\Exceptions\RegistryInstanceAlreadyRegisteredException;
use Playground\Core\Exceptions\RegistryInstanceInvalidException;
use Playground\Core\Exceptions\RegistryInstanceNotFoundException;

/**
 * @template RClass of object
 *
 * @implements \Playground\Core\Contracts\Registry<RClass>
 */
abstract class BaseRegistry implements Registry
{
    /**
     * The registered instances
     *
     * @var array<string, RClass>
     */
    private array $instances = [];

    /**
     * Get the name from the object
     *
     * @param object         $object
     *
     * @return string
     *
     * @psalm-param RClass   $object
     * @phpstan-param RClass $object
     */
    abstract protected function getName(object $object): string;

    /**
     * @param object         $object
     *
     * @return static
     *
     * @psalm-param RClass   $object
     * @phpstan-param RClass $object
     *
     * @throws \Playground\Core\Exceptions\RegistryInstanceAlreadyRegisteredException
     * @throws \Playground\Core\Exceptions\RegistryInstanceInvalidException
     */
    #[Override]
    public function register(object $object): static
    {
        $name = $this->getName($object);

        if ($this->registered($name)) {
            throw RegistryInstanceAlreadyRegisteredException::make($name, $this->for());
        }

        if (! is_subclass_of($object, $this->for())) {
            throw RegistryInstanceInvalidException::make($this->for());
        }

        $this->instances[$name] = $object;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    #[Override]
    public function registered(string $name): bool
    {
        return isset($this->instances[$name]);
    }

    /**
     * @param string $name
     *
     * @return object
     *
     * @psalm-return RClass
     * @phpstan-return RClass
     *
     * @throws \Playground\Core\Exceptions\RegistryInstanceNotFoundException
     */
    #[Override]
    public function get(string $name): object
    {
        if (! $this->registered($name)) {
            throw RegistryInstanceNotFoundException::make($name, $this->for());
        }

        return $this->instances[$name];
    }
}
