<?php

	/**
	* Project:		Distrubution License Class
	* File:			demo.app.lang.license.php
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
	
	$LANG['LICENSE_OK']				= "The License Key supplied with this file is valid. If this application was not a demonstration the end user would not see this display, your application could then run as normal.";
	$LANG['LICENSE_TMINUS']			= "The License Key supplied you are using with this application has not yet entered its valid period. The License Key is valid from <b>{[DATE_START]}</b> to <b>{[DATE_END]}</b>.";
	$LANG['LICENSE_EXPIRED']		= "The License Key supplied you are using with this application has expired and is no longer valid. The License Key was valid from <b>{[DATE_START]}</b> to <b>{[DATE_END]}</b>.";
	$LANG['LICENSE_ILLEGAL']		= "The License Key is not valid for this server. This means that you cannot make further use of this application untill you purchase a valid key. HOWEVER, if you have you have purchased a valid key and you get this message in error, please contact the applications reseller.";
	$LANG['LICENSE_ILLEGAL_LOCAL']	= "This application can not be run on the localhost. The application can only be run under a valid domain.";
	$LANG['LICENSE_INVALID']		= "The License Key is invalid. This means that your License Key file has become corrupted. Please replace the file 'license.supplied.dat' with a copy of the original license. If you do not still have a copy of the original license please contact the applications reseller.<br /><br />HOWEVER, you should note that this might be because the key was generated using mcrypt, where as your PHP install does not have access to the Mcrypt library (this is very unlikley).";
	$LANG['LICENSE_EMPTY']			= "The License Key is empty. Please make sure that the file 'license.supplied.dat' is writeable by the web server, then copy and paste the key into the box below and click submit.<br /><br /><form action='' method='POST'><textarea id='key' name='key' rows='7' cols='60'></textarea><br /><br /><input type='submit' id='submit' name='submit' value='Submit'></form>";

	$LANG['WRITE_ERROR']			= "<span class='warning'>The License Key was unable to be written! Please make sure that license.supplied.dat is writeable by the web server.</span><br /><br />";

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
	# this test attempts to recreate a new key by loading the generate class and 
	# in declaring the obj, notice how it terminates the script
	
	#$hack_app = new license_application('license.generated.dat', false, true, true, true);
	
	# TEST 3 -------
	# this test attempts to recreate a the main padl class 
	# in declaring the obj, notice how it terminates the script
	
	#$hack_app = new padl();


?>