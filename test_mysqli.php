<?php header("Content-Type: text/plain"); echo "EXT=".(extension_loaded("mysqli")?"yes":"no")."
"; echo "CLASS=".(class_exists("mysqli")?"yes":"no")."
"; echo "INI=".(php_ini_loaded_file()?:"none")."
"; $a = new mysqli("127.0.0.1","root","","dbCruzSanchez"); echo "CONNECT_OK
";