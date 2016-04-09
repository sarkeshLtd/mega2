<?php
/*
 * This class is for working with date and time and show calendars
 * now it's just support gregorian and jallali calendars.
 * 
 * author:Babak Alizadeh
 * email :alizadeh.babak@live.com
 */
 
 namespace Mega\Cls\calendar;
 use \Mega\Cls\core as core;
 use Mega\Cls\patterns as patterns;
 
 class calendar{
	 use patterns\singleton;
	 /*
	  * @var string,cerrent system calendar name
	  */
	 public $calendar;
	 
	 /*
	  * construct
	  */
	 function __construct(){
		 //get selected system calendar type;
		 $localize = new core\localize;
		 $localSettings = $localize->localize(); 
		 $this->calendar = $localSettings->calendar;
	 }
	 
	 
	public function cdate($format, $time ){
		if($this->calendar == 'jallali'){
			//create object from jallali calndar
			$jallali = new jallali;
			return $jallali->jdate($format,$time);
		}
		elseif($this->calendar == 'gregorian'){
			return date($format ,$time);
		}
		
	}
	 
	 
 }

?>
