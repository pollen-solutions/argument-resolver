<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionClass;

class ArgumentResolver
{
    /**
     * @param ContainerInterface|null $container
     */
    protected ?ContainerInterface $container;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     *
     * @param callable|string|array $function
     * @param array $parameters
     *
     * @return array
     * @throws ReflectionException
     */
    public function resolve($function, array $parameters = []) : array
    {
        $reflection = $function instanceof ReflectionFunctionAbstract
            ? $function : ReflectionFactory::create($function);


        if (!$number = $reflection->getNumberOfParameters()) {
            return [];
        }

        $arguments = array_fill(0, $number, null);

        foreach ($reflection->getParameters() as $pos => $parameter) {
            $result = $this->match($parameter, $parameters);

            if ($result) {
                $arguments[$pos] = $result[1];
                unset($parameters[$result[0]]);
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[$pos] = $parameter->getDefaultValue();
                continue;
            }

            throw new InvalidArgumentException(sprintf('Unresolvable parameters %s', $parameter));
        }

        return $arguments;
    }

    protected function match(ReflectionParameter $parameter, array $parameters) : ?array
    {
        $found = null;

        foreach ($parameters as $key => $value) {
            if (!$this->matchType($parameter, $value)) {
                continue;
            }

            if ($key === $parameter->getName()) {
                return [$key, $value];
            }

            if (!$found) {
                $found = [$key, $value];
            }
        }

        if ($this->container) {
            $alias = $parameter->getName();
            if ($this->container->has($alias)) {
                try {
                    return [$alias, $this->container->get($alias)];
                } catch (ContainerExceptionInterface|NotFoundExceptionInterface $e) {
                    unset($e);
                }
            }
        }

        return $found;
    }

    /**
     * Checks if the value matches the parameter type.
     *
     * @param mixed $value
     */
    private function matchType(ReflectionParameter $parameter, $value) : bool
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
