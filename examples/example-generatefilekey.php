<?php
if (isset($_POST['submit']))
{
    // register autoload
    require('../vendor/autoload.php');

    // gets the data and transform to boolean
    $domain     = $_POST['domain'];
    $useMcrypt  = $_POST['useMcrypt']  === 'true' ?  true : false;
    $useTime    = $_POST['useTime']    === 'true' ?  true : false;
    $useServer  = $_POST['useServer']  === 'true' ?  true : false;
    $allowLocal = $_POST['allowLocal'] === 'true' ?  true : false;

    // calculates the offset (expire_in)
    $now        = mktime(date('H'), date('i'), date('s'), date('m'), date('d') , date('Y'));
    $dateLimit  = mktime(23, 59, 59,
            $_POST['dateLimitMonth'],
            $_POST['dateLimitDay'],
            $_POST['dateLimitYear']);
    $expireIn = $dateLimit - $now;

    // instatiate the class
    $padl = new \Padl\License($useMcrypt, $useTime, $useServer, $allowLocal);

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

    header("Content-Type: application/save");
    header("Content-Length:".strlen($license));
    header('Content-Disposition: attachment; filename="' . $_POST['filename'] . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    echo $license;
    exit;
} ?>
<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Generate File Key</h1>
        </div>

        <form action="example-generatefilekey.php" method="post">

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

                <legend>Data for Padl\License::generate Method</legend>

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
                                <?php for($i=1; $i<=31; $i++) : ?>
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

                <div class="clearfix">
                    <label for="filename">File Name</label>
                    <div class="input">
                        <input class="xlarge" id="filename" name="filename" type="text" value="license.dat" />
                        <span class="help-inline">
                            The file name to generate for
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn primary" name="submit">Submit and Generate Key</button>&nbsp;
                    <button type="reset" class="btn">Reset</button>
                </div>

            </fieldset>

        </form>

    </div> <!-- /container -->

<?php include_once ('_footer.php') ?>

  </body>
</html>
