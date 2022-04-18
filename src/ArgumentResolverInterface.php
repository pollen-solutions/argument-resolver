<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use ReflectionException;

interface ArgumentResolverInterface
{
    /**
     * @param callable|string|array $function
     *
     * @return array
     * @throws ReflectionException
     */
    public function resolve($function): array;

    /**
     * @param ResolverInterface $resolver
     *
     * @return self
     */
    public function addResolver(ResolverInterface $resolver): ArgumentResolverInterface;

    /**
     * @return ResolverInterface[]|null
     */
    public function getResolvers(): ?array;
}
