<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionFunctionAbstract;

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
     * @param ResolverInterface[]|null $resolvers
     *
     * @return array
     * @throws ReflectionException
     */
    public function resolve($function, ?array $resolvers = []): array
    {
        $reflection = $function instanceof ReflectionFunctionAbstract
            ? $function : ReflectionFactory::create($function);

        if (!$number = $reflection->getNumberOfParameters()) {
            return [];
        }

        $arguments = array_fill(0, $number, null);

        foreach ($reflection->getParameters() as $pos => $parameter) {
            foreach ($resolvers as $resolver) {
                $result = $resolver->resolve($parameter);

                if ($result !== null) {
                    $arguments[$pos] = $result[1];
                    continue 2;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[$pos] = $parameter->getDefaultValue();
                continue;
            }

            throw new InvalidArgumentException(sprintf('Unresolvable parameters %s', $parameter));
        }

        return $arguments;
    }
}
