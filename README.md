# Mathr
![license MIT](https://img.shields.io/badge/license-MIT-lightgrey.svg)
![version 2.0](https://img.shields.io/badge/version-2.0-green.svg)
[![Build Status](https://travis-ci.org/rodriados/mathr.svg?branch=master)](https://travis-ci.org/rodriados/mathr)
[![Coverage Status](https://coveralls.io/repos/github/rodriados/mathr/badge.svg?branch=master)](https://coveralls.io/github/rodriados/mathr?branch=master)

Mathr is a fast mathematical expression parser and calculator with some added juice.

## Usage

The simplest usage possible for Mathr is by simply sending in a math expression.

```php
<?php
$mathr = new \Mathr\Mathr;
$result = $mathr->evaluate("3 + 4 * 5");
echo $result; // 23
```

You can also create your own variables and functions!

```php
<?php
$mathr = new \Mathr\Mathr;
$mathr->evaluate("myvar = 10");
$mathr->evaluate("fibonacci(0) = 0");
$mathr->evaluate("fibonacci(1) = 1");
$mathr->evaluate("fibonacci(x) = fibonacci(x - 1) + fibonacci(x - 2)");
$result = $mathr->evaluate("fibonacci(myvar)");
echo $result; // 55
```

There are a plenty of native functions and variables which you can use at will.

```php
<?php
$mathr = new \Mathr\Mathr;
$mathr->evaluate("fibonacci(x) = (phi ^ x - (1 - phi) ^ x) / sqrt(5)");
$result = $mathr->evaluate("fibonacci(10)");
echo $result; // 55
```

## Install

The recommended way to install Mathr is via [Composer](http://getcomposer.org).

```json
{
    "require": {
        "rodriados/mathr": "v2.0"
    }
}
```
