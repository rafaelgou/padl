<?php

	/**
	* Project:		Distrubution License Class
	* File:			class.license.server.php
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
	
	class license_server extends padl {
	
		/**
		* init the database value.
		*/
		var $_DB;

		/**
		* the database table prefix.
		*
		* @var array
		*/
		var $DB_PREFIX			= '';
		
		/**
		* the default license approval type. 
		*			- ADMIN means the key generation needs admin approval
		*			- AUTO means the key generation is done on request
		*
		* @var string
		*/
		var $APPROVAL_TYPE		= 'ADMIN';
		
		/**
		* init the approval type vars.
		*/
		var $APPROVAL_DATA;
		var $APPROVAL_TIME;

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
		function license_server($use_mcrypt=true, $use_time=true, $use_server=true, $allow_local=false, $approval_type=false)
		{
			# init the class
			$this->init($use_mcrypt, $use_time, $use_server, $allow_local);
			# set the registration approval type
			if(is_string($approval_type))
			{
				$this->set_approval_type($approval_type);
			}
		}
		
		/**
		* set_approval_type
		*
		* sets the approval type
		*
		* @access public 
		* @param $type string The type of license generation
		*		 ADMIN 	- generates a temp key and assigns the $data param into the key.
		*				  the key will be valid for the period set by the validty param.
		*							  NOTE: if validity is set to 0 then a key is not returned to the client
		*		AUTO	- generates the key when one is asked for. the data array is used, however
		*				  the validity period is treated as a cap for the time span sent from the client
		*				  install, thus if the validy is less than the asked for time period it will override
		* @param $data array An array of data to be bound into the generated key
		* @param $validity number If used with ADMIN it is the length of the temporary key generated. if set to 0
		*				  then no temp key is generated, however if used with AUTO it caps the requested licenses
		*				  time span. If no time span is requested by the client and this is not set to 0 then a span
		*				  will be placed into the key. This way you can issue licenses and not worry about the client
		*				  trying to hack the timespan of the license.
  		* @return resource id
		**/
		function set_approval_type($type='ADMIN', $data=array(), $validity=0)
		{
			$this->APPROVAL_TYPE = $type;
			$this->APPROVAL_DATA = $data;
			$this->APPROVAL_TIME = $validity;
		}

		/**
		* connect
		*
		* connects to the mysql db holding the license data
		*
		* @access public 
		* @param $table string Database table that contains the distributionLicense sql
		* @param $user string User for the mysql db
		* @param $pass string Pass for the mysql db
		* @param $host string Host for the mysql db
  		* @return resource id
		**/
		function connect($table, $user, $pass, $host='localhost')
		{
			# connect to your servers DB
			$this->_DB = @mysql_pconnect($host, $user, $pass);
			# select the table required
			@mysql_select_db($table, $this->_DB);
			return $this->_DB;
		}
		
		/**
		* disconnect
		*
		* disconnects the mysql db
		* been validated on the return server
		*
		* @access public 
  		* @return boolean 
		**/
		function disconnect()
		{
			# close the database
			return @mysql_close($this->_DB);
		}
		
		/**
		* _query
		*
		* builds, then excecutes then returns data for mysql queries 
		*
		* @access private 
		* @param $table string The table to build the query for
		* @param $action string The type of action to build the query for
		* @param $data array The data array containing arguments for the query building
  		* @return array 
		**/
		function _query($table, $action, $data=array())
		{
			$query =  $this->_build_query($table, $action, $data);
			return $this->_execute_query($query);
		}
		
		/**
		* _execute_query
		*
		* excecutes a mysql query and returns the results
		*
		* @access private 
		* @param $query string The query string to execute
  		* @return array 
		**/
		function _execute_query($query)
		{
			# determine the query type
			$query_type = strpos($query, 'INSERT')===false ? (strpos($query, 'SELECT')===false ? 'other' : 'return') : 'insert';
			# run the query
			$query_result = @mysql_query($query, $this->_DB);
			if(!$query_result) return array('RESULT'=>false, 'SQL'=>$query, 'ERROR'=>mysql_error());
			# switch through the query types in order to return the correct data
			switch($query_type)
			{
				case 'insert' :
					$result = mysql_insert_id($this->_DB);
					break;
				case 'return' :
					$result = array();
					if(empty($data['FETCHTYPE']))
					{
						while($row = mysql_fetch_assoc($query_result))
						{
							$result[] = $row;
						}
					}
					break;
				default :
					$result = true;
					break;
			}
			return array('RESULT'=>$result);
		}
		
		/**
		* _build_query
		*
		* gets the query for the table and qction
		*
		* @access private 
		* @param $table string The table to build the query for
		* @param $action string The type of action to build the query for
		* @param $data array The data array containing arguments for the query building
  		* @return string 
		**/
		function _build_query($table, $action, $data=array())
		{
			# get the table prefix and all
			$table_pre = '`'.$this->DB_PREFIX.$table.'`';
			# switch through the queries
			# and build them
			switch($action)
			{
				case 'insert' :
					switch($table)
					{
						case 'licenses' : 
							return 'INSERT INTO '.$table_pre.' (`LICENSE_ID`,`USER_ID`,`FLAGGED`,`STATUS`,`START_DATE`,`DATE_SPAN`,`EXPIRY_DATE`,`NOTES`,`COST`,`PAID`,`REQUEST_KEY`,`TEMP_KEY`,`LICENSE_KEY`,`PAYMENT_NEXT_DUE`,`DATA`,`MAC`,`PATH`,`IP`) VALUES ('.$this->_get_value($data['LICENSE_ID']).','.$this->_get_value($data['USER_ID']).','.$this->_get_value($data['FLAGGED']).','.$this->_get_value($data['STATUS']).','.$this->_get_value($data['START_DATE']).','.$this->_get_value($data['DATE_SPAN']).','.$this->_get_value($data['EXPIRY_DATE']).','.$this->_get_value($data['NOTES']).','.$this->_get_value($data['COST']).','.$this->_get_value($data['PAID']).','.$this->_get_value($data['REQUEST_KEY']).','.$this->_get_value($data['TEMP_KEY']).','.$this->_get_value($data['LICENSE_KEY']).','.$this->_get_value($data['PAYMENT_NEXT_DUE']).','.$this->_get_value($data['DATA']).','.$this->_get_value($data['MAC']).','.$this->_get_value($data['PATH']).','.$this->_get_value($data['IP']).');';
						case 'remote_log' :
							return 'INSERT INTO '.$table_pre.' (`ID`,`TIMESTAMP`,`LICENSE_ID`,`MESSAGE`,`SERVER`,`IP`) VALUES ('.$this->_get_value($data['ID']).','.$this->_get_value($data['TIMESTAMP']).','.$this->_get_value($data['LICENSE_ID']).','.$this->_get_value($data['MESSAGE']).','.$this->_get_value($data['SERVER']).','.$this->_get_value($data['IP']).');';
					}
				case 'return' :
					return 'SELECT '.$this->_get_value($data['RETURN'], '*', false).' FROM '.$table_pre.' '.$this->_build_matches($data['PAIRS']);
				case 'update' :
					return 'UPDATE '.$table_pre.' '.$this->_build_matches($data['SET'], 'SET', ', ').' '.$this->_build_matches($data['PAIRS']);
				case 'delete' :
					return 'DELETE FROM '.$table_pre.' '.$this->_build_matches($data['PAIRS']);
			}
		}
		
		/**
		* _get_value
		*
		* returns a null string if no value is found
		*
		* @access private 
		* @param $value string A value to be checked
		* @param $fill string The return value if the value is empty
  		* @return string 
		**/
		function _get_value($value, $fill='NULL', $add_quotes=true)
		{
			return empty($value) ? $fill : (is_string($value) && $add_quotes ? '"'.$value.'"' : $value);
		}
		
		/**
		* get_null
		*
		* returns formatted sql, ie `FIELD`="VALUE"
		*
		* @access private 
		* @param $pairs array An arrary containing the field/value pairs
		* @param $pre string The matches preceding command
		* @param $sep string The matches seperator
  		* @return string 
		**/
		function _build_matches($pairs, $pre='WHERE', $sep=' && ')
		{
			$num = count($pairs);
			# if there are no pairs return and empty string
			if($num == 0) return '';
			# init the string
			$str = $pre." ";
			# loop through the pairs and build the sql
			for($i=0; $i<$num; $i++)
			{
				$pair 		= $pairs[$i];
				$value 		= $pair['VALUE'];
				$comparison = empty($pair['COMP']) ? '=' : $pair['COMP'];
				$str 	.= '`'.$pair['FIELD'].'`'.$comparison.(is_string($value) ? '"'.$value.'"' : $value);
				if($i < $num-1)
				{
					$str.= $sep;
				}
			}
			return $str;
		}
		
		/**
		* _generate_return_data
		*
		* generates an encrypted string used to return data once the license has
		* been validated on the return server
		*
		* @access private 
		* @param $array array Array that contains the return info from the dial home server
  		* @return string encrypted data string containing server validation info
		**/
		function _generate_return_data($array)
		{
			return $this->BEGIN2.$this->_encrypt($array, 'HOMEKEY').$this->END2;
		}
		
		/**
		* receiveInstall
		*
		* receives an install and license key request. It validates the install
		* request, process it and checks for duplicates against the allow install
		* number, then returns with the result
		*
		* @access public 
		* @param $data array Data array (ie $_POST['POSTDATA']) recieved from
		*					 the client dialing home
		* @param $table string Database table that contains the distributionLicense sql
		* @param $user string User for the mysql db
		* @param $pass string Pass for the mysql db
		* @param $host string Host for the mysql db
  		* @return string Returns the encrypted result from the register_install function in the app
  		*			EMPTY_DATA					- no data has been given to the _recieve_call function
  		*			OVER_INSTALLED				- if the number of installs registered is too many then it is returned
  		*			CORRUPT						- the request does not contain an id and is therefore a corrupt / hacked request
  		*			PENDING						- the data is ok, however the approval type for license generation is set to ADMIN
  		*										  thus any key generation needs to be approved by an administrator
  		*			OK							- the license correspods to the info held on the home server and all is ok
		**/
		function receive_install($data, $table, $user, $pass, $host='localhost')
		{
			$orig_data = $data;
			# init the return array
			$return = array();
			
			# if there is not data die with an error
			if(empty($data)) return $this->_generate_return_data(array('RESULT'=>'EMPTY_DATA'));
			
			# decrypt the data
			$data = $this->_decrypt($data, 'HOMEKEY');
			
			# check for an id
			if($data['ID'] != md5($this->ID2)) return $this->_generate_return_data(array('RESULT'=>'CORRUPT'));
			
			# init the results array
			$results = array();
			# init the license array
			$license = array();

			# insert the id
			$license['ID'] = md5($this->ID1);
			
			# if server checks are required
			if($this->USE_SERVER)
			{
				if(!$this->_compare_domain_ip($data['SERVER']['DOMAIN'], $data['SERVER']['IP'])) $return['RESULT'] = 'DOMAIN_IP_FAIL';
				# update mac address
				$license['SERVER']['DOMAIN']= $data['SERVER']['DOMAIN'];
				# update mac address
				$license['SERVER']['MAC'] 	= $data['SERVER']['MAC'];
				# update the server details
				$license['SERVER']['PATH'] 	= $data['SERVER']['PATH'];
				# update the ip details
				$license['SERVER']['IP'] 	= $data['SERVER']['IP'];
			}
			
			# if time checks are required
			if($this->USE_TIME)
			{
				$license['DATE']['START'] 	= $data['DATE']['START'];
				$license['DATE']['SPAN'] 	= $data['DATE']['SPAN'];
				$license['DATE']['END'] 	= $data['DATE']['END'];
			}
			
			# connect to the db
			$this->connect($table, $user, $pass, $host);

			if(!empty($data['KEY_CODE']))
			{
				# will build in pre approved key codes, thus allowing someone to purchase
				# a license to get a key code. This key code is linked to an individual
				# licensee. If the license key has not been used already and is valid 
				# the license will be generated straight away, regardless of the generation
				# approval variables. It will generate will the the purchase settings from 
				# the database
			}

			# merge the exiting data set in the request with the approval data
			$license['DATA'] = array_merge($data['DATA'], $this->APPROVAL_DATA);
					
			if(!isset($return['RESULT'])) 
			{
				# start building the insertion query
				$query = 'INSERT INTO `'.$this->DB_PREFIX.'licenses` (`LICENSE_ID`,`USER_ID`,`FLAGGED`,`STATUS`,`START_DATE`,`DATE_SPAN`,`EXPIRY_DATE`,`NOTES`,`COST`,`PAID`,`REQUEST_KEY`,`TEMP_KEY`,`LICENSE_KEY`,`PAYMENT_NEXT_DUE`,`INSTALL`,`PACKAGES`,`MAC`,`PATH`,`IP`) VALUES (-VALUES-);';
				
				# sort the approval type
				if($this->APPROVAL_TYPE == 'AUTO')
				{
					# if the approval time is greater than 0 then override the end time
					if($this->APPROVAL_TIME > 0)
					{
						# set the time span of the temp key
						$start 						= time();
						$license['DATE']['START'] 	= $start;
						$license['DATE']['SPAN'] 	= $this->APPROVAL_TIME;
						$license['DATE']['END'] 	= $start+$this->APPROVAL_TIME;
					}
					$create_license = true;
					$return['RESULT'] 	= 'OK';
					$values = 'NULL,NULL,NULL,1,'.$license['DATE']['START'].','.$license['DATE']['SPAN'].','.$license['DATE']['END'].',NULL,NULL,1,"'.$orig_data.'",NULL,NULL,NULL,NULL,NULL,"'.$data['SERVER']['MAC'].'","'.urlencode(serialize($data['SERVER']['PATH'])).'","'.urlencode(serialize($data['SERVER']['IP'])).'"';
				}
				else if($this->APPROVAL_TYPE == 'ADMIN')
				{
					$key = '';
					# if the approval time is greater than 0 generate a temp key
					if($this->APPROVAL_TIME > 0)
					{
						# set the time span of the temp key
						$start 						= time();
						$license['DATE']['START'] 	= $start;
						$license['DATE']['SPAN'] 	= $this->APPROVAL_TIME;
						$license['DATE']['END'] 	= $start+$this->APPROVAL_TIME;
						$license['TEMP'] = 1;
						$create_license = true;
					}
					$values = 'NULL,NULL,NULL,1,'.$license['DATE']['START'].','.$license['DATE']['SPAN'].','.$license['DATE']['END'].',NULL,NULL,NULL,"'.$orig_data.'",NULL,NULL,NULL,NULL,NULL,"'.$data['SERVER']['MAC'].'","'.urlencode(serialize($data['SERVER']['PATH'])).'","'.urlencode(serialize($data['SERVER']['IP'])).'"';
					$return['RESULT'] 	= 'PENDING';
				}
				$query = str_replace('-VALUES-', $values, $query);
				if(!mysql_query($query, $this->_DB))
				{
					$return['ERROR'] = mysql_error();
					$return['RESULT'] = 'SERVER_DB_FAILED';
					$create_license = false;
				}
				
				if($create_license)
				{
					$license['DBID'] = mysql_insert_id($this->_DB);
					# create the key
					$key = $this->_create_license($license);
					$return['KEY'] 	= $key;
					$row = ($this->APPROVAL_TYPE == 'ADMIN') ? 'TEMP_KEY' : 'LICENSE_KEY';
					if(!mysql_query('UPDATE `'.$this->DB_PREFIX.'licenses` SET `'.$row.'`="'.$key.'" WHERE `LICENSE_ID`="'.$license['DBID'].'"'))
					{
						$return['ERROR'] = mysql_error();
						$return['RESULT'] = 'SERVER_DB_FAILED';
						unset($return['KEY']);
					}
				}

			}
			
			# disconnect from the db
			$this->disconnect();
			
			# generate the return data
			return $this->_generate_return_data($return);
		}
		
		/**
		* generate
		*
		* generates the client license key
		*
		* @access private 
		* @param $data array The data array containing the binding info
  		* @return string key string
		**/
		function _create_license($data)
		{
			# set the server os
			$data['DATA']['_PHP_OS'] 	 	= PHP_OS;  
			# set the server os
			$data['DATA']['_PHP_VERSION'] 	= PHP_VERSION;  
			# encrypt the key and return
			return $this->_wrap_license($data);
		}

		/**
		* recieve_call
		*
		* the function to be called by the script that recieves the call on your
		* server from the client server
		*
		* @access public 
		* @param $data array Data array (ie $_POST['POSTDATA']) recieved from
		*					 the client dialing home
		* @param $table string Database table that contains the distributionLicense sql
		* @param $user string User for the mysql db
		* @param $pass string Pass for the mysql db
		* @param $host string Host for the mysql db
  		* @return string Returns the encrypted server validation result from the dial home call
  		*			EMPTY_DATA					- no data has been given to the _recieve_call function
  		*			EMPTY_ID					- no id has been found in the data sent so dial home check cannot be made
  		*			SERVER_LICENSE_404			- the license does not exist on the dial home server
  		*			SERVER_LICENSE_MISMATCH		- the license key sent to the home server and the license key residing in the 
  		*										  home server db do not match
  		*			SERVER_LICENSE_INACTIVE		- license is inactive (ie expired)
  		*			SERVER_LICENSE_WARNING		- a warning has been given to the license holder
  		*			SERVER_LICENSE_SUSPENDED	- the license has been suspended
  		*			SERVER_LICENSE_REVOKED		- license has been revoked
  		*			SERVER_PAYMENT_NOT_MADE		- license has not been paid for
  		*			SERVER_PAYMENT_DUE			- next payment is due
  		*			SERVER_DATE_MISMATCH		- license start date does not match the one on the dial home server
  		*			SERVER_MAC_MISMATCH			- the mac address registered with the license does not match the one on the home server
  		*			SERVER_SERVER_MISMATCH		- the server vars supplied in the license mismatch the stored vars on the home server
  		*			OK							- the license correspods to the info held on the home server and all is ok
		**/
		function recieve_call($data, $table, $user, $pass, $host='localhost')
		{
			# if there is not data die with an error
			if(empty($data))
			{
				return $this->_generate_return_data(array('RESULT'=>'EMPTY_DATA'));
			}
			# init the results array
			$results = array();
			# decrypt the data
			$data = $this->_decrypt($data, 'HOMEKEY');

			# if there is no license id to check then return with error
			if(!isset($data['LICENSE_DATA']['DBID']))
			{
				return $this->_generate_return_data(array('RESULT'=>'EMPTY_ID'));
			}
			
			$istemp = (isset($data['LICENSE_DATA']['TEMP']));

			# connect to the db
			$this->connect($table, $user, $pass, $host);

			# insert log remote log data
			mysql_query('INSERT INTO `'.$this->DB_PREFIX.'remote_log` (`ID`,`TIMESTAMP`,`LICENSE_ID`,`MESSAGE`,`SERVER`,`IP`) VALUES (NULL,"'.time().'","'.$data['LICENSE_DATA']['DBID'].'","'.'test'.'","'.urlencode(serialize($data['LICENSE_DATA']['SERVER']['PATH'])).'","'.$_SERVER['REMOTE_ADDR'].'")', $this->_DB);

			# check up on license in the license table
			$lookup_result = mysql_query('SELECT * FROM `'.$this->DB_PREFIX.'licenses` WHERE `LICENSE_ID`="'.$data['LICENSE_DATA']['DBID'].'"', $this->_DB);
 			# get the license data from the server
			$license = mysql_fetch_array($lookup_result, MYSQL_ASSOC);
			
			# disconnect from the db
			$this->disconnect();

			# if the result is not returned or the license does not exist			
			if(!$lookup_result || !$license)
			{
				# die and send back the result to the server calling home
				return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_404'));
			}

			# compare the license sent against the license stored
			# if any changes have been made to the clients license without updating the one
			# on the server then this will throw an error, main cause would be a hack or
			# developer error
			$stored_key = ($istemp) ? $license['TEMP_KEY'] : $license['LICENSE_KEY'];
			if(md5($stored_key) != $data['LICENSE_DATA']['KEY'])
			{
				return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_MISMATCH'));
			}
			
			# check if license is active
			switch($license['STATUS'])
			{
				case 0 :
					return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_INACTIVE'));
				case -1 :
					return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_WARNING'));
				case -2 :
					return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_SUSPENDED'));
				case -3 :
					return $this->_generate_return_data(array('RESULT'=>'SERVER_LICENSE_REVOKED'));
				default :
					break;
			}

			# check if user has paid
			if(!$license['PAID'] && !$istemp)
			{
				return $this->_generate_return_data(array('RESULT'=>'SERVER_PAYMENT_NOT_MADE'));
			}

			# check if the next pay date is passed
			if($license['PAYMENT_NEXT_DUE']>0 && $license['PAYMENT_NEXT_DUE']  < time())
			{
				return $this->_generate_return_data(array('RESULT'=>'SERVER_PAYMENT_DUE'));
			}

			# check the start date and see if it is valid
			if($this->USE_TIME && ($license['START_DATE'] != $data['LICENSE_DATA']['DATE']['START'] || $license['EXPIRY_DATE'] != $data['LICENSE_DATA']['DATE']['END']))
			{
				return $this->_generate_return_data(array('RESULT'=>'SERVER_DATE_MISMATCH'));
			}

			# if server checks are required
			if($this->USE_SERVER)
			{
				# check mac address
				if($license['MAC'] != $data['LICENSE_DATA']['SERVER']['MAC'])
				{
					return $this->_generate_return_data(array('RESULT'=>'SERVER_MAC_MISMATCH'));
				}

				# check the server vars
				$server_difs = count(array_diff(unserialize(urldecode($license['PATH'])), $data['LICENSE_DATA']['SERVER']['PATH']));
				if($server_difs > 0)
				{
					return $this->_generate_return_data(array('RESULT'=>'SERVER_SERVER_MISMATCH'));
				}
			}
			
			return $this->_generate_return_data(array('RESULT'=>'OK'));
		}
		
	}

?>