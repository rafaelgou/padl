<?php include_once ('_header.php') ?>

    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
            <h1 style="font-size: 3em">Tips &AMP; Tricks</h1>
        </div>

        <h2>Use a Encoder</h2>
        <p>
            If you distribuite your code with open source code, any expert programmer
            could bypass the license.
        </p>
        <p>
            You'd get better results enconding some critical files with
            <a href="http://www.ioncube.com/">Ioncube</a>, 
            <a href="http://www.sourceguardian.com/">SourceGuardian</a>,
            <a href="http://www.zend.com/en/products/guard/">ZendGuard</a>
            or some other encoder you find.
        </p>
        <p>
            You can encode:
        </p>
        <ul>
            <li>The Padl\License class</li>
            <li>The file which loads Padl\License</li>
            <li>The file which throwns the license exception</li>
            <li>The controller of your MVC</li>
            <li>etc.</li>
        </ul>
        <p>
            Keep in mind that if you call Padl from a very common file
            that can be easily replaced, that can be bypassed easily, 
            just overwriting them in example:
        </p>
        <ul>
            <li>Default Front controllers of populars PHP frameworks such as Symfony, CakePHP, Zend Framework, etc.</li>
            <li>Any Open Source Library</li>
            <li>etc.</li>
        </ul>

        <h2>Copy and inject $_SESSION var</h2>
        <p>
            This global var could be spooffed with adequate data to force validation.
        </p>
        <p>
            A usual trick is to copy the $_SESSION var at the very begging of the script,
            before any script that could alter this var. This blocks any undesible injection.
        </p>
        <pre>
// At the beginning of the script
$server_array = $_SERVER;

// Instance
$padl = new \Padl\License(true, true, false, true);

// Injecting Server Vars
$padl->set_server_vars($server_array);
        </pre>
        <h2>Extends Padl\License</h2>
        <p>
            You can extends Padl\License and add extra security to them.
        </p>
        <p>
            The most desirebable override is the HASHs and IDs.
        </p>
        <pre>
namespace Padl;
use Padl\License;
class MyLicense extends License {

    protected $hashKey1   = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35BDFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEzGDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHT';
    protected $hashKey2   = '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYDU52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wohX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4a';
    protected $hashKey3   = 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W66qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62T';

    protected $id1      = 'nSpkAHRiFfM2hE588eB';
    protected $id2      = 'NWCy0s0JpGubCVKlkkK';
    protected $id3      = 'G95ZP2uS782cFey9x5A';
}
        </pre>

        <h2>Add extra info and check after validation</h2>
        <p>
            You can pass extra info when generating, and check that
            after validation. This way you add extra custom security.
        </p>
        <pre>
// (...) normal stuff to generate
$options = array(
    'application' => 'MyApp',
    'version'     => '1.2.9',
    // etc
);
$license = $padl->generate($domain, 0, $expireIn, $options);

// and to validate...
$result = $padl->validate($license);
if ( 
    $result['RESULT'] === 'OK' 
    && $result['DATA']['application'] == 'MyApp'
    && $result['DATA']['version'] == '1.2.9'
) {
    echo 'VALID';
} else {
    echo 'INVALID';
}


        </pre>
        
    </div> <!-- /container -->

<?php include_once ('_footer.php') ?>
    
  </body>
</html>
