<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Generate Key</h1>
        </div>

        <form action="example-generatekey-php52.php#result" method="post"> 
            
            <fieldset> 

                <legend>Data for Instatiate PadlLicense Class</legend> 

                <div class="clearfix"> 
                    <label for="useMcrypt">Use MCript</label> 
                    <div class="input"> 
                        <select name="useMcrypt" id="useMcrypt"> 
                            <option value="true">true</option> 
                            <option value="false">false</option> 
                        </select>
                        <span class="help-inline">
                            Determines if mcrypt encryption is used or not (defaults to true,
                            if mcrypt is not available, it is set to false)
                        </span>
                    </div> 
                </div> 

                <div class="clearfix"> 
                    <label for="useTime">Use Time</label> 
                    <div class="input"> 
                        <select name="useTime" id="useTime"> 
                            <option value="true">true</option> 
                            <option value="false">false</option> 
                        </select>
                        <span class="help-inline">
                            Sets if time binding should be used in the key (defaults to true)
                        </span>
                    </div> 
                </div> 

                <div class="clearfix"> 
                    <label for="useServer">Use server</label> 
                    <div class="input"> 
                        <select name="useServer" id="useServer"> 
                            <option value="true">true</option> 
                            <option value="false">false</option> 
                        </select>
                        <span class="help-inline">
                            Sets if server binding should be used in the key (defaults to true)
                        </span>
                    </div> 
                </div> 

                <div class="clearfix"> 
                    <label for="allowLocal">Allow Local</label> 
                    <div class="input"> 
                        <select name="allowLocal" id="allowLocal"> 
                            <option value="false">false</option> 
                            <option value="true">true</option> 
                        </select>
                        <span class="help-inline">
                            Sets if server binding is in use then localhost servers are valid (defaults to false)
                        </span>
                    </div> 
                </div> 

            </fieldset> 


            <fieldset> 
                
                <legend>Data for PadlLicense::generate Method</legend> 
          
                <div class="clearfix"> 
                    <label for="domain">Domain</label> 
                    <div class="input"> 
                        <input class="xlarge" id="domain" name="domain" type="text" placeholder="domain without http:// or localhost" />
                        <span class="help-inline">
                            The domain to generate for
                        </span>
                    </div> 
                </div> 
          
                <div class="clearfix"> 
                    <label>Date Limit</label> 
                    <div class="input"> 
                        <div class="inline-inputs"> 
                            <select class="small" name="dateLimitMonth" id="dateLimitMonth"> 
                                <option value="1">January</option> 
                                <option value="2">February</option> 
                                <option value="3">March</option> 
                                <option value="4">April</option> 
                                <option value="5">May</option> 
                                <option value="6">June</option> 
                                <option value="7">July</option> 
                                <option value="8">August</option> 
                                <option value="9">September</option> 
                                <option value="10">October</option> 
                                <option value="11">November</option> 
                                <option value="12">December</option> 
                            </select> 
                            <select class="mini" name="dateLimitDay" id="dateLimitDay"> 
                                <?php for ($i=1; $i<=31; $i++) : ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php endfor; ?>
                            </select> 
                            <select class="mini" name="dateLimitYear" id="dateLimitYear"> 
                                <?php for($i=date('Y'); $i<=date('Y')+10; $i++) : ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php endfor; ?>
                            </select> 
                            <span class="help-inline">
                                The date limit for the license
                            </span> 
                        </div> 
                    </div> 
                </div>          
          
                <div class="actions"> 
                    <button type="submit" class="btn primary" name="submit">Submit and Generate Key</button>&nbsp;
                    <button type="reset" class="btn">Reset</button> 
                </div> 
                
            </fieldset>
            
        </form>

<?php if (isset($_POST['submit'])) : ?>        
        <a name="result"></a>
        <p style="height:50px;"></p>
        <h2>Informed data to generate</h2>

        <dl>

            <dt>Domain</dt>
            <dd><?php echo $_POST['domain']?></dd>
            
            <dt>Use Mcript</dt>
            <dd><?php echo $_POST['useMcrypt']?></dd>
            
            <dt>Use Time</dt>
            <dd><?php echo $_POST['useTime']?></dd>
            
            <dt>Use Server</dt>
            <dd><?php echo $_POST['useServer']?></dd>
            
            <dt>Allow Local</dt>
            <dd><?php echo $_POST['allowLocal']?></dd>
            
            <dt>Date Limit</dt>
            <dd><?php echo $_POST['dateLimitMonth']?> - <?php echo $_POST['dateLimitDay']?> - <?php echo $_POST['dateLimitYear']?></dd>
            
        </dl>
        
    <?php
    // register autoload
    include_once('../src/PHP5.2/Padl/PadlLibrary.php');
    PadlLibrary::init();

    // gets the data and transform to boolean
    $domain      = $_POST['domain'];
    $useMcrypt  = $_POST['useMcrypt']  == 'true' ?  true : false;
    $useTime    = $_POST['useTime']    == 'true' ?  true : false;
    $useServer  = $_POST['useServer']  == 'true' ?  true : false;
    $allowLocal = $_POST['allowLocal'] == 'true' ?  true : false;

    // calculates the time offset (expire_in)
    $now         = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
    $dateLimit  = mktime(23, 59, 59, 
            $_POST['dateLimitMonth'], 
            $_POST['dateLimitDay'], 
            $_POST['dateLimitYear']);
    $expireIn = $dateLimit - $now;

    // instatiate the class
    $padl = new PadlLicense($useMcrypt, $useTime, $useServer, $allowLocal);

    // copy the server vars (important for security, see note below)
    $server_array = $_SERVER;

    // set the server vars
    // note this doesn't have to be set, however if not all of your app files are encoded
    // then there would be a possibility that the end user could modify the server vars
    // to fit the key thus making it possible to use your app on any domain
    // you should copy your server vars in the first line of your active script so you can
    // use the unmodified copy of the vars
    $padl->setServerVars($server_array);

    // generate a key with your server details
    $license = $padl->generate($domain, 0, $expireIn);
    ?>

        <h2>Used code to generate</h2>
        <pre>
// Register Autoload
include_once('../src/PHP5.2/Padl/PadlLibrary.php');
PadlLibrary::init();

/*
Instance of License
parameters:
- useMcrypt
- useTime
- useServer
- allowLocal
*/
$padl = new PadlLicense(<?php echo $_POST['useMcrypt'] ?>, <?php echo $_POST['useTime'] ?>, <?php echo $_POST['useServer'] ?>, <?php echo $_POST['allowLocal'] ?>);

//For better security injecting a copy of $_SERVER global var
$server_array = $_SERVER;
$padl->setServerVars($server_array);


// Calculating the time offset (expire_in)
$now         = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
$dateLimit  = mktime(23, 59, 59, 
        $_POST['dateLimitMonth'], 
        $_POST['dateLimitDay'], 
        $_POST['dateLimitYear']);
$expireIn = $dateLimit - $now;

// Generating a key with your server details
$license = $padl->generate('<?php echo $domain ?>', 0, $expireIn);
        </pre>
        
        <h2>License Key or Error Message</h2>
        <pre><?php echo $license ?></pre>

        <h2>Validate</h2>
    <?php $results = $padl->validate($license); ?>    
        
        <pre>
$license = '(... the license key ...)';    
$results = $padl->validate($license);
        </pre>

    <?php include_once('_license_messages.php') ?>

        <h2>PadlLicense::validate return</h2>
        <pre><?php echo print_r($results) ?></pre>

<?php endif; ?>        
        
    </div> <!-- /container -->
    
<?php include_once ('_footer.php') ?>
    
  </body>
</html>
