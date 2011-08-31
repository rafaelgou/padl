<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.generate.php
	* File type: 	DEMO
	* Notes:		This file is a demonstration file that illustrates various
	*				ways to generate and check a license key
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
	* @version 0.1
	* @history---------------------------------------------
	* see CHANGELOG
	*/
	
	$file = 'license.generated.dat';
	
	if(isset($_GET['WIPE']))
	{
		$h = fopen($file, 'a');
		ftruncate($h, 0);
		fclose($h);
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}

	# copy the server vars (important for security, see note below)
	$server_array = $_SERVER;

	echo '<link rel="stylesheet" type="text/css" href="../shared/styles.css">';
	echo '<body>'."\r\n";
	echo '<span class="header">'."\r\n";
	echo '<b>########################################################################</b><br />'."\r\n";
	echo '<b>On Server Generation Demonstration</b><br />'."\r\n";
	echo 'This file is a demonstration file that illustrates how to generate a key file on the client server<br />'."\r\n";
	echo '<span style="font-size: 10;">'."\r\n";
	echo '<b>Oliver Lillie, buggedcom [publicmail at buggedcom dot co dot uk]<br />'."\r\n";
	echo '<a href="http://www.buggedcom.co.uk/" target="_blank" style="color: #F00">http://www.buggedcom.co.uk/</a></b><br />'."\r\n";
	echo '</span>'."\r\n";
	echo '<b>########################################################################</b><br /><br />'."\r\n";
	echo '</span>'."\r\n";

	# import the classes
	include_once('../shared/class.license.lib.php');
	include_once('class.license.app.php');
	include_once('class.license.gen.php');

	# initialise the class
	# note for this demonstration script we will turn off mcrypt usage
	# as some systems do not have it installed in their setup.
	# the initial argument usually defaults to true (use mcrypt)
	$architect = new license_architect($file, false, true, true, true);
	
	# set the server vars
	# note this doesn't have to be set, however if not all of your app files are encoded
	# then there would be a possibility that the end user could modify the server vars
	# to fit the key thus making it possible to use your app on any domain
	# you should copy your server vars in the first line of your active script so you can
	# use the unmodified copy of the vars
	$architect->set_server_vars($server_array);

	# generate a key with your server details
	$gen_key 	= $architect->generate('localhost', 0, 606024752);
	# $gen_key 	= $architect->generate('buggedcom.co.uk', 0, 606024752);
	
	$key 		= ($gen_key == 'KEY_EXISTS' || $gen_key == 'WRITE_TARGET_404' || $gen_key == 'WRITE_TARGET_UNWRITEABLE' || $gen_key == 'WRITE_FAILED' || $gen_key == 'DOMAIN_IP_FAIL' || $gen_key == 'SERVER_FAIL') ? false : $gen_key;
	# validate the generated key and get the data
	$gen_data 	= $architect->validate($key);

	echo '<b>########################################################################</b><br />'."\r\n";
	echo '<b>Server Generated License Key Example</b><br />'."\r\n";
	echo 'Generate a key with your server details, should run and produce OK as the result value<br />'."\r\n";
	echo '<b>########################################################################</b><br /><br />'."\r\n";
	echo '<span style="color: #F00"><b>Generated License is : '.$gen_data['RESULT'].'</b><br />';
	if($gen_data['RESULT'] == 'OK')
	{
		echo '<span style="font-size: 10;">'."\r\n";
		echo 'Key is valid untill : '.$gen_data['DATE']['HUMAN']['END'];
		echo '</span>'."\r\n";
	}
	echo '</span><br /><br />'."\r\n";
	echo '<b>Generated License Key</b><br />'."\r\n";
	echo '<pre>'.$gen_key.'</pre><br />'."\r\n";
	if($gen_key == 'DOMAIN_IP_FAIL')
	{
		echo '<span style="color: #F00;font-size: 10;">'."\r\n";
		echo 'If key fails to be generated with \'DOMAIN_IP_FAIL\' it means that the domain supplied to the <br />'."\r\n";
		echo '$architect->generate function on line #81 has not been changed to your domain.';
		echo '</span><br /><br />'."\r\n";
	}
	if($gen_key == 'KEY_EXISTS')
	{
		echo '<span style="color: #F00;font-size: 10;">'."\r\n";
		echo 'The key has already been written hence, \'KEY_EXISTS\', the written key will still be<br />'."\r\n";
		echo 'validated and data outputed below. Note how the end date is not changing on each refresh <br /><br />'."\r\n";
		echo 'To wipe the generated key click <a href="?WIPE=1" style="color: #F00">here</a><br />'."\r\n";
		echo '</span><br /><br />'."\r\n";
	}
	echo '<b>Generated License Key Contents</b><br />'."\r\n";
	echo '<pre>';
	# validate the key from your server
	print_r($gen_data);
	echo '</pre><br />'."\r\n";

	# make the application secure by running this function
	# it also prevents any future reincarnations of the class calling any of the 
	# key generation and validation functions, it also deletes any class variables
	# that may be set.
	$architect->make_secure();

	# delete the $architect object
	unset($architect);

	echo '</body>'."\r\n";

?>