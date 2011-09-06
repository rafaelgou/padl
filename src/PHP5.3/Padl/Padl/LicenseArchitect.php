<?php
namespace Padl;
use Padl\Padl;
use Padl\LicenseApplication;

/**
* Project:    Distrubution License Class
* File:      class.license.gen.php
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
* @version 0.5
* @history---------------------------------------------
* see CHANGELOG
*/
  
class LicenseArchitect extends LicenseApplication {
  
    /**
    * Constructor
    *
    * @access public 
    * @param $use_mcrypt boolean Determines if mcrypt encryption is used or 
    *     not (defaults to true, however if mcrypt is not available, it 
    *     is set to false) 
    * @param $use_time boolean Sets if time binding should be used in the 
    *     key (defaults to true) 
    * @param $use_server boolean Sets if server binding should be used in  
    *     the key (defaults to true) 
    * @param $allow_local boolean Sets if server binding is in use then  
    *     localhost servers are valid (defaults to false) 
    **/
    public function __constructor($license_path='license.dat', $use_mcrypt=true, $use_time=true, $use_server=true, $allow_local=false)
    {
      # check to see if the class has been secured
      $this->_check_secure();
      parent::__construct($license_path, $use_mcrypt, $use_time, $use_server, $allow_local);
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
    public function writeKey($key)
    {
      # check to see if the class has been secured
      $this->_check_secure();
      # open the key file for writeing and truncate
      $h = fopen($this->_LICENSE_PATH, 'w');
      # if write fails return error
      if(fwrite($h, $key) === false) return false;
      # close file
      fclose($h);
      # return key
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
      # check to see if the class has been secured
      $this->_check_secure();
      # check if key is alread generated
      if(@filesize($this->_LICENSE_PATH) > 4)  return array('RESULT'=>'KEY_EXISTS');
      
      $data = array('DATA'=>$data);
      
      # if the server matching is required then get the info
      if($this->USE_SERVER)
      {
        # evaluate the supplied domain against the collected ips
        if(!$this->_compare_domain_ip($domain, $this->_IPS)) return array('RESULT'=>'DOMAIN_IP_FAIL');
        # check server uris
        if(count($this->_SERVER_INFO) < $this->REQUIRED_URIS) return array('RESULT'=>'SERVER_FAIL');
        
        $data['SERVER']['MAC']     = $this->_MAC;
        $data['SERVER']['PATH']   = $this->_SERVER_INFO;
        $data['SERVER']['IP']     = $this->_IPS;
        $data['SERVER']['DOMAIN']   = $domain;

      }
      
      # if use time restrictions
      if($this->USE_TIME)
      {
        $current           = time();
        $start            = ($current < $start) ? $start : $current+$start;
        # set the dates
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
      
      # includethe id for requests
      $data['ID'] = md5($this->ID2);

      # post the data home
      $data = $this->_post_data($dialhost, $dialpath, $data, $dialport);
      # return the result and key if approved
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
      # check to see if the class has been secured
      $this->_check_secure();
      # check if key is alread generated
      if(@filesize($this->_LICENSE_PATH) > 4)  return 'KEY_EXISTS';
      # check if target exists
      if(!@file_exists($this->_LICENSE_PATH) || !@is_file($this->_LICENSE_PATH)) return 'WRITE_TARGET_404';
      # key file doesn't exist
      if(!@is_writeable($this->_LICENSE_PATH)) return 'WRITE_TARGET_UNWRITEABLE';
      
      # if the URIS returned are false it means that there has not been
      # enough unique data returned by the $_SERVER so cannot generate key
      if($this->_SERVER_INFO !== false || !$this->USE_SERVER)
      {
        # set the id
        $DATA['ID']         = md5($this->ID1);
        
        # set server binds
        if($this->USE_SERVER)
        {
          # evaluate the supplied domain against the collected ips
          if(!$this->_compare_domain_ip($domain, $this->_IPS)) return 'DOMAIN_IP_FAIL';
          
          # set the domain
          $DATA['SERVER']['DOMAIN']   = $domain;
          # set the mac id
          $DATA['SERVER']['MAC']     = $this->_MAC;
          # set the server arrays
          $DATA['SERVER']['PATH']   = $this->_SERVER_INFO;
          # set the ip arrays
          $DATA['SERVER']['IP']     = $this->_IPS;
        }
        
        # set time binds
        if($this->USE_TIME && !is_array($start))
        {
          $current           = time();
          $start            = ($current < $start) ? $start : $current+$start;
          # set the dates
          $DATA['DATE']['START']     = $start;
          $DATA['DATE']['SPAN']     = $expire_in;
          if($expire_in == 'NEVER')
          {
            $DATA['DATE']['END']   = 'NEVER';
          }
          else
          {
            $DATA['DATE']['END']   = $start+$expire_in;
          }
        }
        
        # if start is array then it is the other array and time binding is not in use
        # convert to other array
        if(is_array($start))
        {
          $other_array = $start;
        }
        
        # set the server os
        $other_array['_PHP_OS']    = PHP_OS;  
        
        # set the server os
        $other_array['_PHP_VERSION'] = PHP_VERSION;  

        # merge the data with the other array
        $DATA['DATA']         = $other_array;

        # encrypt the key
        $key = $this->_wrap_license($DATA);
        
        # write the key
        if(!$this->writeKey($key)) return 'WRITE_FAILED';
        
        # return the key
        return $key;
      }
      # no key can be generated so returns false
      return 'SERVER_FAIL';
      
    }
        
  }
  
