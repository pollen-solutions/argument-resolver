<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver;

use InvalidArgumentException;
use ReflectionFunctionAbstract;
use RuntimeException;

class ArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @var array|ResolverInterface[]
     */
    private static array $defaultResolvers = [];

    /**
     * @var ResolverInterface[]|null
     */
    private ?array $resolvers;

    /**
     * @param array|null $resolvers
     */
    public function __construct(?array $resolvers = null)
    {
        if ($resolvers !== null) {
            foreach ($resolvers as $resolver) {
                $this->addResolver($resolver);
            }
        }
    }

    /**
     * @param ResolverInterface[]|null $resolvers
     *
     * @return ArgumentResolverInterface
     */
    public static function create(?array $resolvers = null): ArgumentResolverInterface
    {
        return new self($resolvers);
    }

    /**
     * @return array|ResolverInterface[]
     */
    public static function getDefaultResolvers(): array
    {
        return self::$defaultResolvers;
    }

    /**
     * @param ResolverInterface[]
     *
     * @return void
     */
    public static function setDefaultResolvers(array $defaultResolvers): void
    {
        foreach($defaultResolvers as $resolver) {
            if ($resolver instanceof ResolverInterface) {
                throw new RuntimeException(
                    sprintf('Default resolver must be an instance of %s', ResolverInterface::class)
                );
            }
        }
        self::$defaultResolvers = $defaultResolvers;
    }

    /**
     * @inheritDoc
     */
    public function addResolver(ResolverInterface $resolver): ArgumentResolverInterface
    {
        $this->resolvers[] = $resolver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResolvers(): ?array
    {
        return $this->resolvers;
    }

    /**
     * @inheritDoc
     */
    public function resolve($function): array
    {
        $reflection = $function instanceof ReflectionFunctionAbstract
            ? $function : ReflectionFactory::create($function);

        if (!$number = $reflection->getNumberOfParameters()) {
            return [];
        }

        $arguments = array_fill(0, $number, null);
        $resolvers = $this->getResolvers() ?: self::getDefaultResolvers();

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
