<?php

/**
* Project:        Distrubution License Class
* File:            demo.app.license.php
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

# Register Autoload
include_once('../../../src/PHP5.3/Padl/Padl.php');
Padl::registerAutoload();

# initialise the class
# note for this demonstration script we will turn off mcrypt usage
# as some systems do not have it installed in their setup.
# the initial argument usually defaults to true (use mcrypt)
$application = new Padl\LicenseApplication('license.dat', false, true, false, true);

# set the server vars
# note this doesn't have to be set, however if not all of your app files are encoded
# then there would be a possibility that the end user could modify the server vars
# to fit the key thus making it possible to use your app on any domain
# you should copy your server vars in the first line of your active script so you can
# use the unmodified copy of the vars
$application->set_server_vars($server_array);

# the set key is the key validated for my server, when run on your box it will be illegal
$results     = $application->validate();

# make the application secure by running this function
# it also prevents any future reincarnations of the class calling any of the 
# key generation and validation functions, it also deletes any class variables
# that may be set.
$application->make_secure();

# delete the $application object
unset($application);

# import the language
include_once('demo.app.lang.php');

# switch through the results
switch($results['RESULT'])
{
    case 'OK' :
        $result          = 'ok';
        $message         = $LANG['LICENSE_OK'];
        break;
    case 'TMINUS' :
        $result          = 'tminus';
        $message         = str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $LANG['LICENSE_TMINUS']);
        break;
    case 'EXPIRED' :
        $result          = 'expired';
        $message         = str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $LANG['LICENSE_EXPIRED']);
        break;
    case 'ILLEGAL' :
        $result          = 'illegal';
        $message         = $LANG['LICENSE_ILLEGAL'];
        break;
    case 'ILLEGAL_LOCAL' :
        $result          = 'illegal';
        $message         = $LANG['LICENSE_ILLEGAL_LOCAL'];
        break;
    case 'INVALID' :
        $result          = 'invalid';
        $message         = $LANG['LICENSE_INVALID'];
        break;
    case 'EMPTY' :
        $result         = 'empty';
        $message         = $LANG['LICENSE_EMPTY'];
        if(defined('write_error')) $message = $LANG['WRITE_ERROR'].$message;
        break;
    default :
        break;
}
?><html>
<head>
  <link rel="stylesheet" type="text/css" href="../css/style.css" />
</head>
<body>
    <div id="padl_alert">
        <div id="padl_message">
            <div id="padl_result">
                License <?php echo $result ?>
            </div>
            <?php echo $message ?>
        </div>
        <div id="padl_credits">
            Copyright &COPY; 2005 Oliver Lillie<br/>
            Copyright &COPY; 2011 Rafael Goulart - rafaelgou@gmail.com<br/>
        </div>
    </div>
    
    
</body>
</html>
