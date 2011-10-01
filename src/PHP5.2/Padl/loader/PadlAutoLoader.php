<?php
/**
 * Autoload Class for spl_autoload_register 
 * 
 * Project:   PHP Application Distribution License Class
 * File:      PadlAutoLoader.php
 *
 * Copyright (C) 2005 Oliver Lillie
 * Copyright (C) 2011 Rafael Goulart
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by  the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author  Oliver Lillie buggedcom <publicmail@buggedcom.co.uk>
 * @author  Rafael Goulart <rafaelgou@gmail.com>
 * @license GNU Lesser General Public License
 * @version Release: 1.0.0
 * @link    http://padl.rgou.net
 * @link    http://www.buggedcom.co.uk/
 * @link    http://www.phpclasses.org/browse/package/2298.html
 * @history---------------------------------------------
 * see CHANGELOG
 */
class PadlAutoloader
{

    /**
     * Instance of this class
     * @var PadlAutoLoader 
     */
    public static $loader;

    /**
     * Constructor
     *
     * set private to avoid directly instatiation to implement
     * but is not a Singleton Design Pattern
     **/
    private function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Configure autoloading using Padl.
     *
     * This is designed to play nicely with other autoloaders.
     *
     * @return void
     */
    public static function registerAutoload()
    {
        if (self::$loader == null) {
            self::$loader = new PadlAutoloader();
        }
        return self::$loader;
    }

    /**
     * Load class
     * 
     * @param string $dir   The directory to load
     * @param string $class The class name
     * 
     * @return void
     */
    private static function loadClass($dir, $class)
    {
        $file = PadlLibrary::getPath().DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$class.'.php';
        if (file_exists($file) && is_file($file)) {
            require_once $file;
        }
    }

    /**
     * Internal autoloader for spl_autoload_register().
     * 
     * Permits to test various diretories,
     * just add multiples loadClass
     *
     * @param string $class The class to load
     *
     * @return void
     */
    public static function autoload($class)
    {
        self::loadClass('Padl', $class);
    }

}
