# Mathr
![license MIT](https://img.shields.io/badge/license-MIT-lightgrey.svg)
![version 3.0](https://img.shields.io/badge/version-3.0-green.svg)
[![Build Status](https://travis-ci.org/rodriados/mathr.svg?branch=master)](https://travis-ci.org/rodriados/mathr)
[![Coverage Status](https://coveralls.io/repos/github/rodriados/mathr/badge.svg?branch=master)](https://coveralls.io/github/rodriados/mathr?branch=master)

Mathr is a fast mathematical expression parser, evaluator and calculator with some added juice.

## Usage

The simplest usage possible for Mathr is by simply sending in a math expression.

```php
<?php
$mathr = new Mathr;
$result = $mathr->evaluate("3 + 4 * 5");
echo $result; // 23
```

You also can create your own variables and functions!

```php
<?php
$mathr->evaluate("v = 10");
$mathr->evaluate("fibonacci(0) = 0");
$mathr->evaluate("fibonacci(1) = 1");
$mathr->evaluate("fibonacci(x) = fibonacci(x - 1) + fibonacci(x - 2)");
$result = $mathr->evaluate("fibonacci(v)");
echo $result; // 55
```

If you want to, it's possible to bind functions to native PHP closures!

```php
<?php
$mathr->set('triangle(b, h)', fn ($b, $h) => ($b * $h) / 2);
$result = $mathr->evaluate('triangle(5, 8)');
echo $result; // 20
```

There are a plenty of native functions and variables which you can use at will.

```php
<?php
$mathr->evaluate("fibonacci(x) = ceil((φ ^ n - (1 - φ) ^ n) / sqrt(5))");
$result = $mathr->evaluate("fibonacci(10)");
echo $result; // 55
```

You can easily export and import your functions and variables.

```php
<?php
$exported = $mathr->export(); // Exports all bound functions and variables.
$mathr->import($exported); // Imports functions and variables.
```

## Install

The recommended way to install Mathr is via [Composer](http://getcomposer.org).

```json
{
    "require": {
        "rodriados/mathr": "v3.0"
    }
}
```
