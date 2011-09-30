<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Validate File Key</h1>
        </div>

        <form action="example-validatefilekey.php#result" method="post" enctype="multipart/form-data"> 
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
                <legend>Uploading Key File</legend> 

                <div class="clearfix">
                    <label for="xlInput">Key File Input</label>
                    <div class="input">
                        <input class="input-file" id="keyfile" name="keyfile" type="file">
                        <span class="help-inline">
                            Upload the license key file
                        </span>
                    </div>
                </div>          


                <div class="actions"> 
                    <button type="submit" class="btn primary" name="submit">Submit and Validate Key</button>&nbsp;
                    <button type="reset" class="btn">Reset</button> 
                </div> 
            </fieldset>
        </form>
        
<?php if (isset($_POST['submit'])) : ?>
        
        <a name="result"></a>
        <p style="height:50px;"></p>
        <h2>Informed data to validate</h2>
        <dl>

            <dt>Use Mcript</dt>
            <dd><?php echo $_POST['use_mcrypt']?></dd>
            
            <dt>Use Time</dt>
            <dd><?php echo $_POST['use_time']?></dd>
            
            <dt>Use Server</dt>
            <dd><?php echo $_POST['use_server']?></dd>
            
            <dt>Allow Local</dt>
            <dd><?php echo $_POST['allow_local']?></dd>
            
        </dl>
        
    <?php
    // register autoload
    include_once('../../src/PHP5.2/Padl/Padl.php');

    // gets the data and transform to boolean
    $use_mcrypt  = $_POST['use_mcrypt']  == 'true' ?  true : false;
    $use_time    = $_POST['use_time']    == 'true' ?  true : false;
    $use_server  = $_POST['use_server']  == 'true' ?  true : false;
    $allow_local = $_POST['allow_local'] == 'true' ?  true : false;

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
    
    ?>
        <h2>Instantiation</h2>
        <pre>
// register autoload
include_once('../../src/PHP5.2/Padl/Padl.php');

/*
Instance of License
parameters used in this sample:
- use_mcrypt  = false 
- use_time    = true
- use_server  = false
- allow_local = true
*/
$padl = new PadlLicense(<?php echo $_POST['use_mcrypt'] ?>, <?php echo $_POST['use_time'] ?>, <?php echo $_POST['use_server'] ?>, <?php echo $_POST['allow_local'] ?>);

// For better security injecting a copy of $_SERVER global var
$server_array = $_SERVER;
$padl->set_server_vars($server_array);
        </pre>
        
    <?php 
    // get the license from uploaded file
    $license = file_get_contents($_FILES['keyfile']['tmp_name']);
    ?>    
        <h2>Uploaded License Key</h2>
        <pre><?php echo $license ?></pre>

        <h2>Validate</h2>

    <?php 
    // the set key is the key validated
    $results = $padl->validate($license);
    ?>    
        <pre>
// get the license from uploaded file
$license =  file_get_contents($_FILES['keyfile']['tmp_name']);
// the set key is the key validated
$results = $padl->validate($license);
        </pre>

        <?php include_once('_license_messages.php') ?>

        <h2>PadlLicense::validate return</h2>
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
<?php endif; ?>              
    </div> <!-- /container -->
    
<?php include_once ('_footer.php') ?>
    
  </body>
</html>
