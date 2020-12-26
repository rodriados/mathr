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
 * Loads a fixture class for specialized unit tests.
 * @param string $classname The class to be loaded.
 * @return bool Has the class been loaded?
 */
function loadFixture(string $classname): bool
{
    $classname = str_replace(NS_SEPARATOR, DIR_SEPARATOR, $classname);
    $filename = join(DIR_SEPARATOR, ['test', 'fixtures', "{$classname}.php"]);

    if (!file_exists($filename))
        return false;

    require_once $filename;
    return true;
}

/**
 * Loads a class from its full name.
 * @param string $classname The class to be loaded.
 * @return bool Has the class been loaded?
 */
function loadProject(string $classname): bool
{
    if (!str_starts_with($classname, 'Mathr\\'))
        return loadFixture($classname);

    $classname = substr($classname, 6);
    $classname = str_replace(NS_SEPARATOR, DIR_SEPARATOR, $classname);
    $filename = join(DIR_SEPARATOR, ['src', "{$classname}.php"]);

    if (!file_exists($filename))
        return false;

    require_once $filename;
    return true;
}

spl_autoload_register(callback: 'loadProject', prepend: true);
