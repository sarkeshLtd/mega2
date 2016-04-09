<?php
namespace Mega\Cls\Data\type;
/**
 * @author babak alizadeh
 * @copyright 2014 gnu gpl v3
 * this class use in next version of MegaCMF
 */

class block{
    public $type = 'block';
    public $header;
    public $body;
    public $show_header;
    
    public function __construct($header,$body='',$show=true){
		$this->header = $header;
		$this->body = $body;
		$this->show_header = $show;
	}
	
	public function draw(){
		return [$this->header,$this->body,$this->show_header];
	}
    
}
?>
