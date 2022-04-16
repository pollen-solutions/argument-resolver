<?php

declare(strict_types=1);

namespace Pollen\ArgumentResolver\Resolvers;

use Psr\Http\Message\ServerRequestInterface;

class RequestAttributeResolver extends ParameterResolver
{
    protected ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        parent::__construct($this->request->getAttributes());
    }
}
