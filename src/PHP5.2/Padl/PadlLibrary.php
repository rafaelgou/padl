<?php
/**
 * Padl Library Loader Class
 * 
 * Project:   PHP Application Distribution License Class
 * File:      PadlLibrary.php
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
class PadlLibrary
{
    /**
     * Instance of this class
     * @var PadlLibrary
     */
    private static $library;

    /**
     * Path of this file
     * @var string
     */
    private static $path;

    /**
     * Constructor
     *
     * set private to avoid directly instatiation to implement
     * Singleton Design Pattern
     **/
    private function __construct()
    {
        self::$path = (dirname(__FILE__));
        if (function_exists('spl_autoload_register')) {
            require_once "loader".DIRECTORY_SEPARATOR."PadlAutoLoader.php";
            PadlAutoloader::registerAutoload();
        } else {
            require_once "loader".DIRECTORY_SEPARATOR."PadlAutoLoaderFunction.php";
        }
    }

    /**
     * Initialize autoloading for Padl.
     *
     * This is designed to play nicely with other autoloaders.
     *
     * @return void
     */
    public static function init()
    {
        if (self::$library == null) {
            self::$library = new PadlLibrary();
        }
        return self::$library;
    }

    /**
     * Returns de path
     * 
     * @return string
     */
    public final static function getPath()
    {
        return self::$path;
    }

}