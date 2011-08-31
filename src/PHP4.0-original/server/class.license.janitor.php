<?php

	/**
	* Project:		Distrubution License Class
	* File:			class.license.maintenance.php
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
	* v0.1   : 15.06.2005 : Created
	*/

	class license_janitor extends license_server {
		
		/**
		* Constructor
		*
		* @access public 
		* @param $use_mcrypt boolean Determines if mcrypt encryption is used or not (defaults to true, 
		*					 however if mcrypt is not available, it is set to false) 
		* @param $use_time boolean Sets if time binding should be used in the key (defaults to true) 
		* @param $use_server boolean Sets if server binding should be used in the key (defaults to true) 
		* @param $allow_local boolean Sets if server binding is in use then localhost servers are valid (defaults to false) 
		**/
		function license_janitor($use_mcrypt=true, $use_time=true, $use_server=true, $allow_local=false, $approval_type=false)
		{
			# init the class
			$this->init($use_mcrypt, $use_time, $use_server, $allow_local);
		}
		
		/**
		* insert_license
		*
		* inserts a new license into the db
		*
		* @access public 
		* @param $data array The data corresponding the licenses table fields
		*					 ie $data['LICENSE_KEY'] = 'asdasdadsad';
		*						$data['START_DATE']  = 1115916981; etc
		* @param $num number The number of licenses to be created.
  		* @return array 
		**/
		function insert_license($data, $num=1, $status=0)
		{
			# if STATUS is not included in the data array, mark is as inactive
			$data = array_merge($data, array('STATUS'=>$status));
			$result = array();
			$ok = true;
			# loop through and create the licenses
			for($i=0; $i<$num; $i++)
			{
				# run the query
				$query_result = $this->_query('licenses', 'insert', $data);
				$result['RESULTS'][$query_result] = ($query_result);
				if(!$query_result['RESULT']) $ok = false;
			}
			$result['RESULT'] = $ok;
			return $result;
		}
		
		/**
		* _process_id
		*
		* returns an array of db_ids to be used via the various functions
		*
		* @access private 
		* @param $id string If 'ALL' then all the id's in the db are returned
		* @param $id number Single number of id
		* @param $id array An array of db entry id's
		* @param $table string The db table to process
		* @param $id_var string The id var to look for when using ALL in $id
  		* @return array 
		**/
		function _process_id($id, $table, $id_var)
		{
			# if all ids are to be changed then get them
			if($id == 'ALL')
			{
				unset($id);
				$ids = $this->_query($table, 'return', array('RETURN'=>$id_var));
				return $ids['RESULT'];
			}
			# check id is not an array and process single update
			if(!is_array($id))
			{
				return array($id);
			}
			# is already and array so return it
			return $id;
		}
		
		/**
		* _update
		*
		* generic update function
		*
		* @access private 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $table string DB Table to update
		* @param $id_var string Table id column name
		* @param $field string The new status number
		* @param $new_var mixed The new field value
  		* @return array 
		**/
		function _update($id, $table, $id_var, $field, $new_var)
		{
			# process id 
			$ids = $this->_process_id($id, $table, $id_var);
			$result = array();
			$ok = true;
			# loop through the id's updating them
			foreach($ids as $key=>$id)
			{
				# run the query
				$query_result = $this->_query($table, 'update', array('SET'=>array(array('FIELD'=>$field, 'VALUE'=>$new_var)), 'PAIRS'=>array(array('FIELD'=>$id_var, 'VALUE'=>$id))));
				$result['RESULTS'][$id] = ($query_result['RESULT']) ? true : $query_result;
				if(!$query_result['RESULT']) $ok = false;
			}
			$result['RESULT'] = $ok;
			return $result;
		}
		
		/**
		* update_license_status
		*
		* updates a licenses statuses
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $new_status number The new status number
		*					 		1 	- OK
		*					 		0 	- INACTIVE
		*					 		-1 	- WARNING
		*					 		-2 	- SUSOENDED
		*					 		-3 	- REVOKED
  		* @return array 
		**/
		function update_license_status($id, $new_status=1)
		{
			# check for a valid status entry
			if($new_status > 1 && $new_status < -3) return array('RESULT'=>false, 'ERROR'=>$new_status.' is not a valid status');
			# run the generic update func
			return $this->_update($id, 'licenses', 'LICENSE_ID', 'STATUS', $new_status);
		}
		
		/**
		* update_license_flag
		*
		* updates a licenses paid variable
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $flag boolean If the license requires flag, then true
  		* @return array 
		**/
		function update_license_flag($id, $flag=true)
		{
			# check for a valid flag entry
			if(is_bool($flag) && $flag !== 0 && $flag !== 1) return array('RESULT'=>false, 'ERROR'=>$flag.' is not a valid flag. A flag must be a boolean value.');
			# run the generic update func
			return $this->_update($id, 'licenses', 'LICENSE_ID', 'FLAGGED', $flag);
		}
		
		/**
		* update_license_note
		*
		* updates / clears a licenses notes
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $note boolean If false, then no notes are added, true has no effect
		* @param $note string A string to add to the end of the notes
		* @param $note string If 'CLEAR' then the notes are purged from the license
		* @param $sep string The seperator of each additional note
  		* @return array 
		**/
		function update_license_note($id, $note=false, $sep="\r\n")
		{
			# return if no effects are to be taken
			if($note === false) return array('RESULT'=>true);
			# process id 
			$ids = $this->_process_id($id, $table, $id_var);
			$result = array();
			$ok = true;
			# loop through the id's updating them
			foreach($ids as $key=>$id)
			{
				# create the entry
				if($note == 'CLEAR') $entry = '';
				else 
				{
					$existing_note = $this->_query('licenses', 'return', array('RETURN'=>'NOTES', 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
					$entry = empty($existing_note['RESULT'][0]) ? $note : $existing_note['RESULT'][0] . $sep . $note;
				}
				# run the query
				$query_result = $this->_query('licenses', 'update', array('SET'=>array(array('FIELD'=>'NOTES', 'VALUE'=>$entry)), 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
				$result['RESULTS'][$id] = ($query_result['RESULT']) ? true : $query_result;
				if(!$query_result['RESULT']) $ok = false;
			}
			$result['RESULT'] = $ok;
			return $result;
		}
		
		/**
		* update_license_paid
		*
		* updates a licenses paid variable
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $new_cost boolean If the license has been paid for, then true
  		* @return array 
		**/
		function update_license_paid($id, $paid=true)
		{
			# run the generic update func
			return $this->_update($id, 'licenses', 'LICENSE_ID', 'PAID', $paid);
		}
		
		/**
		* update_license_cost
		*
		* updates a licenses cost
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $new_cost number The new cost of the license
  		* @return array 
		**/
		function update_license_cost($id, $new_cost)
		{
			# run the generic update func
			return $this->_update($id, 'licenses', 'LICENSE_ID', 'COST', $new_cost);
		}
		
		/**
		* update_license_next_payment_date
		*
		* updates a licenses next payment date
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $next_payment_date number The new date in time() mode for the next payment
		* @param $next_payment_date number The timespan till the next date in secs
		* @param $start_time string If $next_payment_date is a timespan this says which date to use as a start date
		*		 DB		- uses the current db entry for PAYMENT_NEXT_DATE starting point
		*		 TIME	- uses the time() for PAYMENT_NEXT_DATE starting point
		* @param $start_time number If $next_payment_date is a timespan and this is a number, this is treated as the starting point
  		* @return array 
		**/
		function update_license_next_payment_date($id, $next_payment_date, $start_time='DB')
		{
			# process id 
			$ids = $this->_process_id($id, 'licenses', 'LICENSE_ID');
			# note can't use generic func
			$result = array();
			$ok = true;
			# loop through the id's updating them
			foreach($ids as $key=>$id)
			{
				# if the next payment date is less than the current time then it
				# is treated as  a time span and not a new date
				if($next_payment_date < time())
				{
					# get the current next payment date from the db
					if($start_time=='DB') $date = $this->_query('licenses', 'return', array('RETURN'=>'PAYMENT_NEXT_DUE', 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
					# get the current time
					else if($start_time=='TIME') $date = time();
					# use the number as the start time
					else if(is_int($start_time)) $date = $start_time;
					# add them together to get the new date
					$next_payment_date = $date + $next_payment_date;
				}
				# run the query
				$query_result = $this->_query('licenses', 'update', array('SET'=>array(array('FIELD'=>'PAYMENT_NEXT_DUE', 'VALUE'=>$next_payment_date)), 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
				$result['RESULTS'][$id] = ($query_result['RESULT']) ? $next_payment_date : $query_result;
				if(!$query_result['RESULT']) $ok = false;
			}
			$result['RESULT'] = $ok;
			return $result;
		}
		
		/**
		* update_license_date
		*
		* updates a licenses dates
		*
		* @access public 
		* @param $id number The license Id to update
		* @param $id array The license ids to be changed ie array(3, 5, 6, 10, 19, 215);
		* @param $id string 'ALL' set all the licenses in the server
		* @param $span number The new date span of the license in seconds
		* @param $new_start boolean If false then the existing start date is used, 
		*		 if true the current date given by time() is used
		* @param $new_start number If the number is less than time() then 
		*		 time()+number is used as the new start date, otherwise the number 
		*		 is the new start date
  		* @return array 
		**/
		function update_license_date($id, $span, $new_start=false)
		{
			if(!is_int($span)) return array('RESULT'=>false, 'ERROR'=>'The span date has to be a integer.');
			# process id 
			$ids = $this->_process_id($id, 'licenses', 'LICENSE_ID');
			# note can't use generic func
			$result = array();
			$ok = true;
			# loop through the id's updating them
			foreach($ids as $key=>$id)
			{
				# get the existing dates for the license
				$dates = $this->_query('licenses', 'return', array('RETURN'=>'START_DATE', 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
				$start = $dates['RESULT'][0]['START_DATE'];
				# check if a new start date is needed
				if($new_start !== false)
				{
					# switch through the cases
					if(is_bool($new_start)) $start = time();
					else if($new_start < time()) $start = time() + $new_start;
					else $start = $new_start;
				}
				# get the new end date
				$end = $start + $span;
				$query_result = $this->_query('licenses', 'update', array('SET'=>array(array('FIELD'=>'START_DATE', 'VALUE'=>$start), array('FIELD'=>'DATE_SPAN', 'VALUE'=>$span), array('FIELD'=>'EXPIRY_DATE', 'VALUE'=>$end)), 'PAIRS'=>array(array('FIELD'=>'LICENSE_ID', 'VALUE'=>$id))));
				$result['RESULTS'][$id] = ($query_result['RESULT']) ? $end : $query_result;
				if(!$query_result['RESULT']) $ok = false;
			}
			$result['RESULT'] = $ok;
			return $result;
		}
		
	}
	
?>