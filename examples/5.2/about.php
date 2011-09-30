<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">About PHP Aplication Distribution Licensing</h1>
        </div>

        <h2>History</h2>
        <p>
            PADL is a originaly written in 2005 by <strong>Oliver Lillie</strong> under older PHP4, parked o PHPCLASSES site.
            You can find the original code as part as this distribution or directelly in
            <a href="http://www.phpclasses.org/package/2298-PHP-Generate-PHP-application-license-keys.html">
            http://www.phpclasses.org/package/2298-PHP-Generate-PHP-application-license-keys.html
            </a>.
        </p>
        <p>
            You can see a quote about:
        </p>
        <blockquote>
            <p>
                There are several solutions to help developers protecting the PHP software of 
                applications that they want to sell.
            </p>
            <p>
                Usually the developers provide license keys with their software that 
                include information about the licensed code, enabled features, authorized 
                configuration of the environment on which it is being installed.
            </p>
            <p>
                This helps the developers to limit the scope of usage of their software 
                according to the type of software license that their clients purchase.
            </p>
            <p>
                This way the developers can use the same software distribution for different 
                configurations and prices and even trial versions.
            </p>
            <p>
                This class provides a means to generate license keys that include encrypted 
                information about the clients PHP environment and even a way to send the keys 
                to the developers site so they can capture the client features and generate 
                upgraded keys that can enable more features for clients that pay the upgrade price.
            </p>
            <p>
                <small>Manuel Lemos (PHPCLASSES manteiner)</small>
            </p>
        </blockquote>
        <p>
            When I need to licence a PHP project, found this class and as it was so well written,
            it was very easy to update it to PH 5.2 and 5.3.
        </p>
        <p>
            You can find more about the original author in:
            <ul>
                <li>
                    <a href="http://www.phpclasses.org/browse/author/122732.html">
                        PHP CLASSES: Classes of Oliver Lillie
                    </a>
                </li>
                <li>
                    <a href="http://www.phpclasses.org/professionals/profile/9072/">
                        PHP CLASSES: Professional profile of Oliver Lillie
                    </a>
                </li>
            </ul>
        </p>
        <p>
            Otherwise a lot of modifications has been made, all core methods and
            examples are or just a copy or strongly based on the original project.
        </p>

        <h2>PHP 5.2 and 5.3 versions</h2>
        <p>
            This versions are the same, but the loader are different and the 5.3 version
            uses namespaces.
        </p>
        <p>
            There are some updates in the structure, class names and methods to remote validation
            to adapt to 2011 days.
        </p>
        <p>
            A validator server is not yet implemented.
        </p>

    </div> <!-- /container -->

<?php include_once ('_footer.php') ?>
    
  </body>
</html>
