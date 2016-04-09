<?php

/**
* Error handler, passes flow over the exception logger with new ErrorException.
*/
function log_error( $num, $str, $file, $line, $context = null )
{
    log_exception( new ErrorException( $str, 0, $num, $file, $line ) );
}

/**
* Uncaught exception handler.
*/
function log_exception( Exception $e )
{
    $registry = \Mega\Cls\core\registry::singleton();

    $errorMsg = '';
    if ( $registry->get('administrator','devMode') == 1){
        $errorMsg .= "<div style='text-align: center;'>";
        $errorMsg .= "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
        $errorMsg .= "<table style='width: 800px; display: inline-block;'>";
        $errorMsg .= "<tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" . get_class( $e ) . "</td></tr>";
        $errorMsg .= "<tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}</td></tr>";
        $errorMsg .= "<tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>";
        $errorMsg .= "<tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>";
        $errorMsg .= "</table></div>";
    }
    print $errorMsg;
    $errorMsg .= 'DOMAIN_EXE:' . DOMAIN_EXE . '</br></hr>';
    $errorMsg .= 'Ip:' . $_SERVER['SERVER_ADDR'] . '</br></hr>';
    //send to developers
    @\Mega\Cls\network\mail::simpleSend('bug report',S_DEVELOPERS_EMAIL,'Error in system', $errorMsg);
    exit();
}

/**
* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
*/
function check_for_fatal()
{
    $error = error_get_last();
    if ( $error["type"] == E_ERROR )
        log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
}

register_shutdown_function( "check_for_fatal" );
set_error_handler( "log_error" );
set_exception_handler( "log_exception" );
ini_set( "display_errors",'On');
error_reporting( E_ALL );
// ERROR_REPORTING defined in config file
ini_set('error_log',ERRORS_LOG_PLACE);

?>
