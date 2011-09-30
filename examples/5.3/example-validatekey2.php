<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Validate Key</h1>
        </div>

        <h2>Instantiation</h2>
        <pre>
// Register Autoload
include_once('../../src/PHP5.3/Padl/Padl.php');
Padl::registerAutoload();

/*
Instance of License
parameters used in this sample:
- use_mcrypt  = false 
- use_time    = true
- use_server  = false
- allow_local = true
*/
$padl = new Padl\License(false, true, false, true);
        </pre>
        
        <?php
        // Register Autoload
        include_once('../../src/PHP5.3/Padl/Padl.php');
        Padl::registerAutoload();

        $padl = new Padl\License(false, true, false, true);

        $license = 
'-------------------------------BEGIN LICENSE KEY--------------------------------
p6XMFhDTWsrakl0SXVSYjN1M2NJTEliR2VBbkdOcGIyWGRjWnV2ZDdSN2ZYbWxxNnVsbWNXOG9teHNjM
1dvcTRGb2liU0VsRmkwMW41OGdXT1VtY09LdUt1UGpxZVFlSURDdDRCOXFwVjdwNUdhanNocGY3ZXhjb
kJ4MnJ5NmxOZlNzcm0rV1lpZWo0bHNqWldtZEhtUHgzK1VoR3BiaFgyQ2VZV3FhSXlUcElhMHZicUhZN
mx3Z3Noc2FJaUVrWFNLZTV4OG1JTjVqYis1Z1hWM2dXU0lwcWkvZUllaGhZcUxvMVdVd1lsc2NKdVVkb
jExY1crRW4yV1ZsT0NOZjVCbWpwdVlscWE0d29xc1pwUzR0SUIrWnFocmJYMmVlNGRobkl4K3VZUm9mb
VYzcVhlOW5xcUR0cUtWa3FodmE2eVBmbjJDZHVDbHlzZWF5TzY2eVh1ZTRyR293VlJ2d1p4eWFIQlZ5b
0tMa2FPbmxvZVZsNUtJbG5xbGVLU21iM1Y2Z0dtQWlNeXp3YVpsNk0rd3I2eHdsTDNsbWVhSTJieXB1N
0s1ckhPc3NOellxb2VseWJXY2dyZCtxR2x3Y2NHUXFYeTd2cUtibklCdlpzaVFacUtDaDVuTHlNUi9rb
nlncU1lN2RidkkwYWlHeGR5NDI4V0d5NkhmdXJDNllhQzN4YWFocVppcHA2QzFxY1p6cDdlMGFJSy9xS
nVJbzFWK2ttYUJxSXRsazhuQ2JXK3poWFY0Zm05eGdxRmlwSHVveG9DUGZtaDRkbjExb0tGbmhuVjdnT
zJCZFhXeFcyZUlvSHVJYWFPWWMzUjlXWWllajRkanBXcVdhb21DaFh1YmUyQm5pRytEeGRIamNJeVRqN
W03clp4dmJkU0llbyt0cDRpWGUxV0poN3VUaTJ0L3ZINTNlSFYvZW5adWxvaVpicWg4ZUlCcHBJT2FuS
EZ1bjdPWmRIeDNjM1dMcG1hdXpLZUdnSGlKbEl0bWdiQ3FwRzZLZVl4OHJubDdkcksycVltaWgzbDNyT
DZFYUlXWWgxMlAwYVdsZjU5VHRxU2NsY0tjZzF1UXdJSitqcEo2dWN2a3Z1aDdrc0JzcElDQmQ1R0Vsc
ktnaVh1RnpZcUdsMmFPdDRCOWZtaDdjR2lQaDVsVmNML0I=
--------------------------------END LICENSE KEY---------------------------------';    
        ?>
        <h2>Sample License Key</h2>
        <pre><?php echo $license ?></pre>

        <h2>Validate</h2>
        <?php 
        
        // copy the server vars (important for security, see note below)
        $server_array = $_SERVER;

        // set the server vars
        // note this doesn't have to be set, however if not all of your app files are encoded
        // then there would be a possibility that the end user could modify the server vars
        // to fit the key thus making it possible to use your app on any domain
        // you should copy your server vars in the first line of your active script so you can
        // use the unmodified copy of the vars
        $padl->set_server_vars($server_array);
        
        // the set key is the key validated for my server, when run on your box it will be illegal
        $results = $padl->validate($license);
        ?>    
        
        <pre>
$license = '(... the license key ...)';    
$results = $padl->validate($license);
        </pre>

        <?php include_once('_license_messages.php') ?>

        <h2>Padl\License::validate return</h2>
        <pre><?php echo print_r($results) ?></pre>

        <h2>Possibles RESULTS</h2>
        <p>
            The <strong>RESULT</strong> key value returned by the Padl\Licence::validate method.
        </p>
        <dl>
            <dt>OK</dt>
            <dd>key is valid</dd>

            <dt>CORRUPT</dt>
            <dd>key has been tampered with</dd>

            <dt>TMINUS</dt>
            <dd>the key is being used before the valid start date</dd>

            <dt>EXPIRED</dt>
            <dd>the key has expired</dd>

            <dt>ILLEGAL</dt>
            <dd>the key is not on the same server the license was registered to</dd>

            <dt>ILLEGAL_LOCAL</dt>
            <dd>the key is not allowed to be installed on a local machine</dd>

            <dt>INVALID</dt>
            <dd>the the encryption key used to encrypt the key differs or the key is not complete</dd>

            <dt>EMPTY</dt>
            <dd>the the key is empty</dd>

            <dt>404</dt>
            <dd>the the key is missing</dd>
        </dl>
    </div> <!-- /container -->
    
<?php include_once ('_footer.php') ?>
    
  </body>
</html>
