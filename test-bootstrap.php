<?php
spl_autoload_register(function($className) {
	$parts = explode("\\", $className);
	array_shift($parts);
	
	$path = __DIR__ ."/src/". implode("/", $parts) .".php";
	
	if (is_file($path)) {
		require_once $path;
	}
});