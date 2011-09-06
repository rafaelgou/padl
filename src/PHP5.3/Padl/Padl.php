<?php
class Padl {
    
    const VERSION = "2.0.0";
    static $initPath;
    static $initialized = false;
    
    /**
     * Internal autoloader for spl_autoload_register().
     * 
     * @param string $class
     */
    public static function autoload($class)
    {
        $path = dirname(__FILE__).'/'.str_replace('\\', '/', $class).'.php';

        if (!file_exists($path))
        {
          return;
        }

        if (self::$initPath && !self::$initialized)
        {
          self::$initialized = true;
          require self::$initPath;
        }

        require_once $path;
    }

    private function __construct() { }
    
    /**
     * Configure autoloading using Padl.
     * 
     * This is designed to play nicely with other autoloaders.
     *
     * @param string $initPath The init script to load when autoloading the first Padl class
     */
    public static function registerAutoload($initPath = null)
    {
      self::$initPath = $initPath;
      spl_autoload_register(array('Padl', 'autoload'));
    }

    public final static function getVersion()
    {
        return self::VERSION;
    }
    
}
	