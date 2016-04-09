<?php
//this classs for using registry table in database
namespace Mega\Cls\core;
use Mega\Cls\database as db;
use Mega\Cls\patterns as patterns;
	
class registry{
	use patterns\singleton;
	
	/*
	* @var object orm
	*/
	private $orm;
	
	/*
	* construct
	*/
	function __construct(){
		$this->orm = db\orm::singleton();
	}
	
	/*
	* get key value from registry
	* @param string $plugin,name of plugin
	* @param string $key
	* @return string (not found: null)
	*/ 
	public function get($plugin, $key){
		$result = $this->orm->exec('SELECT r.id, r.a_key, r.value, p.name FROM registry r INNER JOIN plugins p ON p.id = r.plugin  WHERE p.name = ? and r.a_key = ?;', [$plugin, $key],SELECT_ONE_ROW);
		if(!is_null($result)) return $result->value;
		return null;
	}
	
	/*
	* find all settings for one plugin
	* @param string $plugin,name of plugin
	* @return array string (no settings found:null)
	*/ 
	public function getPlugin($plugin){
		$result = $this->orm->exec('SELECT r.a_key, r.value FROM registry r INNER JOIN plugins p ON p.id = r.plugin  WHERE p.name = ?;', array($plugin));
		$resultObject = new \Mega\Cls\Data\obj;
		foreach($result as $row)
			$resultObject->{$row->a_key} = $row->value;
		return $resultObject;
	}
	
	/*
	* update key value in registry
	* @param string $plugin,name of plugin
	* @param string $key
	* @param string $value,new value for key
	* @return boolean (successfull:true , fail:false)
	*/
	public function set($plugin, $key, $value){
		if($this->orm->count('plugins','name=?',[$plugin])	){
			$plugin = $this->orm->findOne('plugins','name=?',[$plugin]);
			$item = $this->orm->findOne('registry','plugin=? && a_key=?',[$plugin->id,$key]);
			$item->value = $value;
			$this->orm->store($item);
			return true;
		}
		//plugin not found 
		return false;
	}
	
	/*
	* delete all settings from plugin
	* @param string $plugin,plugin name
	* return void
	*/ 
	public function deletePlugin($plugin){
		$this->orm->exec('DELETE FROM registry WHERE plugin=?;', [$plugin],NON_SELECT); 
	}
	
	/*
	 * save new keys in registry
	 * @param string $plugin, name of plugin
	 * @param string $key,name of key
	 * @param string $value, default value of key
	 * @return boolean
	 */
	public function newKey($plugin,$key,$value){
		$orm = db\orm::singleton();
		if($orm->count('plugins','name=?',[$plugin]) != 0){
			$plugin = $orm->findOne('plugins','name=?',[$plugin]);
			if($orm->count('registry','plugin=? and a_key=?',[$plugin->id,$key]) == 0){
				$record = $orm->dispense('registry');
				$record->plugin = $plugin->id;
				$record->a_key = $key;
				$record->value = $value;
				$orm->store($record);
				return true;
			}
		}
		return false;
	}
}
?>
