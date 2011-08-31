<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.receive.php
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
	* @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
	* @version 0.2
	* @history---------------------------------------------
	* see CHANGELOG
	*/
	
	if(empty($_POST))
	{
		echo '<link rel="stylesheet" type="text/css" href="../shared/styles.css">';
		echo '<body>'."\r\n";
		echo '<span class="header">'."\r\n";
		echo '<b>########################################################################</b><br />'."\r\n";
		echo '<b>Recieve Call Demonstration File</b><br />'."\r\n";
		echo 'This file validates a call from a remote client server and validates the lisence key from the<br />'."\r\n";
		echo 'client. It thens returns the result of the database check and comparison to the client server.<br />'."\r\n";
		echo 'This file is only used from a remote socket opening.<br />'."\r\n";
		echo '<span style="font-size: 10;">'."\r\n";
		echo '<b>Oliver Lillie, buggedcom [publicmail at buggedcom dot co dot uk]<br />'."\r\n";
		echo '<a href="http://www.buggedcom.co.uk/" target="_blank" style="color: #F00">http://www.buggedcom.co.uk/</a></b><br />'."\r\n";
		echo '</span>'."\r\n";
		echo '<b>########################################################################</b><br /><br />'."\r\n";
		echo '</span>'."\r\n";
		echo '<body>'."\r\n";
	}
	else
	{
		# import db cofigs
		include_once('db.config.php');
	
		# import the classes
		include_once('../shared/class.license.lib.php');
		include_once('class.license.server.php');
	
		# initialise the class using neither server or time checking and set the use
		# of mcrypt according to the recieved data
		$server = new license_server($_POST['MCRYPT'], false, true, true);
	
		# recieve the call from client server
		# and return the results to the client server by echoing back the 
		# returned data to the socket call
		die($server->recieve_call($_POST['POSTDATA'], $table, $user, $pass));
	}

?>