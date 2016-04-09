<?php

/*
 * Function for load classes and files
 */
function loadFiles($class){
	$class = str_replace('\\','/',$class);
	$class .= '.php';
	$class = strtolower($class);
	if(file_exists(APP_PATH . $class)) require_once(APP_PATH . $class);
	if(file_exists($class)) require_once($class);
}
spl_autoload_register('loadFiles');
?>
