# Argument Resolver Component

[![Latest Stable Version](https://img.shields.io/packagist/v/pollen-solutions/argument-resolver.svg?style=for-the-badge)](https://packagist.org/packages/pollen-solutions/argument-resolver)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Argument Resolver** Component is a smart arguments resolving of callable library.
It allows you to dynamically determine the arguments to pass to a function, an invokable class or a method.

## Installation

```bash
composer require pollen-solutions/argument-resolver
```

## Basic Usage

```php
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\ParameterResolver;

$acmeCallable = static function (int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) {
    var_dump($product_id, $in_stock, $price, $product_attrs);
    exit;
};

$parameters = [
    'product_id' => 1,
    'name'       => 'My beautiful sneaker',
    'in_stock'   => true,
    'price'      => 156.30,
    'product_attrs' => [
        'size'  => 12,
        'color' => 'pink'
    ]
];

$arguments = (new ArgumentResolver([new ParameterResolver($parameters)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

In below example, callable is a closure function.

It could be a class method :

```php
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\ParameterResolver;

class AcmeClass
{
    public function storeProduct(int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) 
    {
        var_dump($product_id, $in_stock, $price, $product_attrs);
        exit;
    }
}

$acmeCallable = [new AcmeClass(), 'storeProduct'];

$parameters = [
    'product_id' => 1,
    'name'       => 'My beautiful sneaker',
    'in_stock'   => true,
    'price'      => 156.30,
    'product_attrs' => [
        'size'  => 12,
        'color' => 'pink'
    ]
];

$arguments = (new ArgumentResolver([new ParameterResolver($parameters)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

Also, it could be an invokable class :

```php
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\ParameterResolver;

class AcmeClass
{
    public function __invoke(int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) 
    {
        var_dump($product_id, $in_stock, $price, $product_attrs);
        exit;
    }
}

$acmeCallable = new AcmeClass();

$parameters = [
    'product_id' => 1,
    'name'       => 'My beautiful sneaker',
    'in_stock'   => true,
    'price'      => 156.30,
    'product_attrs' => [
        'size'  => 12,
        'color' => 'pink'
    ]
];

$arguments = (new ArgumentResolver([new ParameterResolver($parameters)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

## Resolvers

### ParameterResolver

-- @todo or see below --

### ContainerResolver

The container resolver is used to resolve the arguments of a callable provided by a container that implements
\Psr\Container\ContainerInterface.

```php
use Pollen\Container\Container;
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\ContainerResolver;

class Foo {}

class Bar {}

$container = new Container();
$container->add(Foo::class, new Foo);
$container->add(Bar::class, new Bar);

$acmeCallable = static function (Foo $foo, Bar $bar) {
    var_dump($foo, $bar);
    exit;
};

$arguments = (new ArgumentResolver([new ContainerResolver($container)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

### RequestResolver

The request resolver is used to resolve arguments of a callable provided by an HTTP request that implements
Psr\Http\Message\ServerRequestInterface.

```php
use Laminas\Diactoros\ServerRequestFactory;
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\RequestResolver;

// This code must be served by your app and visits :
// http://127.0.0.1/?product_id=1&name=My beautiful sneaker&in_stock=true&price=156.30&product_attrs[size]=12&product_attrs[color]=pink
$request = ServerRequestFactory::fromGlobals();
// also you can force $_GET attributes :
//$request = ServerRequestFactory::fromGlobals(null, [
//    'product_id' => 1,
//    'name'       => 'My beautiful sneaker',
//    'in_stock'   => true,
//    'price'      => 156.30,
//    'product_attrs' => [
//        'size'  => 12,
//        'color' => 'pink'
//    ]
//]);

$acmeCallable = static function (int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) {
    var_dump($product_id, $in_stock, $price, $product_attrs);
    exit;
};

$arguments = (new ArgumentResolver([new RequestResolver($request)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

### RequestAttributeResolver

The request attribute attribute resolver is used to resolve arguments of a callable provided through attributes of an
HTTP request that implements Psr\Http\Message\ServerRequestInterface.

```php
use Laminas\Diactoros\ServerRequestFactory;
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\RequestAttributeResolver;

$request = ServerRequestFactory::fromGlobals();
$request = $request->withAttribute('product_id', 1);
$request = $request->withAttribute('name', 'My beautiful sneaker');
$request = $request->withAttribute('in_stock', true);
$request = $request->withAttribute('price', 156.30);
$request = $request->withAttribute('filters', [
    'size'  => 12,
    'color' => 'pink'
]);

$acmeCallable = static function (int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) {
    var_dump($product_id, $in_stock, $price, $product_attrs);
    exit;
};

$arguments = (new ArgumentResolver([new RequestAttributeResolver($request)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

### Chained resolvers

```php
use Pollen\Container\Container;
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\Resolvers\ContainerResolver;
use Pollen\ArgumentResolver\Resolvers\ParameterResolver;

class EntityManager {}

$container = new Container();
$container->add(EntityManager::class, new EntityManager);

$acmeCallable = static function (EntityManager $manager, int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) {
    var_dump($manager, $product_id, $in_stock, $price, $product_attrs);
    exit;
};

$parameters = [
    'product_id' => 1,
    'name'       => 'My beautiful sneaker',
    'in_stock'   => true,
    'price'      => 156.30,
    'product_attrs' => [
        'size'  => 12,
        'color' => 'pink'
    ]
];

$arguments = (new ArgumentResolver([new ParameterResolver($parameters), new ContainerResolver($container)]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

### Create a custom resolver

You can create your own custom resolver. In this example, we are creating an HTTP request session based resolver.
To works, a resolver must implements Pollen\ArgumentResolver\ResolverInterface.

```php
use Pollen\ArgumentResolver\ArgumentResolver;
use Pollen\ArgumentResolver\ResolverInterface;

class SessionResolver implements ResolverInterface
{
    protected array $params;

    public function __construct()
    {
        $this->params = $_SESSION;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter): ?array
    {
        $key = $parameter->getName();
        if (!$value = $this->params[$key] ?? null) {
            return null;
        }

        if (!$this->matchType($parameter, $this->params[$key])) {
            return null;
        }

        return [$key, $value];
    }

    /**
     * @param ReflectionParameter $parameter
     * @param mixed $value
     *
     * @return bool
     */
    protected function matchType(ReflectionParameter $parameter, $value): bool
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
            case 'bool':
                return is_bool($value);
            case 'float':
                return is_float($value);
            case 'int':
                return is_int($value);
            case 'string':
                return is_string($value);
            case 'iterable':
                return is_iterable($value);
        }

        return true;
    }
}

$_SESSION['product_id'] = 1;
$_SESSION['name'] = 'My beautiful sneaker';
$_SESSION['in_stock'] = true;
$_SESSION['price'] = 156.30;
$_SESSION['product_attrs'] = [
    'size'  => 12,
    'color' => 'pink',
];

$acmeCallable = static function (int $product_id, string $name, bool $in_stock, float $price, array $product_attrs) {
    var_dump($product_id, $in_stock, $price, $product_attrs);
    exit;
};

$arguments = (new ArgumentResolver([new SessionResolver()]))->resolve($acmeCallable);

$acmeCallable(...$arguments);
```

## Credits

- Freely inspired by Rybakit work in [ArgumentsResolver](https://github.com/rybakit/arguments-resolver)