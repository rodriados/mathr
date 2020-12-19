<?php
/**
 * Autoloader for unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */

const NS_SEPARATOR = '\\';
const DIR_SEPARATOR = DIRECTORY_SEPARATOR;

/**
 * Loads a class from its full name.
 * @param string $classname The class to be loaded.
 * @return bool Has the class been loaded?
 */
function loader(string $classname): bool
{
    [$namespace, $class] = explode(NS_SEPARATOR, $classname, 2);

    if ($namespace != 'Mathr')
        return false;

    $classname = str_replace(NS_SEPARATOR, DIR_SEPARATOR, $class);
    $filename = "src" . DIR_SEPARATOR . "{$classname}.php";

    if (!file_exists($filename))
        return false;

    require_once $filename;
    return true;
};

spl_autoload_register(callback: 'loader', prepend: true);
