<?php

spl_autoload_register(prepend: true, callback: function (string $classname) {
    $separator = '\\';

    [$namespace, $class] = explode($separator, $classname, 2);

    if ($namespace != 'Mathr')
        return false;

    $classname = str_replace($separator, '/', $class);

    require "src/{$classname}.php";
    return true;
});
