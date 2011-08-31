<?php

	function batchSQL ($script, $id, $errors= ERR_STOP) 
	{
		$errors = array();
		if (is_null($script) || $script == "")
		{
			return false;
		}
		$script = str_replace (" --", " #", $script);
		$script = explode (";", $script);
		foreach ($script as $command)
		{
			if ((!$result=mysql_query($command, $id)) && mysql_errno($id) != 1065) 
			{
				$errors[] = mysql_error();
			}
			if (mysql_errno ($id) != 1065) 
			{
				$output = $result;
			}
		}
		return (count($errors) > 0) ? $errors : $output;
	}

	function connect($table, $user, $pass, $host='localhost')
	{
		# connect to your servers DB
		$id = @mysql_pconnect($host, $user, $pass);
		# select the table required
		@mysql_select_db($table, $id);
		return $id;
	}
	
	function disconnect($id)
	{
		# close the database
		return @mysql_close($id);
	}
	
	include_once('db.config.php');
	
	$id = connect($table, $user, $pass);
	$result = batchSQL(file_get_contents('db.mysql.sql'), $id);
	disconnect($id);
	
	if(is_bool($result))
	{
		die('Successfull install of the distributionLicense sql was completed.');
	}
	else
	{
		echo 'There were errors wth the sql install.<br /><br />';
		echo '<pre>';
		print_r($result);
		echo '</pre>';
	}
	
?>