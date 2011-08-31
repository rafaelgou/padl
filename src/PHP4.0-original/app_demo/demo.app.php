<?php

	/**
	* Project:		Distrubution License Class
	* File:			demo.app.license.php
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
	
	# if the key has been posted please write the file
	if(isset($_POST['key'])) 
	{
		$h = @fopen('license.supplied.dat', 'w');
		if(@fwrite($h, $_POST['key']) === false)
		{
			define('write_error', true);
			@fclose($h);
		}
		else 
		{
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
		
	}

	# copy the server vars (important for security, see note below)
	$server_array = $_SERVER;

	# import the classes
	include_once('../shared/class.license.lib.php');
	include_once('../app/class.license.app.php');

	# initialise the class
	# note for this demonstration script we will turn off mcrypt usage
	# as some systems do not have it installed in their setup.
	# the initial argument usually defaults to true (use mcrypt)
	$application = new license_application('license.dat', false, true, false, true);
	
	# set the server vars
	# note this doesn't have to be set, however if not all of your app files are encoded
	# then there would be a possibility that the end user could modify the server vars
	# to fit the key thus making it possible to use your app on any domain
	# you should copy your server vars in the first line of your active script so you can
	# use the unmodified copy of the vars
	$application->set_server_vars($server_array);

	# the set key is the key validated for my server, when run on your box it will be illegal
	$results 	= $application->validate();

	# make the application secure by running this function
	# it also prevents any future reincarnations of the class calling any of the 
	# key generation and validation functions, it also deletes any class variables
	# that may be set.
	$application->make_secure();

	# delete the $application object
	# unset($application);
	
	# import the language
	include_once('demo.app.lang.php');
	
	# switch through the results
	switch($results['RESULT'])
	{
		case 'OK' :
			$result 		= 'ok';
			$message 		= $LANG['LICENSE_OK'];
			break;
		case 'TMINUS' :
			$result 		= 'tminus';
			$message 		= str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $LANG['LICENSE_TMINUS']);
			break;
		case 'EXPIRED' :
			$result 		= 'expired';
			$message 		= str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $LANG['LICENSE_EXPIRED']);
			break;
		case 'ILLEGAL' :
			$result 		= 'illegal';
			$message 		= $LANG['LICENSE_ILLEGAL'];
			break;
		case 'ILLEGAL_LOCAL' :
			$result 		= 'illegal';
			$message 		= $LANG['LICENSE_ILLEGAL_LOCAL'];
			break;
		case 'INVALID' :
			$result 		= 'invalid';
			$message 		= $LANG['LICENSE_INVALID'];
			break;
		case 'EMPTY' :
			$result 		= 'empty';
			$message 		= $LANG['LICENSE_EMPTY'];
			if(defined('write_error')) $message = $LANG['WRITE_ERROR'].$message;
			break;
		default :
			break;
	}

	# template base64'd
	$template = "PGhlYWQ+DQk8bGluayByZWw9InN0eWxlc2hlZXQiIHR5cGU9InRleHQvY3NzIiBocmVmPSIuLi9zaGFyZWQvc3R5bGVzLmNzcyIgLz4NPC9oZWFkPg08Ym9keSBsZWZ0bWFyZ2luPSIwIiB0b3BtYXJnaW49IjAiAAA+DTx0YWJsZSB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBib3JkZXI9IjAiIGNlbGxwYWRkaW5nPSIwIiBjZWxsc3BhY2luZz0iMCIgYWxpZ249ImNlbnRlciI+DQkJPHRyIGFsaWduPSJjZW50ZXIiIHZhbGlnbj0ibWlkZGxlIj4NCQkJPHRkIGFsaWduPSJjZW50ZXIiIHZhbGlnbj0ibWlkZGxlIj4NCQkJCTxkaXYgYWxpZ249ImNlbnRlciI+DQkJCQkJPHRhYmxlIHdpZHRoPSI2MDAiIGJvcmRlcj0iMCIgY2VsbHBhZGRpbmc9IjAiIGNlbGxzcGFjaW5nPSIwIj4NCQkJCQkJPHRyPg0JCQkJCQkJPHRkIGhlaWdodD0iMjMiIGNvbHNwYW49IjUiIHZhbGlnbj0idG9wIj4NCQkJCQkJCQk8aW1nIHNyYz0icmVzdWx0c190b3AuZ2lmIiB3aWR0aD0iNjAwIiBoZWlnaHQ9IjIzIiAvPg0JCQkJCQkJPC90ZD4NCQkJCQkJPC90cj4NCQkJCQkJPHRyPg0JCQkJCQkJPHRkIHdpZHRoPSIyMiIgaGVpZ2h0PSI1MyIgdmFsaWduPSJ0b3AiPg0JCQkJCQkJCTxpbWcgc3JjPSJyZXN1bHRzX2xlZnQuZ2lmIiB3aWR0aD0iMjIiIGhlaWdodD0iNTMiIC8+DQkJCQkJCQk8L3RkPg0JCQkJCQkJPHRkIHdpZHRoPSI1OSIgdmFsaWduPSJ0b3AiPg0JCQkJCQkJCTxpbWcgc3JjPSJyZXN1bHRzX2FsZXJ0LmdpZiIgd2lkdGg9IjU5IiBoZWlnaHQ9IjUzIiAvPg0JCQkJCQkJPC90ZD4NCQkJCQkJCTx0ZCBjb2xzcGFuPSIyIiB2YWxpZ249InRvcCI+DQkJCQkJCQkJPGltZyBzcmM9InJlc3VsdHNfe1tSRVNVTFRdfS5naWYiIHdpZHRoPSI0OTciIGhlaWdodD0iNTMiIC8+DQkJCQkJCQk8L3RkPg0JCQkJCQkJPHRkIHdpZHRoPSIyMiIgdmFsaWduPSJ0b3AiPg0JCQkJCQkJCTxpbWcgc3JjPSJyZXN1bHRzX3JpZ2h0LmdpZiIgd2lkdGg9IjIyIiBoZWlnaHQ9IjUzIiAvPg0JCQkJCQkJPC90ZD4NCQkJCQkJPC90cj4NCQkJCQkJPHRyPg0JCQkJCQkJPHRkIGhlaWdodD0iMjciIHZhbGlnbj0idG9wIiBiYWNrZ3JvdW5kPSJyZXN1bHRzX2xlZnQuZ2lmIj4NCQkJCQkJCQk8aW1nIHNyYz0ic3BhY2VyLmdpZiIgd2lkdGg9IjIyIiBoZWlnaHQ9IjI3IiAvPg0JCQkJCQkJPC90ZD4NCQkJCQkJCTx0ZCBjb2xzcGFuPSIyIiB2YWxpZ249InRvcCI+DQkJCQkJCQkJPGltZyBzcmM9InNwYWNlci5naWYiIHdpZHRoPSIxMCIgaGVpZ2h0PSIyNyIgLz4NCQkJCQkJCTwvdGQ+DQkJCQkJCQk8dGQgd2lkdGg9IjU0MSIgdmFsaWduPSJ0b3AiPg0JCQkJCQkJCTxzcGFuIGNsYXNzPSJtZXNzYWdlIj4NCQkJCQkJCQkJe1tNRVNTQUdFXX0gDQkJCQkJCQkJPC9zcGFuPg0JCQkJCQkJPC90ZD4NCQkJCQkJCTx0ZCB2YWxpZ249InRvcCIgYmFja2dyb3VuZD0icmVzdWx0c19yaWdodC5naWYiPg0JCQkJCQkJCTxpbWcgc3JjPSJzcGFjZXIuZ2lmIiB3aWR0aD0iMjIiIGhlaWdodD0iMjciIC8+DQkJCQkJCQk8L3RkPg0JCQkJCQk8L3RyPg0JCQkJCQk8dHI+DQkJCQkJCQk8dGQgaGVpZ2h0PSIxMDQiIGNvbHNwYW49IjUiIHZhbGlnbj0idG9wIj4NCQkJCQkJCQk8aW1nIHNyYz0icmVzdWx0c19ib3R0b20uZ2lmIiB3aWR0aD0iNjAwIiBoZWlnaHQ9IjEwNCIgLz4NCQkJCQkJCTwvdGQ+DQkJCQkJCTwvdHI+DQkJCQkJPC90YWJsZT4NCQkJCTwvZGl2Pg0JCQk8L3RkPg0JCTwvdHI+DQk8L3RhYmxlPg08L2JvZHk+DQ==";

	# get the template, assign the vars and output
	die(str_replace(array('{[RESULT]}', '{[MESSAGE]}'), array($result, $message), base64_decode($template)));
	
?>