<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.callhome.php
	* File type: 	DEMO
	* Notes:		This file is a demonstration file that illustrates how the
	*				call on the clients server can be made to your server
	*				validating the clients license. As it validates from to
	*				my server it requires a live internet connection to function
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

	# copy the server vars (important for security, see note below)
	$server_array = $_SERVER;

	echo '<link rel="stylesheet" type="text/css" href="../shared/styles.css">';
	echo '<body>'."\r\n";
	echo '<span class="header">'."\r\n";
	echo '<b>########################################################################</b><br />'."\r\n";
	echo '<b>Dial Home Demostration</b><br />'."\r\n";
	echo 'This file is a demonstration file that illustrates how to perform a dial home check<br />'."\r\n";
	echo '<span style="font-size: 10;">'."\r\n";
	echo '<b>Oliver Lillie, buggedcom [publicmail at buggedcom dot co dot uk]<br />'."\r\n";
	echo '<a href="http://www.buggedcom.co.uk/" target="_blank" style="color: #F00">http://www.buggedcom.co.uk/</a></b><br />'."\r\n";
	echo '</span>'."\r\n";
	echo '<b>########################################################################</b><br /><br />'."\r\n";
	echo '</span>'."\r\n";

	# import the classes
	include_once('../shared/class.license.lib.php');
	include_once('class.license.app.php');

	# initialise the class with mcrypt off to maximise compatability with servers
	$application = new license_application('license.supplied.dat', false, true, false, true);
	
	# set the server vars
	# note this doesn't have to be set, however if not all of your app files are encoded
	# then there would be a possibility that the end user could modify the server vars
	# to fit the key thus making it possible to use your app on any domain
	# you should copy your server vars in the first line of your active script so you can
	# use the unmodified copy of the vars
	$application->set_server_vars($server_array);

	# the key to be validated
	# the key below contains the id used in the home server db. it was created like so
	# $application->generate(array('_LICENSE_ID'=>1));
	# validate the data from the key
	$set_data = $application->validate(false, true, 'www.buggedcom.co.uk', 'http://www.buggedcom.co.uk/distributionlicense/files/server/demo.receive.license.php');
	
	echo '<b>########################################################################</b><br />'."\r\n";
	echo '<b>Set License Key Validation Example</b><br />'."\r\n";
	echo 'The set key is validated to an id in my db on my server. It dials home to...<br />'."\r\n";
	echo '<b>########################################################################</b><br /><br />'."\r\n";
	echo '<b><span style="color: #F00">Supplied License is : '.$set_data['RESULT'].'</span></b><br />'."\r\n";
	if($set_data['RESULT'] == 'OK')
	{
		echo '<span style="font-size: 10;color: #F00;">'."\r\n";
		echo 'Key is valid untill : '.$set_data['DATE']['HUMAN']['END'];
		echo '</span><br />'."\r\n";
	}
	echo '<br /><b>License Key</b><br />'."\r\n";
	echo '<pre>'. file_get_contents($application->_LICENSE_PATH) .'</pre><br />'."\r\n";
	echo '<b>License Key Contents</b><br />'."\r\n";
	echo '<pre>';
	# validate the key from my server
	print_r($set_data);
	echo '</pre><br />'."\r\n";
	echo '</span>'."\r\n";

	# make the application secure by running this function
	# it also prevents any future reincarnations of the class calling any of the 
	# key generation and validation functions, it also deletes any class variables
	# that may be set.
	$application->make_secure();

	# delete the $application object
	unset($application);

	echo '</body>'."\r\n";

?>