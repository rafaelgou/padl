<?php
$MESSAGE['LICENSE_OK']            = "The License Key supplied with this file is valid. If this application was not a demonstration the end user would not see this display, your application could then run as normal.";
$MESSAGE['LICENSE_TMINUS']        = "The License Key supplied you are using with this application has not yet entered its valid period. The License Key is valid from <b>{[DATE_START]}</b> to <b>{[DATE_END]}</b>.";
$MESSAGE['LICENSE_EXPIRED']	      = "The License Key supplied you are using with this application has expired and is no longer valid. The License Key was valid from <b>{[DATE_START]}</b> to <b>{[DATE_END]}</b>.";
$MESSAGE['LICENSE_ILLEGAL']       = "The License Key is not valid for this server. This means that you cannot make further use of this application untill you purchase a valid key. HOWEVER, if you have you have purchased a valid key and you get this message in error, please contact the applications reseller.";
$MESSAGE['LICENSE_ILLEGAL_LOCAL'] = "This application can not be run on the localhost. The application can only be run under a valid domain.";
$MESSAGE['LICENSE_INVALID']       = "The License Key is invalid. This means that your License Key file has become corrupted. Please replace the file 'license.supplied.dat' with a copy of the original license. If you do not still have a copy of the original license please contact the applications reseller.<br /><br />HOWEVER, you should note that this might be because the key was generated using mcrypt, where as your PHP install does not have access to the Mcrypt library (this is very unlikley).";
$MESSAGE['LICENSE_EMPTY']         = "The License Key is empty. Please make sure that the file 'license.supplied.dat' is writeable by the web server, then copy and paste the key into the box below and click submit.<br /><br /><form action='' method='POST'><textarea id='key' name='key' rows='7' cols='60'></textarea><br /><br /><input type='submit' id='submit' name='submit' value='Submit'></form>";
$MESSAGE['WRITE_ERROR']           = "<span class='warning'>The License Key was unable to be written! Please make sure that license.supplied.dat is writeable by the web server.</span><br /><br />";

# switch through the results
switch($results['RESULT'])
{
    case 'OK' :
        $result          = 'License Ok';
        $message         = $MESSAGE['LICENSE_OK'];
        break;
    case 'TMINUS' :
        $result          = 'License tminus';
        $message         = str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $MESSAGE['LICENSE_TMINUS']);
        break;
    case 'EXPIRED' :
        $result          = 'Expired License';
        $message         = str_replace(array('{[DATE_START]}', '{[DATE_END]}'), array($results['DATE']['HUMAN']['START'], $results['DATE']['HUMAN']['END']), $MESSAGE['LICENSE_EXPIRED']);
        break;
    case 'ILLEGAL' :
        $result          = 'Illegal License';
        $message         = $MESSAGE['LICENSE_ILLEGAL'];
        break;
    case 'ILLEGAL_LOCAL' :
        $result          = 'Illegal License';
        $message         = $MESSAGE['LICENSE_ILLEGAL_LOCAL'];
        break;
    case 'INVALID' :
        $result          = 'Invalid License';
        $message         = $MESSAGE['LICENSE_INVALID'];
        break;
    case 'EMPTY' :
        $result         = 'Empty License';
        $message         = $MESSAGE['LICENSE_EMPTY'];
        if(defined('write_error')) $message = $MESSAGE['WRITE_ERROR'].$message;
        break;
    default :
        break;
}
?>
        <div id="padl_alert">
            <div id="padl_message">
                <div id="padl_result">
                    <?php echo $result ?>
                </div>
                <?php echo $message ?>
            </div>
            <div id="padl_credits">
                Copyright &COPY; 2005 Oliver Lillie<br/>
                Copyright &COPY; 2011 Rafael Goulart<br/>
            </div>
        </div>
