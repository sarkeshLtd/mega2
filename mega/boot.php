<?php
//start session system
session_start();
//this include file has autoload function
require_once(APP_PATH . 'mega/inc/autoload.php');

if(file_exists(APP_PATH . "db-config.php")) {
    //going to run sarkesh!
    include_once(APP_PATH . "config.php");
    //LOAD INC Files
    if(SHOW_ERRORS) require_once( APP_PATH . 'mega/inc/debug.php');

	require_once( APP_PATH . 'mega/defines.php');
	require_once(APP_PATH . 'mega/inc/localize.php');

	//load parts in action mode
	if(isset($_REQUEST['q'])){
		require_once(APP_PATH . 'mega/inc/load.php');
	}
	else{
		//jump to home page
		$localize = \Mega\Cls\Core\localize::singleton();
		$local = $localize->localize();
		\Mega\Cls\Core\router::jump(\Mega\Cls\Core\general::createUrl([$local->home],$local->language) ,true);
		exit();
	}
}
else{
	//jump to installing page
	require_once(APP_PATH . "install/index.php");
}
		
?>
