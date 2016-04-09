<?php
namespace Mega\Cls\Database;
/*
 * This class is for working with xml data
 */
 class xml{
	/*
	* set child element in xml
	* @param array $arr
	* @param SimpleXMLElement $xml
	*/ 
	private function array_to_xml(array $arr, \SimpleXMLElement $xml){
		foreach ($arr as $k => $v) {
			is_array($v)
				? $this->array_to_xml($v, $xml->addChild($k))
				: $xml->addChild($k, $v);
		}
		return $xml;
	}
	/*
	* change array to xml
	* @param array $arr
	* @param string $root,root tag in xml file
	* @return string in xml format
	*/
	public function arrayToXml($arr, $root){
		return $this->array_to_xml($arr, new \SimpleXMLElement('<' . $root .'/>'))->asXML();
	}
 }

?>
