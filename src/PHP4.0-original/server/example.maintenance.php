<?php

	/**
	* Project:		Distrubution License Class
	* File:			example.maintenance.php
	* File type: 	DEMO
	* Notes:		This file is a demonstration file that illustrates how the
	*				a call from a clients server may be recieved and process
	*				a license request
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
	
	# note this demo file is note yet complete, as there are many more functions
	# to add... it will be updated when the janitor class is updated.
	
	# import db cofigs
	include_once('db.config.php');

	# import the classes
	include_once('../shared/class.license.lib.php');
	include_once('class.license.server.php');
	include_once('class.license.janitor.php');
	
	# create the new maintenaince object
	$janitor = new license_janitor();
	
	# connect to the db
	$janitor->connect($table, $user, $pass);
	
	trace_r( $janitor->update_license_date(1, 60*60*24*7*52, true) );
	
	# disconnect the db
	$janitor->disconnect();
	

?>