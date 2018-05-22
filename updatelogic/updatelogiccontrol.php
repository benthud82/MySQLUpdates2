
<?php

$whsearray = array(3,7,11);

set_time_limit(99999);
ini_set('max_execution_time', 99999);
ini_set('set_time_limit', 99999);
ini_set('memory_limit', '-1');
ini_set('request_terminate_timeout', 99999);

foreach ($whsearray as $whssel) {

    include 'main.php';

    if ($whssel <> 11) {
        include 'main_case.php';
    }

    include 'optimalbayloose.php';

    if ($whssel <> 11) {
        include 'optimalbaycase.php';
    }
}


include 'itemscore.php';

