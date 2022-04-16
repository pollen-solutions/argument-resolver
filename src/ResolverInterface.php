<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use ReflectionParameter;

interface ResolverInterface
{
    /**
     * @param ReflectionParameter $parameter
     *
     * @return array|null
     */
    public function resolve(ReflectionParameter $parameter): ?array;
}