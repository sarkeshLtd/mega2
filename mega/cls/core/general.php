<?php
//this function is for general works

namespace Mega\Cls\core;
class general{

	/*
	 * create random string
	 * @param integer $length,len of string
	 * @param string $type,(Numberic:N , Character:C, numeric and character:NC)
	 * @return string
	 */	
	static function randomString($length = 10 ,$type = 'NC') {
		if($type == 'NC') $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		elseif($type == 'N') $characters = '0123456789';
		else $characters = 'abcdefghijklmnopqrstuvwxyz';
	    $string = '';    
	    for ($p = 0; $p < $length; $p++) $string .= $characters[mt_rand(0, strlen($characters)-1)];
	    return $string;
	}
	
	/*
	 * create internal url
	 * @param array $parameters,URL Parameters (example: ['plugin','users','action','register']
	 * @return string url, (example 'http://DOMAIN_EXE.com/?plugin=users&action=register
	 */
	static function createUrl($parameters,$language = null){
		$url = '';
		if(defined('CLEAN_URL')) if(!CLEAN_URL) $url = '?q=';
		if(!is_null($language)) $url .= $language . '/';
		elseif(MULTI_LANG){
			if(array_key_exists('siteLanguage',$_SESSION)) $url .= $_SESSION['siteLanguage'] . '/';
			else $url .= SITE_LANG . '/';
		}
		for($i = 0; $i<= max(array_keys($parameters)) ; $i ++){
			if($i == max(array_keys($parameters)))
				$url .= $parameters[$i];
			else
				$url .= $parameters[$i] . '/';
		}
		return DOMAIN_EXE . '/' . $url;	
	}

    /**
     * check for that pluginoption is defined or not
     * @return boolean
     */
    public static function hasPluginOptions(){
        if(defined('PLUGIN_OPTIONS'))
            return true;
        return false;
    }
}
?>
