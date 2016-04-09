<?php
/*
 * Class for working with database record types
 * Author:Babak Alizadeh
 * Email:alizadeh.babak@gmail.com
 * Published under LGPL V3 license
 */

namespace Mega\Cls\Data;
class dbRow{
	
	/*
	* @var store row data
	*/
	public $row;
	 
	/*
	* @var store table name
	*/
	public $table;
	 
	/*
	* @var store id
	*/
	public $id;
	 
	/*
	* construct
	* @param string $table,name of table
	*/
	 
	function __construct($table = null){
		$this->row = [];
		$this->table = $table;
		$this->id = null;
	}
	
	/*
	* set record from dynamic properties
	* @param string $colName,name of column
	* @param string $value,value of cell
	*/
	public function __set($colName,$value){
		$this->row[$colName] = $value;
	}
	
	/*
	* get record value from dynamic properties
	* @param string $colName,name of column
	* @return value of cell
	*/
	public function __get($colName){
		if(array_key_exists($colName,$this->row)) return $this->row[$colName];
	}
	
	/*
	* check for that column is exist
	* @param string $colName,name of column
	* @return boolean(exist:true , else flase)
	*/
	public function __isset($colName){
		if(array_key_exists($colName,$this->row)) return true;
		return false;
	}
	
	/*
	* load row
	* @param array || object $row,values of columns in row
	* @param string $table,table name
	* @param integer $id,row id
	*/
	public function load($row,$table,$id){
		$this->row = array_merge($this->row, (array) $row);
		$this->setTable($table);
		$this->setId($id);
	}
	
	/*
	* set table name
	* @param string $table,table name
	*/
	public function setTable($table){
		$this->table = $table;
	}
	
	/*
	* set id of row
	* @param integer $id,row id
	*/
	public function setId($id){
		$this->id = $id;
	}
	
		/*
	* get table name
	* @return string,table name
	*/
	public function getTable(){
		return $this->table;
	}
	
	/*
	* get id of row
	* @return integer,id of table(if row not saved: null)
	*/
	public function getId(){
		return $this->id;
	}
	/*
	* fetch columns
	* @return array of columns
	*/
	public function fetch(){
		return $this->row;
	}	
}
