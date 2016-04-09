<?php
	namespace Mega\Cls\network;
	//this class is for working with network objects
	class network{
		
		//this function get file from url and save that in temp directory with random file name 
		//and return file address on serverto access that
		public function download($url){
			$headers = @get_headers($url);
			if($headers[0] == 'HTTP/1.1 404 Not Found') {
				//file not exist
				return '0';
			}
			else{
				//file exist going to download
				$file_name = APP_PATH . "upload/buffer/" . \core\general::random_string(5) . ".zip";
				echo $file_name;
				file_put_contents($file_name, file_get_contents($url));
				return $file_name;
			}

		}
	}
?> 
