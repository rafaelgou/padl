# PHP Aplication Distribution Licensing

## What is PADL?

A class to generate and validate licenses for a domain, restricting
an expire date.

## Demo

See the [PADL Site](http://padl.rgou.net).

## History

PADL is a originaly written in 2005 by Oliver Lillie under older PHP4, 
parked on PHPCLASSES site. You can find the original code as part as 
this distribution or directelly in 
http://www.phpclasses.org/package/2298-PHP-Generate-PHP-application-license-keys.html .

You can see a quote about:

    There are several solutions to help developers protecting the PHP software 
    of applications that they want to sell.

    Usually the developers provide license keys with their software 
    that include information about the licensed code, enabled features, 
    authorized configuration of the environment on which it is being installed.

    This helps the developers to limit the scope of usage of their 
    software according to the type of software license that their clients purchase.

    This way the developers can use the same software distribution 
    for different configurations and prices and even trial versions.

    This class provides a means to generate license keys that include encrypted 
    information about the clients PHP environment and even a way to send the keys 
    to the developers site so they can capture the client features and generate 
    upgraded keys that can enable more features for clients that pay the upgrade price.

    Manuel Lemos (PHPCLASSES manteiner)

When I need to licence a PHP project, found this class and as it was so well written, 
it was very easy to update it to PH 5.2 and 5.3.

You can find more about the original author in:

* [PHP CLASSES: Classes of Oliver Lillie](http://www.phpclasses.org/browse/author/122732.html)
* [PHP CLASSES: Professional profile of Oliver Lillie](http://www.phpclasses.org/professionals/profile/9072/)
* Otherwise a lot of modifications has been made, all core methods and examples 
  are or just a copy or strongly based on the original project.

## PHP 5.2 and 5.3 versions

This versions are the same, but the loader are different and the 5.3 version uses namespaces.

There are some updates in the structure, class names and methods to remote validation to 
adapt to 2011 days.

## TODO 

A validator server is not yet implemented - code was not converted to PHP 5.2 and 5.3 -
but in fact it could be done very easily with a webservice.

## Usage

On `examples` directory there are a many examples of basic usage.
Run the examples under a web server.

To test under a localhost, use the parameter *allowLocal=True*.

To generate and validate the license the code *MUST BE* and *MUST RUN*
under the domain to be validated.

You can store the license in a file, database, or even remotely.

### Include the library

Autoloader for PHP 5.3

    // Register Autoload 
    include_once('PATH_TO/src/PHP5.3/Padl/Padl.php');
    Padl::registerAutoload();

Autoloader for PHP 5.2

    // Register Autoload
    include_once('../../src/PHP5.2/Padl/PadlLibrary.php');
    PadlLibrary::init();

### Generate:

    /*
    Instance of License
    parameters:
    - useMcrypt
    - useTime
    - useServer
    - allowLocal
    */
    $padl = new Padl\License(true, true, true, true);

    //For better security injecting a copy of $_SERVER global var
    $server_array = $_SERVER;
    $padl->setServerVars($server_array);

    $date_expire = '12/31/2011';
    list($month, $day, $year) = explode($date_expire);
    // Calculating the time offset (expire_in)
    $now       = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
    $dateLimit = mktime(23, 59, 59, $month, $day, $year);
    $expireIn  = $dateLimit - $now;

    // Generating a key with your server details
    $license = $padl->generate('localhost', 0, $expire_in);

    // Save the license anywhere, database, filesystem, even remotely

###  Validate

    /*
    Instance of License
    parameters used in this sample:
    - useMcrypt  = false 
    - useTime    = true
    - useServer  = false
    - allowLocal = true
    */
    $padl = new PadlLicense(true, true, true, true);

    // For better security injecting a copy of $_SERVER global var
    $server_array = $_SERVER;
    $padl->setServerVars($server_array);

    // get the license from a form, or load from database, filesystem
    $license = (... load the license ...);

    // the set key is the key validated
    $results = $padl->validate($license);