<?php

spl_autoload_register(function(string $class) {
	list($namespace, $class) = explode('\\', $class, 2);
	if($namespace != "Mathr")
		return false;
	require "src/".str_replace("\\", '/', $class).".php";
	return true;
}, false, true);
