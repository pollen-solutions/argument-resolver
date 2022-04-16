<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver\Resolvers;

use Pollen\Container\ContainerInterface;

class ContainerResolver
{
    /**
     * @param ContainerInterface $container
     */
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
