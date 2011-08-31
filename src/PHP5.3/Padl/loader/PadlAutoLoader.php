<?php
/*
* Library autoloader - spl_autoload_register
*/
class PadlAutoloader {

	public static $loader;
	
	private function __construct() {
		spl_autoload_register(array($this, 'license'));
	}

	public static function init() {
		if (self::$loader == null) {
			self::$loader = new PadlAutoloader();
		}
		return self::$loader;
	}	
	
	private function addClass($dir, $class){
		$file = PadlLibrary::getPath().DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$class.'.php';
		if (file_exists($file) && is_file($file)) {
			require_once $file;
		}
	}
	
	public function license($class) {
		$this->addClass('license', $class);
	}

}
