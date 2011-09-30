# PHP Aplication Distribution Licensing

Alpha public version

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

A validator server is not yet implemented.

## Usage

On `examples` directory there are a many examples of basic usage.
