

<?php
set_time_limit(99999);
//code to update fomaverage mysql table
//This takes 2+ hours to update.  Can this be streamlined
//Need to update this through weekend nightstream
//One idea is create another table of only items that have shipped 30+ times over past 100+ days and join the results query to this table

function _arraykeysearch($array, $multikey, $matchvalue) {
    $resultarray = [];
    foreach ($array as $keyval => $value) {
        if ($array[$keyval][$multikey] == $matchvalue) {
            array_push($resultarray, $keyval);
        }
    }
    return $resultarray;
}

function standard_deviation($arr) {
    // Calculates the standard deviation for all non-zero items in an array

    $n = count($arr);   // Counts non-zero elements in the array.
    $mean = array_sum($arr) / $n;     // Calculates the arithmetic mean.
    $sum = 0;

    foreach ($arr as $key => $a) {
        $sum = $sum + pow($a - $mean, 2);
    }

    $stdev = sqrt($sum / $n);

    return $stdev;
}

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 

$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM));

//$sqldelete = "TRUNCATE nonfomaverage";
//$querydelete = $conn1->prepare($sqldelete);
//$querydelete->execute();

//pull in all fom dates
$sqldates = "SELECT DISTINCT FOMDATE FROM FOMRAW WHERE ISFOM = 'N' ORDER BY FOMDATE";
$querydates = $conn1->prepare($sqldates);
$querydates->execute();

//create array of FOM dates and a default value of 0 for the ship qty.  This will be overwritten later with actual ship qty if applicable
$datearray = array();
foreach ($querydates as $current) {
    // create the array key if it doesn't exist already
    if (!array_key_exists($current[0], $datearray)) {
        $datearray[$current[0]] = 0;
    }
}

//Only want to look at items that have had a minimum of 30 ship occurences over past 100+ days
$sqlexclude = "SELECT DISTINCT CONCAT(FOMWHSE,FOMITEM, FOMPKGU), COUNT(FOMITEM) FROM FOMRAW WHERE ISFOM = 'N' AND FOMWHSE = 7 AND FOMDATE BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() GROUP BY CONCAT(FOMWHSE,FOMITEM, FOMPKGU) HAVING COUNT(FOMITEM) >= 5 ORDER BY FOMITEM";
$queryexclude = $conn1->prepare($sqlexclude);
$queryexclude->execute();


$includearray = array();
foreach ($queryexclude as $current1) {
    // create the array key if it doesn't exist already
    if (!array_key_exists($current1[0], $includearray)) {
        $includearray[$current1[0]] = 0;
    }
}

//pull in all fom items
$sqlitem = "SELECT DISTINCT concat(FOMWHSE,FOMITEM, FOMPKGU) FROM FOMRAW WHERE ISFOM = 'N' and FOMWHSE = 7 ORDER BY FOMITEM";
$queryitem = $conn1->prepare($sqlitem);
$queryitem->execute();

$itemarray = array();
foreach ($queryitem as $current2) {
    // create the array key if it doesn't exist already
    if (!array_key_exists($current2[0], $itemarray)) {
        $itemarray[$current2[0]] = 0;
    }
}


//pull in all result data for fom
$result1 = $conn1->prepare("SELECT concat(FOMWHSE, FOMITEM, FOMPKGU), FOMPQTY, FOMDATE FROM fomraw WHERE ISFOM = 'N' and FOMWHSE = 7");
$result1->execute();
$resultarray = $result1->fetchAll();

$datekeys = array_keys($datearray);

foreach ($itemarray as $key => $value) {
    //deterime if item is in exlude array (30+ shipments).  If not, continue.
    if (!array_key_exists($key, $includearray)) {
        continue;
    }

    foreach ($datekeys as $key3) {
        $datearray[$key3] = 0;
    }


    //create array of item shipments by date.  $key is the concat of (whs, item, pkgu).  Iterate through each item, pull in shipment results by date and apply to $datearray
    //use array key search to determine shipments key array from the result array.  Use these keys to pull the shipments by date and add to $datearray
    $resultkeys = _arraykeysearch($resultarray, 0, $key);

    //loop through resultkeys
    foreach ($resultkeys as $lookupkey) {
        $datefind = $resultarray[$lookupkey][2]; //find the date for the key in the datearray
        $shipqtyfind = $resultarray[$lookupkey][1]; //find the ship qty for that date
        $datearray[$datefind] = $shipqtyfind;  //replace the 0 with the actual ship qty in the date array
        unset($resultarray[$lookupkey]);
    }


    $std = standard_deviation($datearray);
    $avg = array_sum($datearray) / count($datearray);
    $count = count($datearray);
    $item = substr($key, 1, 7);
    $pkgu = substr($key, 8);
    $whse = substr($key, 0, 1);


    $sql = "INSERT INTO NONFOMAVERAGE (NONFOMAVGWHSE, NONFOMAVGITEM, NONFOMAVGPKGU, NONFOMAVGAVG, NONFOMAVGSTD, NONFOMAVGCOUNT) VALUES (:NONFOMAVGWHSE, :NONFOMAVGITEM, :NONFOMAVGPKGU, :NONFOMAVGAVG, :NONFOMAVGSTD, :NONFOMAVGCOUNT)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':NONFOMAVGWHSE' => $whse, ':NONFOMAVGITEM' => $item, ':NONFOMAVGPKGU' => $pkgu, ':NONFOMAVGAVG' => $avg, ':NONFOMAVGSTD' => $std, ':NONFOMAVGCOUNT' => $count));
}