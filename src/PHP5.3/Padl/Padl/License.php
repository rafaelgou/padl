<?php
namespace Padl;
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
* @link http://padl.rgou.net
* @link http://www.buggedcom.co.uk/
* @link http://www.phpclasses.org/browse/package/2298.html
* @author Oliver Lillie, buggedcom <publicmail at buggedcom dot co dot uk>
* @author Rafael Goulart <rafaelgou at gmail.com>
* @history---------------------------------------------
* see CHANGELOG
*/
class License {
  
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
    protected $HASH_KEY1   = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35BDFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEzGDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHT';
    protected $HASH_KEY2   = '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYDU52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wohX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4a';
    protected $HASH_KEY3   = 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W66qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62T';
     
    /**
    * You may not want to use mcrypt even if your system has it installed
    * make this false to use a regular encryption method
    *
    * @var boolean
    */
    protected $USE_MCRYPT  = true;

    /**
    * The algorythm to be used by mcrypt
    *
    * @var string
    */
    protected $ALGORITHM  = 'blowfish';

    /**
    * use time binding vars inited.
    */
    protected $USE_TIME;
    
    /**
    * time checking start period difference allowance ie if the user has slightly different time 
    * setting on their server make an allowance for the diff period. carefull to not make it too 
    * much otherwise they could just reset their server to a time period before the license expires.
    *
    * @var number (seconds)
    */
    protected $START_DIF    = 129600;  
    
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
    protected $ID1      = 'nSpkAHRiFfM2hE588eB';
    protected $ID2      = 'NWCy0s0JpGubCVKlkkK';
    protected $ID3      = 'G95ZP2uS782cFey9x5A';

    /**
    * begining and end strings
    *
    * @var strings
    */
    protected $BEGIN1    = 'BEGIN LICENSE KEY';
    protected $END1      = 'END LICENSE KEY';

    /**
    * wrap key settings
    *
    * @var number
    * @var string
    * @var string
    */
    protected $_WRAPTO    = 80;
    protected $_PAD      = "-";
    
    /**
    * init the linebreak var
    */
    protected $_LINEBREAK;
    
    /**
    * dial home return query deliminators
    *
    * @var string
    * @var string
    */
    protected $BEGIN2     = '_DATA{';
    protected $END2       = '}DATA_';
      
    /**
    * init the key data array.
    *
    * @var array
    */
    protected $_DATA      = array();

    /**
    * use server binding vars inited.
    */
    protected $USE_SERVER;
    protected $_SERV;
    protected $_MAC;
    protected $ALLOW_LOCAL;
    protected $_SERVER_INFO = array();
    protected $_SERVER_VARS = array();
    protected $_IPS = array();
    
    
    /**
    * this is the number of required server stats for the key generation to be successfull
    * if the server can't produce this number of details then the key fails to be generated
    * you can set it to however many you wish, the max is 5
    *
    * @var number
    */
    protected $REQUIRED_URIS  = 2;

    /**
    * the date string for human readable format
    *
    * @var string
    */
    protected $DATE_STRING  = 'M/d/Y H:i:s';
    
    /**
    * The number of allowed differences between the $_SERVER vars and the vars
    * stored in the key
    *
    * @var number
    */
    protected $_ALLOWED_SERVER_DIFS  = 0;
    
    /**
    * The number of allowed differences between the $ip vars in the key and the ip
    * vars collected from the server
    *
    * @var number
    */
    protected $_ALLOWED_IP_DIFS    = 0;

    /**
    * Constructor
    *
    * @access public 
    * @param $use_mcrypt boolean Determines if mcrypt encryption is used or not (defaults to true, 
    *           however if mcrypt is not available, it is set to false) 
    * @param $use_time boolean Sets if time binding should be used in the key (defaults to true) 
    * @param $use_server boolean Sets if server binding should be used in the key (defaults to true) 
    * @param $allow_local boolean Sets if server binding is in use then localhost servers are valid (defaults to false) 
    **/
    public function __construct($use_mcrypt=true, $use_time=true, $use_server=true, $allow_local=false)
    {
      // check to see if the class has been secured
//      $this->_check_secure();
      $this->init($use_mcrypt, $use_time, $use_server, $allow_local);
      if($this->USE_SERVER)
      {
        $this->_MAC  = $this->_get_mac_address();
      }
    }

    /**
    * init
    *
    * init the license class
    *
    * @access public 
    * @param $use_mcrypt boolean Determines if mcrypt encryption is used or not (defaults to true, 
    *           however if mcrypt is not available, it is set to false) 
    * @param $use_time boolean Sets if time binding should be used in the key (defaults to true) 
    * @param $use_server boolean Sets if server binding should be used in the key (defaults to true) 
    * @param $allow_local boolean Sets if server binding is in use then localhost servers are valid (defaults to false) 
    **/
    public function init($use_mcrypt=true, $use_time=true, $use_server=true, $allow_local=false)
    {
//        // check to see if the class has been secured
//        $this->_check_secure();
        $this->USE_MCRYPT  = ($use_mcrypt && function_exists('mcrypt_generic'));
        $this->USE_TIME    = $use_time;
        $this->ALLOW_LOCAL = $allow_local;
        $this->USE_SERVER  = $use_server;
        $this->_LINEBREAK  = $this->_get_os_linebreak();
    }
    
    /**
    * set_server_vars
    *
    * to protect against spoofing you should copy the $_SERVER vars into a
    * separate array right at the first line of your script so parameters can't 
    * be changed in unencoded php files. This doesn't have to be set. If it is
    * not set then the $_SERVER is copied when _get_server_info (private) function
    * is called.
    *
    * @access public 
    * @param $array array The copied $_SERVER array
    **/
    public function set_server_vars($array)
    {
//      // check to see if the class has been secured
//      $this->_check_secure();
      $this->_SERVER_VARS = $array;
      // some of the ip data is dependant on the $_SERVER vars, so update them
      // after the vars have been set
      $this->_IPS      = $this->_get_ip_address();
      // update the server info
      $this->_SERVER_INFO  = $this->_get_server_info();
    }

    /**
     * Validate a license
     * 
     * @param  string $license
     * @return array
     */
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
     * @return type 
     */
    public function validateRemote ($license, $dialhost, $dialpath, $dialport="80") 
    {
        return $this->doValidate($license, true, $dialhost, $dialpath, $dialport);
    }

    /**
    * doValidate
    *
    * validates the server key and returns a data array. 
    *
    * @access public 
      * @return array Main object in array is 'RESULT', it contains the result
      *     of the validation.
      *     OK     - key is valid
      *     CORRUPT   - key has been tampered with
      *     TMINUS   - the key is being used before the valid start date
      *     EXPIRED   - the key has expired
      *     ILLEGAL   - the key is not on the same server the license was registered to
      *     ILLEGAL_LOCAL   - the key is not allowed to be installed on a local machine
      *     INVALID   - the the encryption key used to encrypt the key differs or the key is not complete
      *     EMPTY     - the the key is empty
      *     404     - the the key is missing
    **/
    protected function doValidate($license, $dialhome=false, $dialhost="", $dialpath="", $dialport="80")
    {
//      // check to see if the class has been secured
//      $this->_check_secure();

      if(strlen($license)>0)
      {
        // decrypt the data
        $DATA = $this->_unwrap_license($license);
        if(is_array($DATA))
        {  
          // missing / incorrect id therefore it has been tampered with
          if($DATA['ID'] != md5($this->ID1))
          {
            $DATA['RESULT'] = 'CORRUPT';
          }
          if($this->USE_TIME)
          {
            // the license is being used before it's official start
            if($DATA['DATE']['START'] > time()+$this->START_DIF)
            {
              $DATA['RESULT'] = 'TMINUS';
            }
            // the license has expired
            if($DATA['DATE']['END']-time() < 0 && $DATA['DATE']['SPAN'] != 'NEVER')
            {
              $DATA['RESULT'] = 'EXPIRED';
            }
            $DATA['DATE']['HUMAN']['START'] = date($this->DATE_STRING, $DATA['DATE']['START']);
            $DATA['DATE']['HUMAN']['END']   = date($this->DATE_STRING, $DATA['DATE']['END']);
          }
          if($this->USE_SERVER)
          {
            $mac     = $DATA['SERVER']['MAC'] == $this->_MAC;
            $path    = count(array_diff($this->_SERVER_INFO, $DATA['SERVER']['PATH'])) <= $this->_ALLOWED_SERVER_DIFS;
            $domain  = $this->_compare_domain_ip($DATA['SERVER']['DOMAIN'], $this->_IPS);
            $ip      = count(array_diff($this->_IPS, $DATA['SERVER']['IP'])) <= $this->_ALLOWED_IP_DIFS;
            
            // the server details
            if(!$mac || !$path || !$domain || !$ip)
            {
              $DATA['RESULT'] = 'ILLEGAL';
            }
            
            // check if local
            $local     = $this->ALLOW_LOCAL && (in_array('127.0.0.1', $DATA['SERVER']['IP']) || $DATA['PATH']['SERVER_ADDR'] == '127.0.0.1' || $DATA['PATH']['HTTP_HOST'] == '127.0.0.1');
            if(!$local)
            {
              $DATA['RESULT'] = 'ILLEGAL_LOCAL';
            }
          }
          // passed all current test so license is ok
          if(!isset($DATA['RESULT']))
          {
            // dial to home server if required
            if($dialhome)
            {
              // create the details to send to the home server
              $stuff_to_send = array();
              $stuff_to_send['LICENSE_DATA'] = $DATA;
              $stuff_to_send['LICENSE_DATA']['KEY'] = md5($license);
              // dial home
              $DATA['RESULT'] = $this->_call_home($stuff_to_send, $dialhost, $dialpath, $dialport);
            }
            else
            {
              // result is ok all test passed, license is legal
              $DATA['RESULT'] = 'OK';
            }
          }
        /*
          */
          // data is returned for use
          return $DATA;
        }
        else
        {
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
     * @param string $date_format 
     */
    public function setDateFormat($date_format)
    {
        $this->DATE_STRING = $date_format;
    }
    
    /**
    * _get_os_linebreak
    *
    * get's the os linebreak
    *
    * @access private 
    * @param $true_val boolean If the true value is needed for writing files, make true
    *              defaults to false
      * @return string Returns the os linebreak
    **/
    protected function _get_os_linebreak($true_val=false)
    {
        $os = strtolower(PHP_OS);
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
          // linux variation
          // tested on server
          case 'linux' : 
              $nl = "\n";
              break;
          // darwin is mac os x
          // tested only on the client os
          case 'darwin' : 
              // note os x has \r line returns however it appears that the ifcofig
              // file used to source much data uses \n. let me know if this is
              // just my setup and i will attempt to fix.
              if($true_val) $nl = "\r";
              else $nl = "\n";
              break;
          // defaults to a win system format;
          default :
              $nl = "\r\n";
        }
        return $nl;
    }
    
    /**
    * _post_data
    *
    * Posts data to and recieves data from dial home server. Returned info
    * contains the dial home validation result
    *
    * @access private 
    * @param $host string Host name of the server to be contacted
    * @param $path string Path of the script for the data to be sent to
    * @param $query_array array Array that contains the license key info to be validated
    * @param $port number Port Number to send the data through
      * @return array Result of the dialhome validation
      * @return string - SOCKET_FAILED will be returned if it was not possible to open a socket to the home server
    **/
    protected function _post_data($host, $path, $query_array, $port=80)
    {
        // generate the post query info
        $query    = 'POSTDATA='.$this->_encrypt($query_array, 'HOMEKEY');
        $query    .= '&MCRYPT='.$this->USE_MCRYPT;
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
        if(!$header)
        {
            // if the socket fails return failed
            return array('RESULT'=>'SOCKET_FAILED');
        }
        @fputs($header, $post);
        // read the returned data
        while (!@feof($header))
        {
            $return .= @fgets($header, 1024);
        }
        fclose($header);
        
        // seperate out the data using the delims
        $leftpos = strpos($return, $this->BEGIN2)+strlen($this->BEGIN2);
        $rightpos = strpos($return, $this->END2)-$leftpos;

        // decrypt and return the data
        return $this->_decrypt(substr($return, $leftpos, $rightpos), 'HOMEKEY');
    }

    /**
    * _compare_domain_ip
    *
    * uses the supplied domain in the key and runs a check against the collected
    * ip addresses. If there are matching ips it returns true as the domain
    * and ip address match up
    *
    * @access private 
      * @return boolean
    **/
    protected function _compare_domain_ip($domain, $ips=false)
    {
        // if no ips are supplied get the ip addresses for the server
        if(!$ips) $ips = $this->_get_ip_address();
        // get the domain ip list
        $domain_ips = gethostbynamel($domain);
        // loop through the collected ip's searching for matches against the domain ips
        if(is_array($domain_ips) && count($domain_ips) > 0)
        {
            foreach($domain_ips as $ip)
            {
                if(in_array($ip, $ips)) return true;
            }
        }
        return false;
    }

    /**
    * _pad
    *
    * pad out the begin and end seperators
    *
    * @access private 
    * @param $str string The string to be padded
      * @return string Returns the padded string
    **/
    protected function _pad($str)
    {
        $str_len   = strlen($str);
        $spaces   = ($this->_WRAPTO-$str_len)/2;
        $str1 = '';
        for($i=0; $i<$spaces; $i++)
        {
            $str1 = $str1.$this->_PAD;
        }
        if($spaces/2 != round($spaces/2))
        {
            $str = substr($str1, 0, strlen($str1)-1).$str;
        }
        else
        {
            $str = $str1.$str;
        }
        $str = $str.$str1;
        return $str;
    }
    
    /**
    * _get_key
    *
    * gets the hash key for the current encryption
    *
    * @access private 
    * @param $key_type string The license key type being produced
      * @return string Returns the hash key
    **/
    protected function _get_key($key_type)
    {
        switch($key_type)
        {
            case 'KEY' :
                return $this->HASH_KEY1;
            case 'REQUESTKEY' :
                return $this->HASH_KEY2;
            case 'HOMEKEY' :
                return $this->HASH_KEY3;
            default :
            // TODO missing default return!!
        }
    }

    /**
    * _get_begin
    *
    * gets the begining license key seperator text
    *
    * @access private 
    * @param $key_type string The license key type being produced
      * @return string Returns the begining string
    **/
    protected function _get_begin($key_type)
    {
        switch($key_type)
        {
            case 'KEY' :
                return $this->BEGIN1;
            case 'REQUESTKEY' :
                return $this->BEGIN2;
            case 'HOMEKEY' :
                return '';
        }
    }
    
    /**
    * _get_end
    *
    * gets the ending license key seperator text
    *
    * @access private 
    * @param $key_type string The license key type being produced
      * @return string Returns the ending string
    **/
    protected function _get_end($key_type)
    {
        switch($key_type)
        {
            case 'KEY' :
                return $this->END1;
            case 'REQUESTKEY' :
                return $this->_END2;
            case 'HOMEKEY' :
                return '';
        }
    }
    
    /**
    * _generate_random_string
    *
    * generates a random string
    *
    * @access private 
    * @param $length number The length of the random string
    * @param $seeds string The string to pluck the characters from
      * @return string Returns random string
    **/
    protected function _generate_random_string($length=10, $seeds='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890123456789')
    {
        $str = '';
        $seeds_count = strlen($seeds);
      
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);
      
        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seeds_count - 1)};
        }
        return $str;
    }
    
    /**
    * _encrypt
    *
    * encrypts the key
    *
    * @access private 
    * @param $src_array array The data array that contains the key data
      * @return string Returns the encrypted string
    **/
    protected function _encrypt($src_array, $key_type='KEY')
    {
//        // check to see if the class has been secured
//        $this->_check_secure();
        
        $rand_add_on = $this->_generate_random_string(3);
        // get the key
        $key   = $this->_get_key($key_type);
        $key   = $rand_add_on . $key;
        
        // check to see if mycrypt exists
        if($this->USE_MCRYPT)
        {
            // openup mcrypt
            $td   = mcrypt_module_open($this->ALGORITHM, '', 'ecb', '');
            $iv   = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            // process the key
            $key   = substr($key, 0, mcrypt_enc_get_key_size($td));
            // init mcrypt
            mcrypt_generic_init($td, $key, $iv);
            
            // encrypt data
            // double base64 gets makes all the characters alpha numeric 
            // and gets rig of the special characters
            $crypt   = mcrypt_generic($td, serialize($src_array));
          
            // shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        }
        else
        {
            // if mcrypt doesn't exist use regular encryption method
            // init the vars
            $crypt = '';
            $str = serialize($src_array);
            
            // loop through the str and encrypt it
            for($i=1; $i<=strlen($str); $i++)
            {
                $char     = substr($str, $i-1, 1);
                $keychar   = substr($key, ($i % strlen($key))-1, 1);
                $char     = chr(ord($char)+ord($keychar));
                $crypt    .= $char;
            }
        }
        // return the key
        return $rand_add_on.base64_encode(base64_encode(trim($crypt)));
    }
    
    /**
    * _decrypt
    *
    * decrypts the key
    *
    * @access private 
    * @param $enc_string string The key string that contains the data
      * @return array Returns decrypted array
    **/
    protected function _decrypt($str, $key_type='KEY')
    {
//        // check to see if the class has been secured
//        $this->_check_secure();
        
        $rand_add_on = substr($str, 0, 3);
        $str = base64_decode(base64_decode(substr($str, 3)));
        // get the key
        $key   = $rand_add_on . $this->_get_key($key_type);
        
        // check to see if mycrypt exists
        if($this->USE_MCRYPT)
        {
            // openup mcrypt
            $td   = mcrypt_module_open($this->ALGORITHM, '', 'ecb', '');
            $iv   = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            // process the key
            $key   = substr($key, 0, mcrypt_enc_get_key_size($td));
            // init mcrypt
            mcrypt_generic_init($td, $key, $iv);
      
            // decrypt the data and return
            $decrypt = @mdecrypt_generic($td, $str);
            
            // shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        }
        else
        {
            // if mcrypt doesn't exist use regular decryption method
            // init the decrypt vars
            $decrypt   = '';

            // loop through the text and decode the string
            for($i=1; $i<=strlen($str); $i++)
            {
                $char     = substr($str, $i-1, 1);
                $keychar   = substr($key, ($i % strlen($key))-1, 1);
                $char     = chr(ord($char)-ord($keychar));
                $decrypt   .= $char;
            }
        }
        // return the key
        return @unserialize($decrypt);
    }
    
    /**
    * _wrap_license
    *
    * wraps up the license key in a nice little package
    *
    * @access private 
    * @param $src_array array The array that needs to be turned into a license str
    * @param $key_type string The type of key to be wrapped (KEY=license key, REQUESTKEY=license request key)
      * @return string Returns encrypted and formatted license key
    **/
    protected function _wrap_license($src_array, $key_type='KEY')
    {
        // sort the variables
        $begin   = $this->_pad($this->_get_begin($key_type));
        $end   = $this->_pad($this->_get_end($key_type));
        
        // encrypt the data
        $str   = $this->_encrypt($src_array, $key_type);
        
        // return the wrap
        return $begin.$this->_LINEBREAK.wordwrap($str, $this->_WRAPTO, $this->_LINEBREAK, 1).$this->_LINEBREAK.$end;
    }
    
    /**
    * _unwrap_license
    *
    * unwraps license key back into it's data array
    *
    * @access private 
    * @param $enc_str string The encrypted license key string that needs to be decrypted
    * @param $key_type string The type of key to be unwrapped (KEY=license key, REQUESTKEY=license request key)
      * @return array Returns license data array
    **/
    protected function _unwrap_license($enc_str, $key_type='KEY')
    {
        
        // sort the variables
        $begin   = $this->_pad($this->_get_begin($key_type));
        $end   = $this->_pad($this->_get_end($key_type));
        
        // get string without seperators
        $str   = trim(str_replace(array($begin, $end, "\r", "\n", "\t"), '', $enc_str));

        // decrypt and return the key
        return $this->_decrypt($str, $key_type);
    }
    
//    /**
//    * make_secure
//    *
//    * deletes all class values to prevent re-writing of a key;
//    *
//    * @access public 
//    **/
//    public function make_secure($report=false)
//    {
//        if($report) define('_PADL_REPORT_ABUSE_', true);
//        // walkthrough and delete the class vars
//        foreach(array_keys(get_object_vars($this)) as $value)
//        {
//            unset($this->$value);
//        }
//        // define that class is secure
//        define('_PADL_SECURE_', true);
//    }
    
//    /**
//    * _check_secure
//    *
//    * checks to see if the class has been made secure
//    *
//    * @access private 
//    **/
//    protected function _check_secure()
//    {
//        // check to see if padl has been made secure
//        if(defined('_PADL_SECURE_')) 
//        {  
//            // if(defined('_PADL_REPORT_ABUSE_')) $this->_post_data($this->_HOST, $this->_PATH, array());
//            // trigger the error because user has attempted to access secured functions
//            // after the call has been made to 'make_secure'
//            throw new \Exception(
//              'The PHP Application Distribution License System (PADL) has been made secure.' .
//              'You have attempted to use functions that have been protected and this has terminated your script.', 
//              500
//            );
//            exit;
//        }
//    }

    /**
    * _get_os_var
    *
    * gets various vars depending on the os type 
    *
    * @access private 
      * @return string various values
    **/
    protected function _get_os_var($var_name, $os)
    {
      $var_name = strtolower($var_name);
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
          switch($var_name)
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
          switch($var_name)
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
    * _get_config
    *
    * gets the server config file and returns it. tested on Linux, 
    * Darwin (Mac OS X), and Win XP. It may work with others as some other
    * os's have similar ifconfigs to Darwin but they haven't been tested
    *
    * @access private 
      * @return string config file data
    **/
    protected function _get_config()
    {
//      // check to see if the class has been secured
//      $this->_check_secure();
      if(ini_get('safe_mode'))
      {
        // returns invalid because server is in safe mode thus not allowing 
        // sbin reads but will still allow it to open. a bit weird that one.
        return 'SAFE_MODE';
      }
      // if anyone has any clues for windows environments
      // or other server types let me know
      $os = strtolower(PHP_OS);
      if(substr($os, 0, 3)=='win')
      {
        // this windows version works on xp running apache 
        // based server. it has not been tested with anything
        // else, however it should work with NT, and 2000 also
        
        // execute the ipconfig
        @exec('ipconfig/all', $lines);
        // count number of lines, if none returned return MAC_404
        // thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
        if(count($lines) == 0) return 'ERROR_OPEN';
        // $path the lines together
        $conf = implode($this->_LINEBREAK, $lines);
      }
      else
      {
        // get the conf file name
        $os_file = $this->_get_os_var('conf', $os);
        // open the ipconfig
        $fp = @popen($os_file, "rb");
        // returns invalid, cannot open ifconfig
        if (!$fp) return 'ERROR_OPEN';
        // read the config
        $conf = @fread($fp, 4096);
        @pclose($fp);
      }
      return $conf;
    }
    
    /**
    * _get_ip_address
    *
    * Used to get the MAC address of the host server. It works with Linux,
    * Darwin (Mac OS X), and Win XP. It may work with others as some other
    * os's have similar ifconfigs to Darwin but they haven't been tested
    *
    * @access private 
      * @return array IP Address(s) if found (Note one machine may have more than one ip)
      * @return string ERROR_OPEN means config can't be found and thus not opened
      * @return string IP_404 means ip adress doesn't exist in the config file and can't be found in the $_SERVER
      * @return string SAFE_MODE means server is in safe mode so config can't be read
    **/
    protected function _get_ip_address()
    {
      $ips = array();
      // get the cofig file
      $conf = $this->_get_config();
      // if the conf has returned and error return it
      if($conf != 'SAFE_MODE' && $conf != 'ERROR_OPEN')
      {
        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if(substr($os, 0, 3)=='win')
        {
          // anyone any clues on win ip's
        }
        else
        {
          // explode the conf into seperate lines for searching
          $lines = explode($this->_LINEBREAK, $conf);
          // get the ip delim
          $ip_delim = $this->_get_os_var('ip', $os);
          
          // ip pregmatch 
          $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
          // seperate the lines
          foreach ($lines as $key=>$line)
          {
            // check for the ip signature in the line
            if(!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ip_delim)) 
            {
              // seperate out the ip
              $ip   = substr($line, strpos($line, $ip_delim)+strlen($ip_delim));
              $ip   = trim(substr($ip, 0, strpos($ip, " ")));
              // add the ip to the collection
              if(!isset($ips[$ip])) $ips[$ip] = $ip;
            }
          }
        }
      }
      
      // if the conf has returned nothing
      // attempt to use the $_SERVER data
      if(isset($this->_SERVER_VARS['SERVER_NAME']))
      {
        $ip = gethostbyname ($this->_SERVER_VARS['SERVER_NAME']);
        if(!isset($ips[$ip])) $ips[$ip] = $ip;
      }
      if(isset($this->_SERVER_VARS['SERVER_ADDR']))
      {
        $name   = gethostbyaddr ($this->_SERVER_VARS['SERVER_ADDR']);
        $ip   = gethostbyname ($name);
        if(!isset($ips[$ip])) $ips[$ip] = $ip;
        // if the $_SERVER addr is not the same as the returned ip include it aswell
        if(isset($addr) && $addr != $this->_SERVER_VARS['SERVER_ADDR'])
        {
          if(!isset($ips[$this->_SERVER_VARS['SERVER_ADDR']])) $ips[$this->_SERVER_VARS['SERVER_ADDR']] = $this->_SERVER_VARS['SERVER_ADDR'];
        }
      }
      // count return ips and return if found
      if(count($ips) > 0) return $ips;
      // failed to find an ip check for conf error or return 404
      if($conf == 'SAFE_MODE' || $conf == 'ERROR_OPEN') return $conf;
      return 'IP_404';
    }
    
    /**
    * _get_mac_address
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
    protected function _get_mac_address()
    {
      // open the config file
      $conf = $this->_get_config();
      
      // if anyone has any clues for windows environments
      // or other server types let me know
      $os = strtolower(PHP_OS);
      if(substr($os, 0, 3)=='win')
      {
        // explode the conf into lines to search for the mac
        $lines = explode($this->_LINEBREAK, $conf);
        // seperate the lines for analysis
        foreach ($lines as $key=>$line)
        {
          // check for the mac signature in the line
          // originally the check was checking for the existence of string 'physical address'
          // however Gert-Rainer Bitterlich pointed out this was for english language
          // based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
          if(preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) 
          {
            $trimmed_line = trim($line);
            // take of the mac addres and return
            return trim(substr($trimmed_line, strrpos($trimmed_line, " ")));
          }
        }
      }
      else
      {
        // get the mac delim
        $mac_delim = $this->_get_os_var('mac', $os);
        
        // get the pos of the os_var to look for
        $pos = strpos($conf, $mac_delim);
        if($pos)
        {
          // seperate out the mac address
          $str1 = trim(substr($conf, ($pos+strlen($mac_delim))));
          return trim(substr($str1, 0, strpos($str1, "\n")));
        }
      }
      // failed to find the mac address
      return 'MAC_404'; 
    }

    /**
    * _get_server_info
    *
    * used to generate the server binds when server binding is needed.
    *
    * @access private 
      * @return array server bindings
      * @return boolean false means that the number of bindings failed to 
      *      meet the required number
    **/
    protected function _get_server_info()
    {
      if(empty($this->_SERVER_VARS))
      {
        $this->set_server_vars($_SERVER);
      }
      // get the server specific uris
      $a = array();
      if(isset($this->_SERVER_VARS['SERVER_ADDR']) && (!strrpos($this->_SERVER_VARS['SERVER_ADDR'], '127.0.0.1') || $this->ALLOW_LOCAL))
      {
        $a['SERVER_ADDR'] = $this->_SERVER_VARS['SERVER_ADDR'];
      }
      // corrected by Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>, Thanks
      if(isset($this->_SERVER_VARS['HTTP_HOST']) && (!strrpos($this->_SERVER_VARS['HTTP_HOST'], '127.0.0.1') || $this->ALLOW_LOCAL))
      {
        $a['HTTP_HOST'] =  $this->_SERVER_VARS['HTTP_HOST'];
      }
      if(isset($this->_SERVER_VARS['SERVER_NAME']))
      {
        $a['SERVER_NAME'] =  $this->_SERVER_VARS['SERVER_NAME'];
      }
      if(isset($this->_SERVER_VARS['PATH_TRANSLATED']))
      {
        $a['PATH_TRANSLATED'] = substr($this->_SERVER_VARS['PATH_TRANSLATED'], 0, strrpos($this->_SERVER_VARS['PATH_TRANSLATED'], '/'));
      }
      else if(isset($this->_SERVER_VARS['SCRIPT_FILENAME']))
      {
        $a['SCRIPT_FILENAME'] =  substr($this->_SERVER_VARS['SCRIPT_FILENAME'], 0, strrpos($this->_SERVER_VARS['SCRIPT_FILENAME'], '/'));
      }
      if(isset($_SERVER['SCRIPT_URI']))
      {
        $a['SCRIPT_URI'] =  substr($this->_SERVER_VARS['SCRIPT_URI'], 0, strrpos($this->_SERVER_VARS['SCRIPT_URI'], '/'));
      }
      
      // if the number of different uris is less than the required amount,
      // fail the request
      if(count($a) < $this->REQUIRED_URIS)
      {
        return 'SERVER_FAILED';
      }
      
      return $a;

    }

    /**
    * _call_home
    *
    * calls the dial home server (your server) andvalidates the clients license
    * with the info in the mysql db
    *
    * @access private 
    * @param $data array Array that contains the info to be validated
    * @param $dialhost string Host name of the server to be contacted
    * @param $dialpath string Path of the script for the data to be sent to
    * @param $dialport number Port Number to send the data through
      * @return string Returns: the encrypted server validation result from the dial home call
      *            : SOCKET_FAILED    => socket failed to connect to the server
    **/
    protected function _call_home($data, $dialhost, $dialpath, $dialport)
    {
      // post the data home
      $data = $this->_post_data($dialhost, $dialpath, $data, $dialport);
      return (empty($data['RESULT'])) ? 'SOCKET_FAILED' : $data['RESULT'];
    }

    
    /**
    * writeKey
    *
    * writes the key 
    *
    * @access public 
    * @param $key string The key string
      * @return boolean Returns boolean on success
    **/
    public function writeKey($key, $file_path)
    {
//      // check to see if the class has been secured
//      $this->_check_secure();
      // open the key file for writeing and truncate
      $h = fopen($file_path, 'w');
      // if write fails return error
      if(fwrite($h, $key) === false) return false;
      // close file
      fclose($h);
      // return key
      return true;
    }
    
    /**
    * register_install
    *
    * registers the install with the home server and if registration is 
    * excepted it then generates and installs the key.
    *
    * @access public 
    * @param $domain string the domain to register the license to
    * @param $start number  the start time of the license, can be either 
    *     the actuall time or the time span until the license is valid
    * @param $expire_in number/string number of seconds untill the license 
    *     expires after start, or 'NEVER' to never expire
    * @param $data array Array that contains the info to be validated
    * @param $dialhost string Host name of the server to be contacted
    * @param $dialpath string Path of the script for the data to be sent to
    * @param $dialport number Port Number to send the data through
      * @return string Returns the encrypted install validation
    **/
    public function register_install($domain, $start, $expire_in, $data, $dialhost, $dialpath, $dialport='80')
    {
      // check to see if the class has been secured
      $this->_check_secure();
      // check if key is alread generated
      
      // TODO
      if(@filesize($this->_LICENSE_PATH) > 4)  return array('RESULT'=>'KEY_EXISTS');
      
      $data = array('DATA'=>$data);
      
      // if the server matching is required then get the info
      if($this->USE_SERVER)
      {
        // evaluate the supplied domain against the collected ips
        if(!$this->_compare_domain_ip($domain, $this->_IPS)) return array('RESULT'=>'DOMAIN_IP_FAIL');
        // check server uris
        if(count($this->_SERVER_INFO) < $this->REQUIRED_URIS) return array('RESULT'=>'SERVER_FAIL');
        
        $data['SERVER']['MAC']     = $this->_MAC;
        $data['SERVER']['PATH']   = $this->_SERVER_INFO;
        $data['SERVER']['IP']     = $this->_IPS;
        $data['SERVER']['DOMAIN']   = $domain;

      }
      
      // if use time restrictions
      if($this->USE_TIME)
      {
        $current           = time();
        $start            = ($current < $start) ? $start : $current+$start;
        // set the dates
        $data['DATE']['START']     = $start;
        if($expire_in == 'NEVER')
        {
          $data['DATE']['SPAN']   = '~';
          $data['DATE']['END']   = 'NEVER';
        }
        else
        {
          $data['DATE']['SPAN']   = $expire_in;
          $data['DATE']['END']   = $start+$expire_in;
        }
      }
      
      // includethe id for requests
      $data['ID'] = md5($this->ID2);

      // post the data home
      $data = $this->_post_data($dialhost, $dialpath, $data, $dialport);
      // return the result and key if approved
      return (empty($data['RESULT'])) ? array('RESULT'=>'SOCKET_FAILED') : $data;
    }

    /**
    * generate
    *
    * generates the server key when the license class resides on the server
    *
    * @access public 
    * @param $domain string The domain to bind the license to.
    * @param $start number The number of seconds untill the key is valid
    *     if the value is 0 then the current value given by time() is 
    *     used as the start date.
    * @param $expire_in number The number of seconds the key will be valid 
    *     for (the default reverts to 31449600 - 1 year)
    * @param $other_array array An array that can contain any other data you
    *     want to store in the key
      * @return string key string
      * @return string   KEY_EXISTS      - key has already been written and thus can't write
      *          DOMAIN_IP_FAIL     - means the domain name supplied doesn't match the corresponding ip
      *          SERVER_FAIL     - enough server vars failed to be found
    **/
    public function generate($domain='', $start=0, $expire_in=31449600, $other_array=array())
    {
//      // check to see if the class has been secured
//      $this->_check_secure();
//      // check if key is alread generated
//      if(@filesize($this->_LICENSE_PATH) > 4)  return 'KEY_EXISTS';
//      // check if target exists
//      if(!@file_exists($this->_LICENSE_PATH) || !@is_file($this->_LICENSE_PATH)) return 'WRITE_TARGET_404';
//      // key file doesn't exist
//      if(!@is_writeable($this->_LICENSE_PATH)) return 'WRITE_TARGET_UNWRITEABLE';
      
      // if the URIS returned are false it means that there has not been
      // enough unique data returned by the $_SERVER so cannot generate key
      if($this->_SERVER_INFO !== false || !$this->USE_SERVER)
      {
        // set the id
        $DATA['ID']         = md5($this->ID1);
        
        // set server binds
        if($this->USE_SERVER)
        {
          // evaluate the supplied domain against the collected ips
          if(!$this->_compare_domain_ip($domain, $this->_IPS)) return 'DOMAIN_IP_FAIL';
          
          // set the domain
          $DATA['SERVER']['DOMAIN'] = $domain;
          // set the mac id
          $DATA['SERVER']['MAC']    = $this->_MAC;
          // set the server arrays
          $DATA['SERVER']['PATH']   = $this->_SERVER_INFO;
          // set the ip arrays
          $DATA['SERVER']['IP']     = $this->_IPS;
        }
        
        // set time binds
        if($this->USE_TIME && !is_array($start))
        {
          $current = time();
          $start   = ($current < $start) ? $start : $current+$start;
          // set the dates
          $DATA['DATE']['START'] = $start;
          $DATA['DATE']['SPAN']  = $expire_in;
          if($expire_in == 'NEVER')
          {
            $DATA['DATE']['END']   = 'NEVER';
          }
          else
          {
            $DATA['DATE']['END']   = $start+$expire_in;
          }
        }
        
        // if start is array then it is the other array and time binding is not in use
        // convert to other array
        if(is_array($start))
        {
          $other_array = $start;
        }
        
        // set the server os
        $other_array['_PHP_OS']    = PHP_OS;  
        
        // set the server os
        $other_array['_PHP_VERSION'] = PHP_VERSION;  

        // merge the data with the other array
        $DATA['DATA']         = $other_array;

        // encrypt the key
        $key = $this->_wrap_license($DATA);
        
//        // write the key
//        if(!$this->writeKey($key)) return 'WRITE_FAILED';
        
        // return the key
        return $key;
      }
      // no key can be generated so returns false
      return 'SERVER_FAIL';
      
    }
    
}
