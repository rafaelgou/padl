# PHP Aplication Distribution Licensing

## Table of Contents

* [License](#license)
    * [__construct](#__construct)
    * [init](#init)
    * [setServerVars](#setservervars)
    * [validate](#validate)
    * [validateRemote](#validateremote)
    * [setDateFormat](#setdateformat)
    * [writeKey](#writekey)
    * [registerInstall](#registerinstall)
    * [generate](#generate)

## License

Padl Licence Class
Project:   PHP Application Distribution License Class
File:      License.php

Copyright (C) 2005 Oliver Lillie
Copyright (C) 2011 Rafael Goulart

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by  the Free
Software Foundation; either version 2 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

* Full name: \Padl\License

**See Also:**

* http://padl.rgou.net * http://www.buggedcom.co.uk/ * http://www.phpclasses.org/browse/package/2298.html 

### __construct

Constructor

```php
License::__construct( boolean $useMcrypt = true, boolean $useTime = true, boolean $useServer = true, boolean $allowLocal = false ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$useMcrypt` | **boolean** | boolean Determines if mcrypt encryption is used or not (defaults to true,
                            however if mcrypt is not available, it is set to false) |
| `$useTime` | **boolean** | boolean Sets if time binding should be used in the key (defaults to true) |
| `$useServer` | **boolean** | boolean Sets if server binding should be used in the key (defaults to true) |
| `$allowLocal` | **boolean** | boolean Sets if server binding is in use then localhost servers are valid (defaults to false) |




---

### init

init
init the license class

```php
License::init( boolean $useMcrypt = true, boolean $useTime = true, boolean $useServer = true, boolean $allowLocal = false ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$useMcrypt` | **boolean** | Determines if mcrypt encryption is used or not (defaults to true,
                           however if mcrypt is not available, it is set to false) |
| `$useTime` | **boolean** | Sets if time binding should be used in the key (defaults to true) |
| `$useServer` | **boolean** | Sets if server binding should be used in the key (defaults to true) |
| `$allowLocal` | **boolean** | Sets if server binding is in use then localhost servers are valid (defaults to false) |




---

### setServerVars

setServerVars

```php
License::setServerVars( array $array ): void
```

to protect against spoofing you should copy the $server vars into a
separate array right at the first line of your script so parameters can't
be changed in unencoded php files. This doesn't have to be set. If it is
not set then the $server is copied when _getServerInfo (private) function
is called.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$array` | **array** | The copied $server array |




---

### validate

Validate a license

```php
License::validate( string $license ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$license` | **string** | The license string |




---

### validateRemote

Validate a License through a remote server

```php
License::validateRemote( string $license, string $dialhost, string $dialpath, string $dialport = &quot;80&quot; ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$license` | **string** | The license to validate |
| `$dialhost` | **string** | The host to dial |
| `$dialpath` | **string** | The path to dial |
| `$dialport` | **string** | The port of the host |




---

### setDateFormat

Sets the Date Format

```php
License::setDateFormat( string $dateFormat ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dateFormat` | **string** | The date format to use on license |




---

### writeKey

writeKey

```php
License::writeKey( string $key, \Padl\type $filePath ): boolean
```

writes the key


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string** | The key string |
| `$filePath` | **\Padl\type** | The path of the file |


**Return Value:**

Returns boolean on success



---

### registerInstall

registerInstall

```php
License::registerInstall( string $domain, \Padl\number $start, \Padl\number $expireIn, array $data, string $dialhost, string $dialpath, \Padl\number $dialport = &#039;80&#039; ): string
```

registers the install with the home server and if registration is
excepted it then generates and installs the key.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$domain` | **string** | The domain to register the license to |
| `$start` | **\Padl\number** | The start time of the license, can be either
                        the actuall time or the time span until the license is valid |
| `$expireIn` | **\Padl\number** | Number of seconds untill the license
                        expires after start, or 'NEVER' to never expire |
| `$data` | **array** | Array that contains the info to be validated |
| `$dialhost` | **string** | Host name of the server to be contacted |
| `$dialpath` | **string** | Path of the script for the data to be sent to |
| `$dialport` | **\Padl\number** | Port Number to send the data through |


**Return Value:**

Returns the encrypted install validation



---

### generate

generate

```php
License::generate( string $domain = &#039;&#039;, \Padl\number $start, \Padl\number $expireIn = 31449600, array $otherArray = array() ): string
```

generates the server key when the license class resides on the server
returns:
- KEY_EXISTS     - key has already been written and thus can't write
- DOMAIN_IP_FAIL - means the domain name supplied doesn't match the corresponding ip
- SERVER_FAIL    - enough server vars failed to be found


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$domain` | **string** | The domain to bind the license to. |
| `$start` | **\Padl\number** | The number of seconds untill the key is valid
                          if the value is 0 then the current value given by time() is
                          used as the start date. |
| `$expireIn` | **\Padl\number** | The number of seconds the key will be valid
                          for (the default reverts to 31449600 - 1 year) |
| `$otherArray` | **array** | An array that can contain any other data you
                          want to store in the key |


**Return Value:**

Key string



---



--------
> This document was automatically generated from source code comments on 2016-09-03 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
