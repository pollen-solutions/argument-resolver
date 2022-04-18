<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use Pollen\ArgumentResolver\Resolvers\ContainerResolver;
use Pollen\Container\ServiceProvider;

class ArgumentResolverServiceProvider extends ServiceProvider
{
    protected $provides = [
        ArgumentResolverInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        ArgumentResolver::setDefaultResolvers([new ContainerResolver($this->getContainer())]);

        $this->getContainer()->share(ArgumentResolverInterface::class, ArgumentResolver::class);
    }
}