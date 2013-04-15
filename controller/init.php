<?php
	session_start();
	function __autoload($class_name) {
	    include $_SERVER['DOCUMENT_ROOT'].'/helloworld/model/'.strtolower($class_name) . '.php';
	}
	

	function arrayToJSON($item){
		$string = '[';
		$j = 0;
		foreach($item as $i){

			if(is_object($i)){
				$string .= $i->toJson();
			}elseif(is_array($i)){
				$string .= arrayToJSON($i);
			}else{
				$string .= json_encode($i);
			}
			$j++;
			if($j<count($item)){
				$string .= ',';
			}
		}
		$string .= ']';
		return $string;
	}


?>