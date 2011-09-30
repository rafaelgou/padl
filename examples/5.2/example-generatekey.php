<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Generate Key</h1>
        </div>

        <form action="example-generatekey.php#result" method="post"> 
            
            <fieldset> 

                <legend>Data for Instantiate PadlLicense Class</legend> 

                <div class="clearfix"> 
                    <label for="use_mcrypt">Use MCript</label> 
                    <div class="input"> 
                        <select name="use_mcrypt" id="use_mcrypt"> 
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
                    <label for="use_time">Use Time</label> 
                    <div class="input"> 
                        <select name="use_time" id="use_time"> 
                            <option value="true">true</option> 
                            <option value="false">false</option> 
                        </select>
                        <span class="help-inline">
                            Sets if time binding should be used in the key (defaults to true)
                        </span>
                    </div> 
                </div> 

                <div class="clearfix"> 
                    <label for="use_server">Use server</label> 
                    <div class="input"> 
                        <select name="use_server" id="use_server"> 
                            <option value="true">true</option> 
                            <option value="false">false</option> 
                        </select>
                        <span class="help-inline">
                            Sets if server binding should be used in the key (defaults to true)
                        </span>
                    </div> 
                </div> 

                <div class="clearfix"> 
                    <label for="allow_local">Allow Local</label> 
                    <div class="input"> 
                        <select name="allow_local" id="allow_local"> 
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
                            <select class="small" name="date_limit_month" id="date_limit_month"> 
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
                            <select class="mini" name="date_limit_day" id="date_limit_day"> 
                                <?php for($i=1; $i<=31; $i++) : ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php endfor; ?>
                            </select> 
                            <select class="mini" name="date_limit_year" id="date_limit_year"> 
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
            <dd><?php echo $_POST['use_mcrypt']?></dd>
            
            <dt>Use Time</dt>
            <dd><?php echo $_POST['use_time']?></dd>
            
            <dt>Use Server</dt>
            <dd><?php echo $_POST['use_server']?></dd>
            
            <dt>Allow Local</dt>
            <dd><?php echo $_POST['allow_local']?></dd>
            
            <dt>Date Limit</dt>
            <dd><?php echo $_POST['date_limit_month']?> - <?php echo $_POST['date_limit_day']?> - <?php echo $_POST['date_limit_year']?></dd>
            
        </dl>
        
    <?php
    // register autoload
    include_once('../../src/PHP5.2/Padl/Padl.php');

    // gets the data and transform to boolean
    $domain      = $_POST['domain'];
    $use_mcrypt  = $_POST['use_mcrypt']  == 'true' ?  true : false;
    $use_time    = $_POST['use_time']    == 'true' ?  true : false;
    $use_server  = $_POST['use_server']  == 'true' ?  true : false;
    $allow_local = $_POST['allow_local'] == 'true' ?  true : false;

    // calculates the time offset (expire_in)
    $now         = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
    $date_limit  = mktime(23, 59, 59, 
            $_POST['date_limit_month'], 
            $_POST['date_limit_day'], 
            $_POST['date_limit_year']);
    $expire_in = $date_limit - $now;

    // Instantiate the class
    $padl = new PadlLicense($use_mcrypt, $use_time, $use_server, $allow_local);

    // copy the server vars (important for security, see note below)
    $server_array = $_SERVER;

    // set the server vars
    // note this doesn't have to be set, however if not all of your app files are encoded
    // then there would be a possibility that the end user could modify the server vars
    // to fit the key thus making it possible to use your app on any domain
    // you should copy your server vars in the first line of your active script so you can
    // use the unmodified copy of the vars
    $padl->set_server_vars($server_array);

    // generate a key with your server details
    $license = $padl->generate($domain, 0, $expire_in);
    ?>

        <h2>Used code to generate</h2>
        <pre>
// register autoload
include_once('../../src/PHP5.2/Padl/Padl.php');

/*
Instance of License
parameters:
- use_mcrypt
- use_time
- use_server
- allow_local
*/
$padl = new PadlLicense(<?php echo $_POST['use_mcrypt'] ?>, <?php echo $_POST['use_time'] ?>, <?php echo $_POST['use_server'] ?>, <?php echo $_POST['allow_local'] ?>);

//For better security injecting a copy of $_SERVER global var
$server_array = $_SERVER;
$padl->set_server_vars($server_array);


// Calculating the time offset (expire_in)
$now         = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
$date_limit  = mktime(23, 59, 59, 
        $_POST['date_limit_month'], 
        $_POST['date_limit_day'], 
        $_POST['date_limit_year']);
$expire_in = $date_limit - $now;

// Generating a key with your server details
$license = $padl->generate('<?php echo $domain ?>', 0, $expire_in);
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
