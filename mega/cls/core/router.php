<?php
/*
 * this class seperate url addrress
 * for doing process we have some parameters that send with GET 
 * 1- plugin parameter for finding what plugin do this process
 * 2- action parameter for run that action on plugin
 * and some etc parameters that plugin process that.
 * if nothing send with action with GET class change that to 'default' and send that to plugin
 */
namespace Mega\Cls\Core;
use Mega\Cls\core as core;
use Mega\Cls\network as network;
use Mega\Cls\browser as browser;
use \Mega\Apps as plg;
use \Mega\Cls\Database as db;
use Mega\Cls\patterns as patterns;

class router{
	use patterns\singleton;
	
	/*
	* @var string, plugin name
	*/
	private $plugin;
	/*
	* @var string, action name
	*/
	private $action;
	
	/*
	* @var array, core settings
	*/
	private $localize;
	
	/*
	* @var object,plugin for controll plugins
	*/
	private $objPlugin;
	
	/*
	* construct
	* @param string $plugin, plugin name
	* @param string $action, action name
	*/
	function __construct($plugin='' ,$action=''){
		$this->objPlugin = new \Mega\Cls\Core\plugin;
		//set last page that user see
		$this->setLastPage();
		//get localize
		$localize = core\localize::singleton();
		$this->localize = $localize->localize();
		if($plugin == '' && $action == ''){
			if(!is_null(PLUGIN)){
				$this->plugin = PLUGIN;
				//now we check action
				if(!is_null(ACTION)) $this->action = ACTION;
				else $this->action = 'default';
			}
		}
		else{
			$this->plugin = $plugin;
			$this->action = $action;
		}			
	}
	
	/*
	* show main requested content
	* @param boolean $show ,(echo:true, else:false)
	* @return string,requested content
	*/
	public function showContent($show = true){
		//this function run from page class.
	    // this function load plugin and run controller
	    //checking for that plugin is enabled
	    $content = browser\msg::pageNotFound();
	    if($this->objPlugin->enabled($this->plugin)){
			if(file_exists('./mega/apps/' . $this->plugin . '/action.php')){
				$PluginName = '\\Mega\\Apps\\' . $this->plugin . '\\action';
			}
			elseif(file_exists('./apps/' . $this->plugin . '/action.php')){
				$PluginName = '\\Apps\\' . $this->plugin . '\\action';
			}

	     	$plugin = new $PluginName;
	     	//run action directly
			if(method_exists($plugin,$this->action))
			$content = call_user_func(array($plugin,$this->action),'content');	
	      }
	      browser\page::setPageTitle($content[0]);
          //show header in up of content or else
          $outputContent = null;
          if(sizeof($content) == 2)
            $outputContent = browser\page::showBlock($content[0],$content[1],'MAIN');
          elseif(sizeof($content) == 3 && $content[2] == true)
            $outputContent = browser\page::showBlock($content[0],$content[1],'BLOCK','default');
		  //show content id show was set
		  if($show && !is_null($outputContent)) echo $outputContent;
		  return $content; 
	}
	
	/*
	* run services and jump request do plugin
	*/
	public function runService(){ 
		 $result = _t('Warning:Your requested service not found!');
		 if($this->objPlugin->enabled($this->plugin)){
			 if(file_exists('./mega/apps/' . $this->plugin . '/service.php'))
				 $PluginName = '\\Mega\\Apps\\' . $this->plugin . '\\service';
			 elseif(file_exists('./apps/' . $this->plugin . '/service.php'))
				 $PluginName = '\\Apps\\' . $this->plugin . '\\service';

	     		 $plugin = new $PluginName;			
	     		 if(method_exists($plugin,$this->action))
					 $result = call_user_func(array($plugin,$this->action),'content');

	     }
	     echo $result;   
	}
	
	/*
	* runing services from controls
	*/
	public function runControl(){
		$options = str_replace('_a_n_d_','&',$_REQUEST['options']);
		$elements = new core\uiobjects($options);
		if(file_exists('./mega/apps/' . $this->plugin . '/event.php'))
					$PluginName = '\\Mega\\Apps\\' . $this->plugin . '\\event';
		elseif(file_exists('./apps/' . $this->plugin . '/event.php'))
			$PluginName = '\\Apps\\' . $this->plugin . '\\event';
		else
			exit('plugin not found');
		//run event,going to run function
		$plugin = new $PluginName;
		$result = call_user_func(array($plugin, $this->action),$elements->get_elements());
		
		foreach($result as $r=>$row){
			foreach($row as $c=>$col){
				$result[$r][$c] = str_replace('&','_a_n_d_',$result[$r][$c]);
			}
		}
		//now show result in xml for use in javascript
		$xml = new db\xml($result);
		echo $xml->arrayToXml($result, "root");
	}
	
	/*
	* refresh page and jump to address
	* @param string $url,page address(refresh:0)
	* @param boolean $inner , (inner url:true , else:false)
	* @param integer $time,set time for jumping time
	*/
	public function refresh($url='0',$inner_url=true , $time=5){
		if($url=='0') $url= DOMAIN_EXE;
		elseif($inner_url && $url != '0') $url= DOMAIN_EXE . $url;
		header("Refresh: $time ; url=$url");
	}
	
	/*
	* jump to address
	* @param string $url,page address(refresh:0)
	* @param boolean $inner , (inner url:true , else:false)
	*/
	public static function jump($url,$inner_url=true){
		//check for show 404 not found message
		if($url == '404')
			$url = ['service','1','plugin','msg','action','msg_404'];
		if(!$inner_url && $url != DOMAIN_EXE) $url= DOMAIN_EXE . $url;
		elseif($url==DOMAIN_EXE) $url= DOMAIN_EXE;
		elseif(is_array($url)) $url = core\general::createUrl($url);
		header("Location:$url");
		return ['',''];
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/*
	* set last page that visited in cookie
	* @param string $url, page URI
	*/
	public function setLastPage($url=''){
		$lastUrl ='';
		if($url!='') $lastUrl=$url;
		else
			if(isset($_SERVER['HTTP_REFERER']))
				$last_url = $_SERVER['HTTP_REFERER'];
		
		if($lastUrl!=''){
			setcookie('SYS_LAST_PAGE',$lastUrl);
		}
	}
	
	/*
	* jump page that set in cookie
	*/
	public function jumpLastPage(){
		if(isset($_COOKIE['SYS_LAST_PAGE'])) header('Location: '. $_COOKIE['SYS_LAST_PAGE']);
		else header('Location: ' . DOMAIN_EXE );
	}
	
	/*
	* return last page that viewed
	*/
	public static function getLastPage(){
		$obj_io = new network\io;
		if(isset($_COOKIE['SYS_LAST_PAGE'])) return  $obj_io->cin('SYS_LAST_PAGE','cookie');
		return DOMAIN_EXE ;
	}
	
	/*
	* return cerrent address of page(cerent url)
	* @return string page url
	*/
	public function thisUrl(){
		return $_SERVER['REQUEST_URI'];	
	}
}
?>
