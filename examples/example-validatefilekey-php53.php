<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Validate File Key</h1>
        </div>

        <form action="example-validatefilekey-php53.php#result" method="post" enctype="multipart/form-data"> 
            <fieldset> 

                <legend>Data for Instatiate Padl\License Class</legend> 

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
            <dd><?php echo $_POST['useMcrypt']?></dd>
            
            <dt>Use Time</dt>
            <dd><?php echo $_POST['useTime']?></dd>
            
            <dt>Use Server</dt>
            <dd><?php echo $_POST['useServer']?></dd>
            
            <dt>Allow Local</dt>
            <dd><?php echo $_POST['allowLocal']?></dd>
            
        </dl>
        
    <?php
    // register autoload
    include_once('../src/PHP5.3/Padl/Padl.php');
    Padl::registerAutoload();

    // gets the data and transform to boolean
    $useMcrypt  = $_POST['useMcrypt']  == 'true' ?  true : false;
    $useTime    = $_POST['useTime']    == 'true' ?  true : false;
    $useServer  = $_POST['useServer']  == 'true' ?  true : false;
    $allowLocal = $_POST['allowLocal'] == 'true' ?  true : false;

    // instatiate the class
    $padl = new Padl\License($useMcrypt, $useTime, $useServer, $allowLocal);

    // copy the server vars (important for security, see note below)
    $server_array = $_SERVER;

    // set the server vars
    // note this doesn't have to be set, however if not all of your app files are encoded
    // then there would be a possibility that the end user could modify the server vars
    // to fit the key thus making it possible to use your app on any domain
    // you should copy your server vars in the first line of your active script so you can
    // use the unmodified copy of the vars
    $padl->setServerVars($server_array);
    
    ?>
        <h2>Instantiation</h2>
        <pre>
// Register Autoload
include_once('  ../src/PHP5.3/Padl/Padl.php');
Padl::registerAutoload();

/*
Instance of License
parameters used in this sample:
- useMcrypt  = false 
- useTime    = true
- useServer  = false
- allowLocal = true
*/
$padl = new Padl\License(<?php echo $_POST['useMcrypt'] ?>, <?php echo $_POST['useTime'] ?>, <?php echo $_POST['useServer'] ?>, <?php echo $_POST['allowLocal'] ?>);

// For better security injecting a copy of $_SERVER global var
$server_array = $_SERVER;
$padl->setServerVars($server_array);
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
<?php endif; ?>              
    </div> <!-- /container -->
    
<?php include_once ('_footer.php') ?>
    
  </body>
</html>
