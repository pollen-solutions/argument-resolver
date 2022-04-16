<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver\Resolvers;

class ParameterResolver
{
    /**
     * @param array $params
     */
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }
}
