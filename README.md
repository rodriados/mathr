# Mathr
![license MIT](https://img.shields.io/badge/license-MIT-lightgrey.svg)
![version 1.0](https://img.shields.io/badge/version-1.0-green.svg)
[![Build Status](https://travis-ci.org/rodriados/mathr.svg?branch=master)](https://travis-ci.org/rodriados/mathr)

Mathr is a fast mathematical expression parser and calculator with some added juice.

## Usage

The simplest usage possible for Mathr is by simply sending in a math expression.

```php
<?php
$mathr = new \Mathr\Evaluator;
$result = $mathr->evaluate("3 + 4 * 5");
echo $result->value(); // 23
```

You can also create your own variable and functions!

```php
<?php
$mathr = new \Mathr\Evaluator;
$mathr->evaluate("myvar = 10");
$mathr->evaluate("fibonacci(0) = 0");
$mathr->evaluate("fibonacci(1) = 1");
$mathr->evaluate("fibonacci(x) = fibonacci(x - 1) + fibonacci(x - 2)");
$result = $mathr->evaluate("fibonacci(myvar)");
echo $result->value(); // 55
```

There are a plenty of native functions and variables which you can use at will.

```php
<?php
$mathr = new \Mathr\Evaluator;
$mathr->evaluate("fibonacci(x) = (phi^x - (1-phi)^x)/sqrt(5)");
$result = $mathr->evaluate("fibonacci(10)");
echo $result->value(); // 55
```

## Install

The recommended way to install Mathr is via [Composer](http://getcomposer.org).

```json
{
    "require": {
        "rodriados/mathr": "dev-master"
    }
}
```
