<?php
/**
 * Project:   Distrubution License Class
 * File:      PadlLicense.php
 *
 * Copyright (C) 2005 Oliver Lillie
 * Copyright (C) 2011 Rafael Goulart
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
 * @author  Oliver Lillie buggedcom <publicmail@buggedcom.co.uk>
 * @author  Rafael Goulart <rafaelgou@gmail.com>
 * @license GNU Lesser General Public License
 * @version Release: 1.0.0
 * @link    http://padl.rgou.net
 * @link    http://www.buggedcom.co.uk/
 * @link    http://www.phpclasses.org/browse/package/2298.html
 * @history---------------------------------------------
 * see CHANGELOG
 */
class PadlLicense
{
    /**
    * hash key 1 used to encrypt the generate key data.
    * hash key 2 used to encrypt the request data
    * hash key 3 used to encrypt the dial home data
    * NOTE1 : there are three different hash keys for the three different operations
    * NOTE2 : these hash key's are for use by both mcrypt and alternate cryptions
    *       and although mcrypts keys are typically short they should be kept long
    *      for the sake of the other functions
    *
    * @var string
    * @var string
    * @var string
    */
    protected $hashKey1   = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35BDFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEzGDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHT';
    protected $hashKey2   = '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYDU52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wohX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4a';
    protected $hashKey3   = 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W66qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62T';

    /**
    * You may not want to use mcrypt even if your system has it installed
    * make this false to use a regular encryption method
    *
    * @var boolean
    */
    protected $useMcrypt  = true;

    /**
    * The algorythm to be used by mcrypt
    *
    * @var string
    */
    protected $algorithm  = 'blowfish';

    /**
    * use time binding vars inited.
    */
    protected $useTime;

    /**
    * time checking start period difference allowance ie if the user has slightly different time
    * setting on their server make an allowance for the diff period. carefull to not make it too
    * much otherwise they could just reset their server to a time period before the license expires.
    *
    * @var number (seconds)
    */
    protected $startDif    = 129600;

    /**
    * id 1 used to validate license keys
    * id 2 used to validate license key requests
    * id 2 used to validate dial home data
    *
    * @var string
    * @var string
    * @var string
    */
    // id to check for to validate source
    protected $id1      = 'nSpkAHRiFfM2hE588eB';
    protected $id2      = 'NWCy0s0JpGubCVKlkkK';
    protected $id3      = 'G95ZP2uS782cFey9x5A';

    /**
    * begining and end strings
    *
    * @var strings
    */
    protected $begin1    = 'BEGIN LICENSE KEY';
    protected $end1      = 'END LICENSE KEY';

    /**
    * wrap key settings
    *
    * @var number
    * @var string
    * @var string
    */
    protected $wrapto    = 80;
    protected $pad      = "-";

    /**
    * dial home return query deliminators
    *
    * @var string
    * @var string
    */
    protected $begin2     = '_DATA{';
    protected $end2       = '}DATA_';

    /**
    * init the key data array.
    *
    * @var array
    */
    protected $data      = array();

    /**
    * use server binding vars inited.
    */
    protected $useServer;
    protected $serv;
    protected $mac;
    protected $allowLocal;
    protected $serverInfo = array();
    protected $serverVars = array();
    protected $ips = array();


    /**
    * this is the number of required server stats for the key generation to be successfull
    * if the server can't produce this number of details then the key fails to be generated
    * you can set it to however many you wish, the max is 5
    *
    * @var number
    */
    protected $requiredUris  = 2;

    /**
    * the date string for human readable format
    *
    * @var string
    */
    protected $dateString  = 'M/d/Y H:i:s';

    /**
    * The number of allowed differences between the $SERVER vars and the vars
    * stored in the key
    *
    * @var number
    */
    protected $allowedServerDifs  = 0;

    /**
    * The number of allowed differences between the $ip vars in the key and the ip
    * vars collected from the server
    *
    * @var number
    */
    protected $allowedIpDifs    = 0;

    /**
     * Constructor
     *
     * @param boolean $useMcrypt  boolean Determines if mcrypt encryption is used or not (defaults to true,
     *                             however if mcrypt is not available, it is set to false)
     * @param boolean $useTime    boolean Sets if time binding should be used in the key (defaults to true)
     * @param boolean $useServer  boolean Sets if server binding should be used in the key (defaults to true)
     * @param boolean $allowLocal boolean Sets if server binding is in use then localhost servers are valid (defaults to false)
     *
     * @return void 
     **/
    public function __construct($useMcrypt=true, $useTime=true, $useServer=true, $allowLocal=false)
    {
        $this->init($useMcrypt, $useTime, $useServer, $allowLocal);
        if ($this->useServer) {
            $this->mac  = $this->getMacAddress();
        }
    }

    /**
     * init
     * init the license class
     *
     * @param boolean $useMcrypt  Determines if mcrypt encryption is used or not (defaults to true,
     *                            however if mcrypt is not available, it is set to false)
     * @param boolean $useTime    Sets if time binding should be used in the key (defaults to true)
     * @param boolean $useServer  Sets if server binding should be used in the key (defaults to true)
     * @param boolean $allowLocal Sets if server binding is in use then localhost servers are valid (defaults to false)
     * 
     * @return void
     * 
     **/
    public function init($useMcrypt=true, $useTime=true, $useServer=true, $allowLocal=false)
    {
        $this->useMcrypt  = ($useMcrypt && function_exists('mcrypt_generic'));
        $this->useTime    = $useTime;
        $this->allowLocal = $allowLocal;
        $this->useServer  = $useServer;
    }

    /**
     * setServerVars
     *
     * to protect against spoofing you should copy the $server vars into a
     * separate array right at the first line of your script so parameters can't
     * be changed in unencoded php files. This doesn't have to be set. If it is
     * not set then the $server is copied when _getServerInfo (private) function
     * is called.
     *
     * @param array $array The copied $server array
     *
     * @return void 
     **/
    public function setServerVars($array)
    {
      $this->serverVars = $array;
      // some of the ip data is dependant on the $server vars, so update them
      // after the vars have been set
      $this->ips      = $this->getIpAddress();
      // update the server info
      $this->serverInfo  = $this->getServerInfo();
    }

    /**
     * Validate a license
     *
     * @param string $license The license string
     * 
     * @return array
     **/
    public function validate ($license)
    {
        return $this->doValidate($license);
    }

    /**
     * Validate a License through a remote server
     *
     * @param string $license  The license to validate
     * @param string $dialhost The host to dial
     * @param string $dialpath The path to dial
     * @param string $dialport The port of the host
     * 
     * @return array
     */
    public function validateRemote ($license, $dialhost, $dialpath, $dialport="80")
    {
        return $this->doValidate($license, true, $dialhost, $dialpath, $dialport);
    }

    /**
     * doValidate
     *
     * validates the server key and returns a data array.
     * Main object in array is 'RESULT', it contains the result of the validation.
     *     OK     - key is valid
     *     CORRUPT   - key has been tampered with
     *     TMINUS   - the key is being used before the valid start date
     *     EXPIRED   - the key has expired
     *     ILLEGAL   - the key is not on the same server the license was registered to
     *     ILLEGAL_LOCAL   - the key is not allowed to be installed on a local machine
     *     INVALID   - the the encryption key used to encrypt the key differs or the key is not complete
     *     EMPTY     - the the key is empty
     *     404     - the the key is missing
     *
     * @param string  $license  The license to validate
     * @param boolean $dialhome Dial to home
     * @param string  $dialhost The host to dial
     * @param string  $dialpath The path to dial
     * @param string  $dialport The port of the host
     * 
     * @return array 
     * @return array
     **/
    protected function doValidate($license, $dialhome=false, $dialhost="", $dialpath="", $dialport="80")
    {
//      // check to see if the class has been secured
//      $this->check_secure();

        if (strlen($license)>0) {
            // decrypt the data
            $data = $this->unwrapLicense($license);
            if (is_array($data)) {
                // missing / incorrect id therefore it has been tampered with
                if ($data['ID'] != md5($this->id1)) {
                    $data['RESULT'] = 'CORRUPT';
                }
                if ($this->useTime) {
                    // the license is being used before it's official start
                    if ($data['DATE']['START'] > time()+$this->startDif) {
                        $data['RESULT'] = 'TMINUS';
                    }
                    // the license has expired
                    if ($data['DATE']['END']-time() < 0 && $data['DATE']['SPAN'] != 'NEVER') {
                        $data['RESULT'] = 'EXPIRED';
                    }
                    $data['DATE']['HUMAN']['START'] = date($this->dateString, $data['DATE']['START']);
                    $data['DATE']['HUMAN']['END']   = date($this->dateString, $data['DATE']['END']);
                }
                if ($this->useServer) {
                    $mac     = $data['SERVER']['MAC'] == $this->mac;
                    $path    = count(array_diff($this->serverInfo, $data['SERVER']['PATH'])) <= $this->allowedServerDifs;
                    $domain  = $this->compareDomainIp($data['SERVER']['DOMAIN'], $this->ips);
                    $ip      = count(array_diff($this->ips, $data['SERVER']['IP'])) <= $this->allowedIpDifs;

                    // the server details
                    if (!$mac || !$path || !$domain || !$ip) {
                        $data['RESULT'] = 'ILLEGAL';
                    }

                    // check if local
                    $local = $this->allowLocal && (in_array('127.0.0.1', $data['SERVER']['IP']) || $data['PATH']['SERVER_ADDR'] == '127.0.0.1' || $data['PATH']['HTTP_HOST'] == '127.0.0.1');
                    if (!$local) {
                        $data['RESULT'] = 'ILLEGAL_LOCAL';
                    }
                }
                // passed all current test so license is ok
                if (!isset($data['RESULT'])) {
                    // dial to home server if required
                    if ($dialhome) {
                        // create the details to send to the home server
                        $stuffToSend = array();
                        $stuffToSend['LICENSE_DATA'] = $data;
                        $stuffToSend['LICENSE_DATA']['KEY'] = md5($license);
                        // dial home
                        $data['RESULT'] = $this->callHome($stuffToSend, $dialhost, $dialpath, $dialport);
                    } else {
                        // result is ok all test passed, license is legal
                        $data['RESULT'] = 'OK';
                    }
                }
                // data is returned for use
                return $data;
            } else {
                // the are two reason that mean a invalid return
                // 1 - the other hash key is different
                // 2 - the key has been tampered with
                return array('RESULT'=>'INVALID');
            }
        }
      // returns empty because there is nothing in the dat_string
      return array('RESULT'=>'EMPTY');
    }

    /**
     * Sets the Date Format
     * 
     * @param string $dateFormat The date format to use on license
     * 
     * @return void
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateString = $dateFormat;
    }

    /**
    * post_data
    *
    * Posts data to and recieves data from dial home server. Returned info
    * contains the dial home validation result
    *
    * @param string $host       Host name of the server to be contacted
    * @param string $path       Path of the script for the data to be sent to
    * @param array  $queryArray Array that contains the license key info to be validated
    * @param number $port       Port Number to send the data through
     * 
    * @return array Result of the dialhome validation
    * @return string - SOCKET_FAILED will be returned if it was not possible to open a socket to the home server
    **/
    protected function post_data($host, $path, $queryArray, $port=80)
    {
        // generate the post query info
        $query    = 'POSTDATA='.$this->encrypt($queryArray, 'HOMEKEY');
        $query    .= '&MCRYPT='.$this->useMcrypt;
        // init the return string
        $return  = '';

        // generate the post headers
        $post     = "POST $path HTTP/1.1\r\n";
        $post   .= "Host: $host\r\n";
        $post   .= "Content-type: application/x-www-form-urlencoded\r\n";
        $post   .= "Content-length: ".strlen($query)."\r\n";
        $post   .= "Connection: close\r\n";
        $post   .= "\r\n";
        $post   .= $query;

        // open a socket
        $header = @fsockopen($host, $port);
        if (!$header) {
            // if the socket fails return failed
            return array('RESULT'=>'SOCKET_FAILED');
        }
        @fputs($header, $post);
        // read the returned data
        while (!@feof($header)) {
            $return .= @fgets($header, 1024);
        }
        fclose($header);

        // seperate out the data using the delims
        $leftpos = strpos($return, $this->begin2)+strlen($this->begin2);
        $rightpos = strpos($return, $this->end2)-$leftpos;

        // decrypt and return the data
        return $this->decrypt(substr($return, $leftpos, $rightpos), 'HOMEKEY');
    }

    /**
     * compareDomainIp
     *
     * uses the supplied domain in the key and runs a check against the collected
     * ip addresses. If there are matching ips it returns true as the domain
     * and ip address match up
     *
     * @param type  $domain The domain to compare
     * @param mixed $ips    The IPs array or false
     * 
     * @return boolean
     **/
    protected function compareDomainIp($domain, $ips=false)
    {
        // if no ips are supplied get the ip addresses for the server
        if (!$ips) {
            $ips = $this->getIpAddress();
        }
        // get the domain ip list
        $domainIps = gethostbynamel($domain);
        // loop through the collected ip's searching for matches against the domain ips
        if (is_array($domainIps) && count($domainIps) > 0) {
            foreach ($domainIps as $ip) {
                if (in_array($ip, $ips)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * pad
     *
     * pad out the begin and end seperators
     *
     * @param string $str The string to be padded
     * 
     * @return string Returns the padded string
     **/
    protected function pad($str)
    {
        $strLen   = strlen($str);
        $spaces   = ($this->wrapto-$strLen)/2;
        $str1 = '';
        for ($i=0; $i<$spaces; $i++) {
            $str1 = $str1.$this->pad;
        }
        if ($spaces/2 != round($spaces/2)) {
            $str = substr($str1, 0, strlen($str1)-1).$str;
        } else {
            $str = $str1.$str;
        }
        $str = $str.$str1;
        return $str;
    }

    /**
     * get_key
     *
     * gets the hash key for the current encryption
     *
     * @param string $keyType The license key type being produced
     * 
     * @return string Returns the hash key
     **/
    protected function get_key($keyType)
    {
        switch($keyType) {
            case 'KEY' :
                return $this->hashKey1;
            case 'REQUESTKEY' :
                return $this->hashKey2;
            case 'HOMEKEY' :
                return $this->hashKey3;
            default :
            // TODO missing default return!!
        }
    }

    /**
     * getBegin
     *
     * gets the begining license key seperator text
     *
     * @param string $keyType string The license key type being produced
     * 
     * @return string Returns the begining string
     **/
    protected function getBegin($keyType)
    {
        switch($keyType)
        {
            case 'KEY' :
                return $this->begin1;
            case 'REQUESTKEY' :
                return $this->begin2;
            case 'HOMEKEY' :
                return '';
        }
    }

    /**
     * getEnd
     *
     * gets the ending license key seperator text
     *
     * @param string $keyType The license key type being produced
     * 
     * @return string Returns the ending string
     **/
    protected function getEnd($keyType)
    {
        switch($keyType)
        {
            case 'KEY' :
                return $this->end1;
            case 'REQUESTKEY' :
                return $this->end2;
            case 'HOMEKEY' :
                return '';
        }
    }

    /**
     * generateRandomString
     *
     * generates a random string
     *
     * @param number $length The length of the random string
     * @param string $seeds  The string to pluck the characters from
     * 
     * @return string Returns random string
     **/
    protected function generateRandomString($length=10, $seeds='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890123456789')
    {
        $str = '';
        $seedsCount = strlen($seeds);

        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seedsCount - 1)};
        }
        return $str;
    }

    /**
     * encrypt
     *
     * encrypts the key
     *
     * @param array  $srcArray The data array that contains the key data
     * @param string $keyType  The type of the key to encrypt
     * 
     * @return string Returns the encrypted string
     **/
    protected function encrypt($srcArray, $keyType='KEY')
    {
        $randAddOn = $this->generateRandomString(3);
        // get the key
        $key   = $this->get_key($keyType);
        $key   = $randAddOn . $key;

        // check to see if mycrypt exists
        if ($this->useMcrypt) {
            // openup mcrypt
            $td = mcrypt_module_open($this->algorithm, '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            // process the key
            $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            // init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            // encrypt data
            // double base64 gets makes all the characters alpha numeric
            // and gets rig of the special characters
            $crypt   = mcrypt_generic($td, serialize($srcArray));

            // shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            // if mcrypt doesn't exist use regular encryption method
            // init the vars
            $crypt = '';
            $str = serialize($srcArray);

            // loop through the str and encrypt it
            for ($i=1; $i<=strlen($str); $i++) {
                $char     = substr($str, $i-1, 1);
                $keychar   = substr($key, ($i % strlen($key))-1, 1);
                $char     = chr(ord($char)+ord($keychar));
                $crypt    .= $char;
            }
        }
        // return the key
        return $randAddOn.base64_encode(base64_encode(trim($crypt)));
    }

    /**
     * decrypt
     *
     * decrypts the key
     *
     * @param string $str     The data that contains the key data
     * @param string $keyType The type of the key to encrypt
     * 
     * @return array Returns decrypted array
     **/
    protected function decrypt($str, $keyType='KEY')
    {
        $randAddOn = substr($str, 0, 3);
        $str = base64_decode(base64_decode(substr($str, 3)));
        // get the key
        $key = $randAddOn . $this->get_key($keyType);

        // check to see if mycrypt exists
        if ($this->useMcrypt) {
            // openup mcrypt
            $td = mcrypt_module_open($this->algorithm, '', 'ecb', '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            // process the key
            $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            // init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            // decrypt the data and return
            $decrypt = @mdecrypt_generic($td, $str);

            // shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            // if mcrypt doesn't exist use regular decryption method
            // init the decrypt vars
            $decrypt   = '';

            // loop through the text and decode the string
            for ($i=1; $i<=strlen($str); $i++) {
                $char     = substr($str, $i-1, 1);
                $keychar  = substr($key, ($i % strlen($key))-1, 1);
                $char     = chr(ord($char)-ord($keychar));
                $decrypt  .= $char;
            }
        }
        // return the key
        return @unserialize($decrypt);
    }

    /**
     * wrapLicense
     *
     * wraps up the license key in a nice little package
     *
     * @param array  $srcArray The array that needs to be turned into a license str
     * @param string $keyType  The type of key to be wrapped (KEY=license key, REQUESTKEY=license request key)
     * 
     * @return string Returns encrypted and formatted license key
     **/
    protected function wrapLicense($srcArray, $keyType='KEY')
    {
        // sort the variables
        $begin = $this->pad($this->getBegin($keyType));
        $end   = $this->pad($this->getEnd($keyType));

        // encrypt the data
        $str   = $this->encrypt($srcArray, $keyType);

        // return the wrap
        return $begin . PHP_EOL . wordwrap($str, $this->wrapto, PHP_EOL, 1) . PHP_EOL . $end;
    }

    /**
    * unwrapLicense
    *
    * unwraps license key back into it's data array
    *
    * @param string $encStr  The encrypted license key string that needs to be decrypted
    * @param string $keyType The type of key to be unwrapped (KEY=license key, REQUESTKEY=license request key)
     * 
    * @return array Returns license data array
    **/
    protected function unwrapLicense($encStr, $keyType='KEY')
    {
        // sort the variables
        $begin = $this->pad($this->getBegin($keyType));
        $end   = $this->pad($this->getEnd($keyType));

        // get string without seperators
        $str   = trim(str_replace(array($begin, $end, "\r", "\n", "\t"), '', $encStr));

        // decrypt and return the key
        return $this->decrypt($str, $keyType);
    }

    /**
     * getOsVar
     *
     * gets various vars depending on the os type
     *
     * @param type $varName The var name
     * @param type $os      The os name
     * 
     * @return string various values
     **/
    protected function getOsVar($varName, $os)
    {
        $varName = strtolower($varName);
        // switch between the os's
        switch($os)
        {
            // not sure if the string is correct for FreeBSD
            // not tested
            case 'freebsd' :
            // not sure if the string is correct for NetBSD
            // not tested
            case 'netbsd' :
            // not sure if the string is correct for Solaris
            // not tested
            case 'solaris' :
            // not sure if the string is correct for SunOS
            // not tested
            case 'sunos' :
            // darwin is mac os x
            // tested only on the client os
            case 'darwin' :
                // switch the var name
                switch($varName)
                {
                    case 'conf' :
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac' :
                        $var = 'ether';
                        break;
                    case 'ip' :
                        $var = 'inet ';
                        break;
                }
                break;
            // linux variation
            // tested on server
            case 'linux' :
                // switch the var name
                switch($varName)
                {
                    case 'conf' :
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac' :
                        $var = 'HWaddr';
                        break;
                    case 'ip' :
                        $var = 'inet addr:';
                        break;
                }
                break;
        }
        return $var;
    }

    /**
     * getConfig
     *
     * gets the server config file and returns it. tested on Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @return string config file data
     **/
    protected function getConfig()
    {
        if (ini_get('safe_mode')) {
            // returns invalid because server is in safe mode thus not allowing
            // sbin reads but will still allow it to open. a bit weird that one.
            return 'SAFE_MODE';
        }
        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3)=='win') {
            // this windows version works on xp running apache
            // based server. it has not been tested with anything
            // else, however it should work with NT, and 2000 also

            // execute the ipconfig
            @exec('ipconfig/all', $lines);
            // count number of lines, if none returned return MAC_404
            // thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
            if (count($lines) == 0) {
                return 'ERROR_OPEN';
            }
            // $path the lines together
            $conf = implode(PHP_EOL, $lines);
        } else {
            // get the conf file name
            $osFile = $this->getOsVar('conf', $os);
            // open the ipconfig
            $fp = @popen($osFile, "rb");
            // returns invalid, cannot open ifconfig
            if (!$fp) {
                return 'ERROR_OPEN';
            }
            // read the config
            $conf = @fread($fp, 4096);
            @pclose($fp);
        }
        return $conf;
    }

    /**
     * getIpAddress
     *
     * Used to get the MAC address of the host server. It works with Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @return array IP Address(s) if found (Note one machine may have more than one ip)
     * @return string ERROR_OPEN means config can't be found and thus not opened
     * @return string IP_404 means ip adress doesn't exist in the config file and can't be found in the $server
     * @return string SAFE_MODE means server is in safe mode so config can't be read
     **/
    protected function getIpAddress()
    {
        $ips = array();
        // get the cofig file
        $conf = $this->getConfig();
        // if the conf has returned and error return it
        if ($conf != 'SAFE_MODE' && $conf != 'ERROR_OPEN') {
            // if anyone has any clues for windows environments
            // or other server types let me know
            $os = strtolower(PHP_OS);
            if (substr($os, 0, 3)=='win') {
            // anyone any clues on win ip's
            } else {
                // explode the conf into seperate lines for searching
                $lines = explode(PHP_EOL, $conf);
                // get the ip delim
                $ipDelim = $this->getOsVar('ip', $os);

                // ip pregmatch
                $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
                // seperate the lines
                foreach ($lines as $key => $line) {
                    // check for the ip signature in the line
                    if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ipDelim)) {
                        // seperate out the ip
                        $ip   = substr($line, strpos($line, $ipDelim)+strlen($ipDelim));
                        $ip   = trim(substr($ip, 0, strpos($ip, " ")));
                        // add the ip to the collection
                        if (!isset($ips[$ip])) {
                            $ips[$ip] = $ip;
                        }
                    }
                }
            }
        }

        // if the conf has returned nothing
        // attempt to use the $server data
        if (isset($this->serverVars['SERVER_NAME'])) {
            $ip = gethostbyname($this->serverVars['SERVER_NAME']);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
        }
        if (isset($this->serverVars['SERVER_ADDR'])) {
            $name = gethostbyaddr($this->serverVars['SERVER_ADDR']);
            $ip   = gethostbyname($name);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
            // if the $server addr is not the same as the returned ip include it aswell
            if (isset($addr) && $addr != $this->serverVars['SERVER_ADDR']) {
                if (!isset($ips[$this->serverVars['SERVER_ADDR']])) {
                    $ips[$this->serverVars['SERVER_ADDR']] = $this->serverVars['SERVER_ADDR'];
                }
            }
        }
        // count return ips and return if found
        if (count($ips) > 0) {
            return $ips;
        }
        // failed to find an ip check for conf error or return 404
        if ($conf == 'SAFE_MODE' || $conf == 'ERROR_OPEN') {
            return $conf;
        }
        return 'IP_404';
    }

    /**
    * getMacAddress
    *
    * Used to get the MAC address of the host server. It works with Linux,
    * Darwin (Mac OS X), and Win XP. It may work with others as some other
    * os's have similar ifconfigs to Darwin but they haven't been tested
    *
    * @access private
      * @return string Mac address if found
      * @return string ERROR_OPEN means config can't be found and thus not opened
      * @return string MAC_404 means mac adress doesn't exist in the config file
      * @return string SAFE_MODE means server is in safe mode so config can't be read
    **/
    protected function getMacAddress()
    {
        // open the config file
        $conf = $this->getConfig();

        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3)=='win') {
            // explode the conf into lines to search for the mac
            $lines = explode(PHP_EOL, $conf);
            // seperate the lines for analysis
            foreach ($lines as $key => $line) {
                // check for the mac signature in the line
                // originally the check was checking for the existence of string 'physical address'
                // however Gert-Rainer Bitterlich pointed out this was for english language
                // based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
                if (preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
                    $trimmedLine = trim($line);
                    // take of the mac addres and return
                    return trim(substr($trimmedLine, strrpos($trimmedLine, " ")));
                }
            }
        } else {
            // get the mac delim
            $macDelim = $this->getOsVar('mac', $os);

            // get the pos of the os_var to look for
            $pos = strpos($conf, $macDelim);
            if ($pos) {
                // seperate out the mac address
                $str1 = trim(substr($conf, ($pos+strlen($macDelim))));
                return trim(substr($str1, 0, strpos($str1, "\n")));
            }
        }
        // failed to find the mac address
        return 'MAC_404';
    }

    /**
     * getServerInfo
     *
     * used to generate the server binds when server binding is needed.
     *
     * @return array server bindings
     * @return boolean false means that the number of bindings failed to
     *      meet the required number
     **/
    protected function getServerInfo()
    {
        if (empty($this->serverVars)) {
            $this->setServerVars($server);
        }
        // get the server specific uris
        $a = array();
        if (isset($this->serverVars['SERVER_ADDR']) && (!strrpos($this->serverVars['SERVER_ADDR'], '127.0.0.1') || $this->allowLocal)) {
            $a['SERVER_ADDR'] = $this->serverVars['SERVER_ADDR'];
        }
        // corrected by Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>, Thanks
        if (isset($this->serverVars['HTTP_HOST']) && (!strrpos($this->serverVars['HTTP_HOST'], '127.0.0.1') || $this->allowLocal)) {
            $a['HTTP_HOST'] =  $this->serverVars['HTTP_HOST'];
        }
        if (isset($this->serverVars['SERVER_NAME'])) {
            $a['SERVER_NAME'] =  $this->serverVars['SERVER_NAME'];
        }
        if (isset($this->serverVars['PATH_TRANSLATED'])) {
            $a['PATH_TRANSLATED'] = substr($this->serverVars['PATH_TRANSLATED'], 0, strrpos($this->serverVars['PATH_TRANSLATED'], '/'));
        } else if (isset($this->serverVars['SCRIPT_FILENAME'])) {
            $a['SCRIPT_FILENAME'] =  substr($this->serverVars['SCRIPT_FILENAME'], 0, strrpos($this->serverVars['SCRIPT_FILENAME'], '/'));
        }
        if (isset($server['SCRIPT_URI'])) {
            $a['SCRIPT_URI'] =  substr($this->serverVars['SCRIPT_URI'], 0, strrpos($this->serverVars['SCRIPT_URI'], '/'));
        }

        // if the number of different uris is less than the required amount,
        // fail the request
        if (count($a) < $this->requiredUris) {
            return 'SERVER_FAILED';
        }

        return $a;
    }

    /**
     * callHome
     *
     * calls the dial home server (your server) andvalidates the clients license
     * with the info in the mysql db
     *
     * @param array  $data     Array that contains the info to be validated
     * @param string $dialhost Host name of the server to be contacted
     * @param string $dialpath Path of the script for the data to be sent to
     * @param number $dialport Port Number to send the data through
     * 
     * @return string Returns: the encrypted server validation result from the dial home call
     *                       : SOCKET_FAILED    => socket failed to connect to the server
     **/
    protected function callHome($data, $dialhost, $dialpath, $dialport)
    {
        // post the data home
        $data = $this->post_data($dialhost, $dialpath, $data, $dialport);
        return (empty($data['RESULT'])) ? 'SOCKET_FAILED' : $data['RESULT'];
    }


    /**
     * writeKey
     *
     * writes the key
     *
     * @param string $key      The key string
     * @param type   $filePath The path of the file
     * 
     * @return boolean Returns boolean on success
     **/
    public function writeKey($key, $filePath)
    {
        // open the key file for writeing and truncate
        $h = fopen($filePath, 'w');
        // if write fails return error
        if (fwrite($h, $key) === false) {
            return false;
        }
        // close file
        fclose($h);
        // return key
        return true;
    }

    /**
     * registerInstall
     *
     * registers the install with the home server and if registration is
     * excepted it then generates and installs the key.
     *
     * @param string $domain   The domain to register the license to
     * @param number $start    The start time of the license, can be either
     *                         the actuall time or the time span until the license is valid
     * @param number $expireIn Number of seconds untill the license
     *                         expires after start, or 'NEVER' to never expire
     * @param array  $data     Array that contains the info to be validated
     * @param string $dialhost Host name of the server to be contacted
     * @param string $dialpath Path of the script for the data to be sent to
     * @param number $dialport Port Number to send the data through
     * 
     * @return string Returns the encrypted install validation
     **/
    public function registerInstall($domain, $start, $expireIn, $data, $dialhost, $dialpath, $dialport='80')
    {
        // check to see if the class has been secured
        $this->check_secure();
        // check if key is alread generated

        // TODO
        if (@filesize($this->licensePath) > 4) {
            return array('RESULT'=>'KEY_EXISTS');
        }

        $data = array('DATA'=>$data);

        // if the server matching is required then get the info
        if ($this->useServer) {
            // evaluate the supplied domain against the collected ips
            if (!$this->compareDomainIp($domain, $this->ips)) {
                return array('RESULT'=>'DOMAIN_IP_FAIL');
            }
            // check server uris
            if (count($this->serverInfo) < $this->requiredUris) {
                return array('RESULT'=>'SERVER_FAIL');
            }

            $data['SERVER']['MAC']    = $this->mac;
            $data['SERVER']['PATH']   = $this->serverInfo;
            $data['SERVER']['IP']     = $this->ips;
            $data['SERVER']['DOMAIN'] = $domain;
        }

        // if use time restrictions
        if ($this->useTime) {
            $current = time();
            $start   = ($current < $start) ? $start : $current+$start;
            // set the dates
            $data['DATE']['START'] = $start;
            if ($expireIn == 'NEVER') {
                $data['DATE']['SPAN']   = '~';
                $data['DATE']['END']   = 'NEVER';
            } else {
                $data['DATE']['SPAN']   = $expireIn;
                $data['DATE']['END']   = $start+$expireIn;
            }
        }

        // includethe id for requests
        $data['ID'] = md5($this->id2);

        // post the data home
        $data = $this->post_data($dialhost, $dialpath, $data, $dialport);
        // return the result and key if approved
        return (empty($data['RESULT'])) ? array('RESULT'=>'SOCKET_FAILED') : $data;
    }

    /**
     * generate
     *
     * generates the server key when the license class resides on the server
     *
     * @param string $domain     The domain to bind the license to.
     * @param number $start      The number of seconds untill the key is valid
     *                           if the value is 0 then the current value given by time() is
     *                           used as the start date.
     * @param number $expireIn   The number of seconds the key will be valid
     *                           for (the default reverts to 31449600 - 1 year)
     * @param array  $otherArray An array that can contain any other data you
     *                           want to store in the key
     * 
     * @return string Key string
     * @return string KEY_EXISTS     - key has already been written and thus can't write
     *                DOMAIN_IP_FAIL - means the domain name supplied doesn't match the corresponding ip
     *                SERVER_FAIL    - enough server vars failed to be found
     **/
    public function generate($domain='', $start=0, $expireIn=31449600, $otherArray=array())
    {

        // if the URIS returned are false it means that there has not been
        // enough unique data returned by the $server so cannot generate key
        if ($this->serverInfo !== false || !$this->useServer) {
            // set the id
            $data['ID']         = md5($this->id1);

            // set server binds
            if ($this->useServer) {
                // evaluate the supplied domain against the collected ips
                if (!$this->compareDomainIp($domain, $this->ips)) {
                    return 'DOMAIN_IP_FAIL';
                }

                // set the domain
                $data['SERVER']['DOMAIN'] = $domain;
                // set the mac id
                $data['SERVER']['MAC']    = $this->mac;
                // set the server arrays
                $data['SERVER']['PATH']   = $this->serverInfo;
                // set the ip arrays
                $data['SERVER']['IP']     = $this->ips;
            }

            // set time binds
            if ($this->useTime && !is_array($start)) {
                $current = time();
                $start   = ($current < $start) ? $start : $current+$start;
                // set the dates
                $data['DATE']['START'] = $start;
                $data['DATE']['SPAN']  = $expireIn;
                if ($expireIn == 'NEVER') {
                    $data['DATE']['END']   = 'NEVER';
                } else {
                    $data['DATE']['END']   = $start+$expireIn;
                }
            }

            // if start is array then it is the other array and time binding is not in use
            // convert to other array
            if (is_array($start)) {
                $otherArray = $start;
            }

            // set the server os
            $otherArray['_PHP_OS'] = PHP_OS;

            // set the server os
            $otherArray['_PHP_VERSION'] = PHP_VERSION;

            // merge the data with the other array
            $data['DATA'] = $otherArray;

            // encrypt the key
            $key = $this->wrapLicense($data);


            // return the key
            return $key;
        }
        // no key can be generated so returns false
        return 'SERVER_FAIL';

    }

}
