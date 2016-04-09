<?php
#This file is Mega web framework main configration file

if(file_exists('db-config.php')){
	require_once("db-config.php");
}

#save  domain for load system
if($_SERVER['PHP_SELF'] != '/index.php')
    define ("DOMAIN_EXE",'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) );
else
    define ("DOMAIN_EXE",'http://' . $_SERVER['HTTP_HOST'] );

#config file system
define('APP_PATH',dirname(__file__) . '/');

#this url use for installing plugin 
#in this address sore plugins
define('PLUGIN_CENTER_ADR','http://plugins.mega.sarkesh.org/');

#THIS URL SET SERVER FOR GET AVALABEL NEW VERSIONS AND SOME MORE INFORMATIONS
define('SERVER_INFO','http://service.sarkesh.org/');

#GET NEW VERSIONS FROM THIS DOMAIN
define('UPDATE_SERVER','http://megacmf.sarkesh.org');

#Developers email for get reports
define('DEVELOPERS_EMAIL','bug@sarkesh.org');

#error reporting state. for more info about this variable see php documents
define('ERROR_REPORTING',E_ALL | E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

#Show errors or not
define('SHOW_ERRORS',FALSE);

#With this config you can set where php error logs will be stored
define('ERRORS_LOG_PLACE',APP_PATH . 'error_log.txt');

#define static variable for show memory and cpu usage
define('SHOW_SYS_STATICS',FALSE);

?>
