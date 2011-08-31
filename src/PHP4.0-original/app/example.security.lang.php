<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.security.lang.php
	*
	* Copyright (C) 2005 Oliver Lillie
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
	* @link http://www.buggedcom.co.uk/
	* @link http://www.phpclasses.org/browse/package/2298.html
	* @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
	* @history---------------------------------------------
	* see CHANGELOG
	*/
	
	$LANG['LICENSE_OK']			= "If you are reading this the validation is complete and the license is OK.";
	$LANG['LICENSE_FAILED']		= "LICENSE IS NOT VALID!!";

	# to test a security hack uncomment the script below, these are just a few
	# examples of what someone trying to reverse engineer the license key could
	# do. and if the make_secure function is not called they could easily know
	# how to if they knew enough about this class and php.
	
	# TEST 1 -------
	# note how the hash_key1 that is used to encrypt the license key no longer
	# exists thus helping eliminate reverse engineering of the key. It's not just
	# the hash keys, but also every other value.
	
	#echo "The License Key HASH KEY is : ".(empty($application->HASH_KEY1) ? '<i>empty</i>' : $application->HASH_KEY1).'<br />';
	#echo "The License Key ALGORITHM is : ".(empty($application->ALGORITHM) ? '<i>empty</i>' : $application->ALGORITHM).'<br />';
	#echo "The License Key ID is : ".(empty($application->ID1) ? '<i>empty</i>' : $application->ID1).'<br />';
	
	# TEST 2 -------
	# attempts to use a restricted function 
	# declaring the obj, notice how it terminates the script
	
	#trace_r($application->_unwrap_license(file_get_contents('license.generated.dat'), 'KEY'));
	
	# TEST 3 -------
	# this test attempts to recreate a new key by loading the generate class and 
	# in declaring the obj, notice how it terminates the script
	
	#$hack_app = new license_application('license.generated.dat', false, true, true, true);
	
	# TEST 4 -------
	# this test attempts to recreate a the main padl class 
	# in declaring the obj, notice how it terminates the script
	
	#$hack_app = new padl();


?>