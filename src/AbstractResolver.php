<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

abstract class AbstractResolver implements ResolverInterface
{
    abstract public function resolve(ReflectionParameter $parameter): ?array;

    /**
     * @param ReflectionParameter $parameter
     * @param mixed $value
     *
     * @return bool
     */
    protected function matchType(ReflectionParameter $parameter, $value) : bool
    {
        if (!$type = $parameter->getType()) {
            return true;
        }

        $typeName = $type->getName();

        if ('array' === $typeName) {
            return is_array($value);
        }

        if ('callable' === $typeName) {
            return is_callable($value);
        }

        if (!$type->isBuiltin()) {
            if (!is_object($value)) {
                return false;
            }

            try {
                $class = new ReflectionClass($typeName);
            } catch (ReflectionException $e) {
                return false;
            }

            return $class->isInstance($value);
        }

        switch ($typeName) {
            case 'bool': return is_bool($value);
            case 'float': return is_float($value);
            case 'int': return is_int($value);
            case 'string': return is_string($value);
            case 'iterable': return is_iterable($value);
        }

        return true;
    }
}
