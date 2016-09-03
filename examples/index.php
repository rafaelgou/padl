<?php include_once ('_header.php') ?>

    <div class="container">

        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">PHP Aplication Distribution Licensing</h1>
            <p>
                A simple way to secure your PHP Applications and Libraries.
            </p>
        </div>

        <!-- Quickstart options
        ================================================== -->
        <div class="quickstart">
          <div class="container">
            <div class="row">
              <div class="span5 columns">
                <h6>Supports PHP 5.3 and above </h6>
                <p>
                    For PHP 5.2 please refer to 
                    <a href="https://github.com/rafaelgou/padl/tree/v1.0.2" class="btn primary">version 1.0.2</a>.
                </p>
              </div>
              <div class="span5 columns">
                <h6>Install or Download</h6>
                <pre>composer require rafaelgou:padl</pre>
                <p>Directly download TAR.GZ or .ZIP from the sources</p>
                <p>
                    <a href="https://github.com/rafaelgou/padl/tarball/master" class="btn primary">Tarball (padl.tar.gz)</a>
                    <a href="https://github.com/rafaelgou/padl/zipball/master" class="btn primary">Zipball (padl.zip)</a>
                </p>
              </div>
              <div class="span5 columns">
                <h6>Fork on GitHub</h6>
                <p>Download, fork, pull, file issues, and more with the official Padl repo on Github.</p>
                <p><a target="_blank" href="https://github.com/rafaelgou/padl" class="btn primary">PADL on GitHub &raquo;</a></p>
              </div>
            </div><!-- /row -->
          </div>
        </div>

        <div class="page-header">
            <h2>Examples for PHP<small>Using namespaced class</small></h2>
        </div>

        <!-- Example row of columns -->
        <div class="row">
        <div class="span4">
            <h2>Generate Key</h2>
            <p>Just generate a Key and show its data.</p>
            <p><a class="btn" href="example-generatekey.php">See &raquo;</a></p>
        </div>
        <div class="span4">
            <h2>Validate Key</h2>
            <p>Validates a sent Key and show its data.</p>
            <p><a class="btn" href="example-validatekey.php">See &raquo;</a></p>
        </div>
        <div class="span4">
            <h2>Generate File Key</h2>
            <p>Just generate a Key and show its data.</p>
            <p><a class="btn" href="example-generatefilekey.php">See &raquo;</a></p>
        </div>
        <div class="span4">
            <h2>Validate File Key</h2>
            <p>Validates a sent File Key and show its data.</p>
            <p><a class="btn" href="example-validatefilekey.php">See &raquo;</a></p>
        </div>

    </div> <!-- /container -->

<?php include_once ('_footer.php') ?>

  </body>
</html>
