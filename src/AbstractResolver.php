<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use ReflectionParameter;

abstract class AbstractResolver implements ResolverInterface
{
    abstract public function resolve(ReflectionParameter $parameter): ?array;
}
