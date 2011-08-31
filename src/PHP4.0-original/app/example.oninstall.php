<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.oninstall.php
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

	$file = 'license.oninstall.dat';
	
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
	echo '<b>Generation Demonstration</b><br />'."\r\n";
	echo 'This file demonstrates the generation of a key on install of the application. It registers the <br />'."\r\n";
	echo 'install with the license server. It then generates a temporary license key that is valid for two days.<br />'."\r\n";
	echo 'NOTE: This connects to a demo license server that resides on my server. It is set to allow<br />'."\r\n";
	echo 'any amount of install requests from the same ip and mac address, this can however be limited<br />'."\r\n";
	echo 'using, ip addresses, domains, mac addresses.<br />'."\r\n";
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
	
	# add some data vars
	# remember that the reserver variable names in the data is _PHP_OS and _PHP_VERSION
	$data = array();
	$data['MAX_UPLOADS'] 		= 10;
	$data['WATERMARK_IMAGES'] 	= true;
	$data['COPYRIGHT_TEXT']		= 'This project is copyrighted to FooBar Limited 2005.';

	# work out secs in two weeks
	# NOTE: an important thing to remember for this example. Although we are
	# asking for a  2 week license, the approval value set on server is 2 days
	# Even though the license returned is temporary if it returned the actual key
	# it would be capped by the approval time value to two days.
	$span = 60*60*24*7*2;
	# $span = 'NEVER';

	# generate the install request
	$results = $architect->register_install('localhost', 0, $span, $data, 'www.buggedcom.co.uk', 'http://www.buggedcom.co.uk/distributionlicense/files/server/demo.register.license.php');

	echo '<span style="font-size: 10;">'."\r\n";
	echo '</span>'."\r\n";
	echo '<b>########################################################################</b><br />'."\r\n";
	echo '<b>On Install</b><br />'."\r\n";
	echo 'The server details are evaluated in this script and if they meet the requirements the data<br />'."\r\n";
	echo 'is sent to my server and makes checks against the demo license server. If the request is<br />'."\r\n";
	echo 'approved. This file generates a temporary key to <a href="license.oninstall.dat" target="_blank" class="bodylink">license.oninstall.dat</a> license file<br />'."\r\n";
	echo '<b>########################################################################</b><br /><br />'."\r\n";
	echo '<span style="color: #F00"><b>Result of the License Request is : '.$results['RESULT'].'</b></span><br />';

	switch($results['RESULT'])
	{
		# this example will never return ok because my server is set to ADMIN
		# and returns temp keys valid for two days
		case 'OK' :			
		# if everything went ok for this example then PENDING is the result
		# it means that a temp key has been returned
		case 'PENDING' :
		
			$key = $architect->validate($results['KEY']);

			echo '<span style="font-size: 10;color: #F00;">'."\r\n";
			echo 'Key is valid untill : '.$key['DATE']['HUMAN']['END'];
			echo '</span><br /><br />'."\r\n";
			
			if($architect->writeKey($results['KEY']))
			{
				echo '<span style="color: #F00"><b>License Key Written to <a href="license.oninstall.dat" target="_blank" style="color: #F00;">license.oninstall.dat</a></b></span><br /><br />';
			}
			else
			{
			}
			
			echo '<b>Requested License Key</b><br />'."\r\n";
			echo '<pre>'.$results['KEY'].'</pre><br />'."\r\n";
			echo '<b>Requested License Key Contents</b><br />'."\r\n";
			echo '<pre>';
			print_r($key);
			echo '</pre><br />'."\r\n";		
			
			break;
			
		# means something went wrong transfering the data and no data was recieved by the server
		case 'EMPTY_DATA' :
			echo '<br /><span style="color: #F00"><b>WARNING : No request data was received by the license server.</b></span><br /><br />';
			break;
		# the socket failed to be opened and no data could be sent
		case 'SOCKET_FAILED' :
			echo '<br /><span style="color: #F00"><b>WARNING : It was not possible to open a socket to connect to the license server.</b></span><br /><br />';
			break;
		# the domain supplied by the registering client didn't match the collected ip addresses
		# thus the request failed
		case 'DOMAIN_IP_FAIL' :
			echo '<br /><span style="color: #F00"><b>WARNING : The domain supplied by this script did not match the corresponding IP Address.<br />';
			echo 'Please edit the address on line #83 to the address you are running this script on.</b></span><br /><br />';
			break;
		# the number of required $_SERVER vars were not met and thus failed the request
		case 'SERVER_FAIL' :
			echo '<br /><span style="color: #F00"><b>WARNING : The number of $_SERVER vars supplied by your server did not meet the required number.</b></span><br /><br />';
			break;
		# either the key has already been written or a temp key has been written
		case 'KEY_EXISTS' :
			echo '<br /><span style="color: #F00"><b>WARNING : The key has already been written.</b></span><br />';
			echo '<span style="color: #F00;font-size: 10;">'."\r\n";
			echo 'To wipe the generated key click <a href="?WIPE=1" style="color: #F00">here</a><br />'."\r\n";
			echo '</span><br /><br />'."\r\n";
			break;
		case 'SERVER_DB_FAILED' :
			echo '<br /><span style="color: #F00"><b>WARNING : The server failed to store the license request, as a result the request failed.<br />';
			echo 'The mysql error was as follows :</b><br /><br />';
			echo wordwrap($results['ERROR'], 130, '<br />').'</b></span>';
	}
	
	# make the application secure by running this function
	# it also prevents any future reincarnations of the class calling any of the 
	# key generation and validation functions, it also deletes any class variables
	# that may be set.
	$architect->make_secure();

	# delete the $architect object
	unset($architect);

	echo '</body>'."\r\n";

?>