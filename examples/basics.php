<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">About PHP Aplication Distribution Licensing</h1>
        </div>

        <h2>Usage</h2>
        <p>
            On `examples` directory there are a many examples of basic usage.
            Run the examples under a web server.
        </p>
        <p>
            To test under a localhost, use the parameter *allowLocal=True*.
        </p>
        <p>
            To generate and validate the license the code *MUST BE* and *MUST RUN*
            under the domain to be validated.
        </p>
        <p>
            You can store the license in a file, database, or even remotely.
        </p>

        <h2>Include the library</h2>

        <h3>Autoloader for PHP 5.3</h3>
        <pre>
// Register Autoload 
include_once('PATH_TO/src/PHP5.3/Padl/Padl.php');
Padl::registerAutoload();
        </pre>

        <h3>Autoloader for PHP 5.2</h3>
        <pre>
// Register Autoload
include_once('../../src/PHP5.2/Padl/PadlLibrary.php');
PadlLibrary::init();
        </pre>

        <h2>Generate</h2>
        <pre>
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
        </pre>

        <h2>Validate</h2>
        <pre>
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
        </pre>
    
    </div> <!-- /container -->

<?php include_once ('_footer.php') ?>
    
  </body>
</html>
