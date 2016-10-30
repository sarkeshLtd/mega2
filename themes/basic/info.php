<?php
namespace theme;
use core\data as data;
	class basic{
		/*
		 * return theme places for insert widgets
		 */
		public static function getPlaces(){
			return ['sidebar1','top_content','main_menu','slide_show','footer','top_footer1','top_footer2','top_footer3','top_footer4'];
		}
		
		/*
		 * return theme info
		 */
		public static function getInfo(){
			$info = new data\obj;
			$info->name = _t('Basic');
			$info->author = _t('Babak Alizadeh');
			return $info;
		}
	}
?>
