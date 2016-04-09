<?php
//this class is for validate inputs
namespace core\lib\base;
use Mega\Cls\patterns as patterns;
class validator{
	use patterns\singleton;
	/*
	 * check for ip to be valid
	 * @inp string,entered ip string
	 * @return boolean,(true:valid, false:invalid)
	 */
	public static function ip($inp){
		return filter_var($inp,FILTER_VALIDATE_IP)
	}
	
}
?>
