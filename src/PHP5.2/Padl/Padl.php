<?php
class PadlLibrary {
	
	private static $library;
	private static $path;
	
	private function __construct() {
		self::$path = (dirname(__FILE__));
		if (function_exists('spl_autoload_register')) {
			require_once "loader".DIRECTORY_SEPARATOR."PadlAutoLoader.php";
			PadlAutoloader::init();
		} else {
			require_once "loader".DIRECTORY_SEPARATOR."PadlAutoFunctionLoader.php";
		}
	}
	
	public static function init() {
		if (self::$library == null) {
			self::$library = new PadlLibrary();
		}
		return self::$library;
	}
	
	public final static function getVersion(){
		return self::VERSION;
	}
	public final static function getPath(){
		return self::$path;
	}
	
}
PadlLibrary::init();

