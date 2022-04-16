<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver\Resolvers;

use Pollen\ArgumentResolver\AbstractResolver;
use ReflectionParameter;

class ParameterResolver extends AbstractResolver
{
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @inerhitDoc
     */
    public function resolve(ReflectionParameter $parameter): ?array
    {
        $found = null;

        foreach ($this->params as $key => $value) {
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

        return $found;
    }
}
