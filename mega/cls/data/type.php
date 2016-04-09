<?php
namespace Mega\Cls\Data;
//check data type
class type{
	
	/*
	 * check for that variable is numeric
	 * @param string $value
	 * @return boolean
	 */
	public static function isNumber($value){
		if(is_numeric($value))
			return true;
		return false;
	}
	
	/*
	 * check for that string is a valid ip
	 * @param string $ip, input ip address
	 * @return boolean
	 */
	public static function isIp($ip){
		if(filter_var($ip, FILTER_VALIDATE_IP) === false)
			return false;
		return true;
	}
	
	/*
	 * check for that entered string is in email format
	 * @param string $email, email
	 * @return boolean
	 */
	public static function isEmail($email){
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			return true;
		return false;
	}
}
?>
