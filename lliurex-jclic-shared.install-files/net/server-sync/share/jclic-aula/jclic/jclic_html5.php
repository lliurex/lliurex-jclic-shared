<?php
    $jclicpath= $_POST['argumento'];
    print ($jclicpath);
    exec("/var/www/jclic-aula/helper.py " . $jclicpath);
?>