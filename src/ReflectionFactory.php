<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

abstract class ReflectionFactory
{
    /**
     * @param callable|string|array $function
     *
     * @throws ReflectionException
     *
     * @return ReflectionFunction|ReflectionMethod|ReflectionFunctionAbstract|object
     */
    public static function create($function): ReflectionFunctionAbstract
    {
        if (is_string($function)) {
            return strpos($function, '::') ? new ReflectionMethod($function) : new ReflectionFunction($function);
        }

        if (is_array($function)) {
            return (new ReflectionClass(ReflectionMethod::class))->newInstanceArgs($function);
        }

        if (method_exists($function, '__invoke')) {
            return new ReflectionMethod($function, '__invoke');
        }

        return new ReflectionFunction($function);
    }
}