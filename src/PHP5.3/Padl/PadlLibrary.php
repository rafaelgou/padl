<?php
class PadlLibrary {
	
	const VERSION = "2.0.0";
	private static $library;
	private static $path;
//	public static $resources;
//	public static $config;
//	public static $log;
	
	private function __construct() {
		self::$path = (dirname(__FILE__));
		if (function_exists('spl_autoload_register')) {
			require_once "loader".DIRECTORY_SEPARATOR."PadlAutoLoader.php";
			PadlAutoloader::init();
		} else {
			require_once "loader".DIRECTORY_SEPARATOR."PadlAutoFunctionLoader.php";
		}
//		self::$resources = PadlResources::init();
//		self::$config = PadlConfig::init();
//		self::$log = LogPadl::init();
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

