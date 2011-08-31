<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.security.php
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
	
	# IMPORTANT, THIS DEMO USES THE license.generated.dat FILE THAT IS GENERATED
	# BY THE demo.generate.license.php SCRIPT. PLEASE RUN THAT FILE BEOFRE THIS

	# this file demonstrates several important security techniques
	# that should be employed within your app's license validation
	# methods
	
	# 1) -----------------------------------------------------------------------
	# you should always encode your applications using such a product as ionocube, 
	# or source guardian.
	
	# 2) -----------------------------------------------------------------------
	# always copy the $_SERVER vars before including any public files (by 
	# public i mean any files that the client has access to, ie lang files or 
	# opensource classes). Ideally the license validation script should be carried
	# out before any imporation of language files, however i know that is not 
	# always possible so hence this work around.
	$server_array = $_SERVER;
	
	# 3) -----------------------------------------------------------------------
	# you should always check that the classes exist, otherwise terminate the script 
	if(@!file_exists('../shared/class.license.lib.php') || @!is_file('../shared/class.license.lib.php')) die('Missing File > ../shared/class.license.lib.php');
	if(@!file_exists('class.license.app.php') || @!is_file('class.license.app.php')) die('Missing File > class.license.app.php');
	
	# import the classes
	include_once('../shared/class.license.lib.php');
	include_once('class.license.app.php');
	
	# 4) -----------------------------------------------------------------------
	# check to see if the classes exist
	if(!class_exists('padl') || !class_exists('license_application')) die('Class code missing.');

	# initialise the class
	$application = new license_application('license.generated.dat', false, true, true, true);

	# set the server vars
	$application->set_server_vars($server_array);
	
	# validate the license key
	$results 	= $application->validate();

	# 5) -----------------------------------------------------------------------
	# once the validation has been carried out there should be no need to run
	# any other scripts. thus by running make_secure() you are wiping all the
	# values from the class and defining a var that terminates the script if
	# any important functions are called or a new instance of the class is 
	# redeclared or inited.
	# by setting the param to true you can report any violations of this to your
	# licensing server. Note the violations detected are only function accesses
	# or redeclarations of the classes, not
	$application->make_secure(true);
	
	# inclusion of lang file for outputing errors and other text.
	# you should look inside the lang file as it contains some security tests you
	# can run to see how make_secure function works, this must always be below
	# the make_secure function call, otherwise there are major security 
	# implications in the strength of your key
	include_once('demo.security.lang.php');	

	# 6) -----------------------------------------------------------------------
	# switch through the results of the validation. This is the main one. If you
	# don't check the validation results, what is the point of using this class!
	switch($results['RESULT'])
	{
		case 'OK' :
			echo $LANG['LICENSE_OK'];
			break;
		default :
			die($LANG['LICENSE_FAILED']);
	}
	
	# 5) -----------------------------------------------------------------------
	# this is really just a tidyup but is worth it, delete the active padl object
	unset($application);
	
	# this would be where you would start including public files in an optimum situation
	# include_once('demo.security.lang.php');
	
?>