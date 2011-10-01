<?php
/**
 * Autoload function for __autoload
 * 
 * Project:   PHP Application Distribution License Class
 * File:      PadlAutoLoaderFunction.php
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
if (class_exists('PadlLibrary')) {
    /**
     * Autoload class
     * 
     * @param string $class The class name
     * 
     * @return boolean 
     */
    function __autoload($class)
    {
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
