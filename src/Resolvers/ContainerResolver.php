<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver\Resolvers;

use Pollen\ArgumentResolver\AbstractResolver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionParameter;

class ContainerResolver extends AbstractResolver
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inerhitDoc
     */
    public function resolve(ReflectionParameter $parameter): ?array
    {
        $alias = ($type = $parameter->getType()) ? $type->getName() : $parameter->getName();

        if ($this->container->has($alias)) {
            try {
                return [$alias, $this->container->get($alias)];
            } catch (ContainerExceptionInterface|NotFoundExceptionInterface $e) {
                unset($e);
            }
        }

        return null;
    }
}
