<?php
/*
* Library autoloader - __autoload
*/
if (class_exists('PadlLibrary')) {
	function __autoload($class) {
		
		$dirs = array(
			'Padl',
		);
		
		foreach ($dirs as $d) {
			$file = PadlLibrary::getPath().DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR.$class.'.php';
			if (file_exists($file) && is_file($file)) {
				require_once($file);
				return true;
			}
		}
		return false;
	}
}
