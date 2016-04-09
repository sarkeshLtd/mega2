<?php
	namespace Mega\Cls\Core;
	use Mega\Cls\Database as db;
	use Mega\Cls\Patterns as patterns;
	//this class controll plugins
	class plugin{
		use patterns\singleton;
		/*
		* @var object , orm class
		*/
		private $orm;
		
		/*
		* construct
		*/
		function __construct(){
			$this->orm = db\orm::singleton();
		}
		
		/* 
		* check for that plugin is enabled
		* @param string $plugin,name of plugin
		* @return boolean (enbaled:true , else:false)
		*/
		public function enabled($plugin){
			if($this->orm->count('plugins',"enable = '1' and name = ?" ,[$plugin]) != 0)
				return true;
			return false;
		}
		
		/*
		* disable plugin from database
		* @param string $plugin,name of plugin
		*/
		public function disable($plugin){
			$this->db->exec("UPDATE SET state = '0' WHERE name = ?", [$plugin], NON_SELECT);
		}
		
		/*
		* get plugin from server and extract that on plugins folder
		* @param string $plugin,name of plugin
		*/
		public function download($plugin){
			$net = new \network\network;
			$fileAdr = $net->download(PluginsCenter . $plugin . '/latest.zip');
			$zip = new \archive\zip($fileAdr);
			$zip->extract('plugins');
		}
	
	}

?>
