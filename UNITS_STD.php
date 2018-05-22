<?php

include_once '../globalincludes/usa_asys.php';
//include_once '../globalincludes/ustxgpslotting_mysql.php';

//calculates UNITS standard deviation to be used in move predictor
set_time_limit(99999);

//function _arraykeysearch($array, $multikey, $matchvalue) {
//    $resultarray = [];
//    foreach ($array as $keyval => $value) {
//        if ($array[$keyval][$multikey] == $matchvalue) {
//            array_push($resultarray, $keyval);
//        }
//    }
//    return $resultarray;
//}


function remove_outliers($dataset, $magnitude = 1.5) {

    $count = count($dataset);
    $mean = array_sum($dataset) / $count; // Calculate the mean
    $deviation = sqrt(array_sum(array_map("sd_square", $dataset, array_fill(0, $count, $mean))) / $count) * $magnitude; // Calculate standard deviation and times by magnitude

    return array_filter($dataset, function($x) use ($mean, $deviation) {
        return ($x <= $mean + $deviation && $x >= $mean - $deviation);
    }); // Return filtered array of values that lie within $mean +- $deviation.
}

function sd_square($x, $mean) {
    return pow($x - $mean, 2);
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


//$sqltruncate = "TRUNCATE slotting.adbs_std";
//$querydelete = $conn1->prepare($sqltruncate);
//$querydelete->execute();

$sqldelete = "DELETE from slotting.units_std WHERE UNITS_WAREHOUSE = 9";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();
//pull in dataset for ID and all ADBS calcs to determine STD
$sql_adbs_std = "SELECT 
                WAREHOUSE,
                ITEM_NUMBER,
                PACKAGE_UNIT,
                PACKAGE_TYPE,
                DSL_TYPE,
                UNITS_01 as UNITS1,
                UNITS_02 as UNITS2,
                UNITS_03 as UNITS3,
                UNITS_04 as UNITS4,
                UNITS_05 as UNITS5,
                UNITS_06 as UNITS6,
                UNITS_07 as UNITS7,
                UNITS_08 as UNITS8,
                UNITS_09 as UNITS9,
                UNITS_10 as UNITS10,
                UNITS_11 as UNITS11,
                UNITS_12 as UNITS12,
                UNITS_13 as UNITS13,
                UNITS_14 as UNITS14,
                UNITS_15 as UNITS15,
                UNITS_16 as UNITS16,
                UNITS_17 as UNITS17,
                UNITS_18 as UNITS18,
                UNITS_19 as UNITS19,
                UNITS_20 as UNITS20,
                UNITS_21 as UNITS21,
                UNITS_22 as UNITS22,
                UNITS_23 as UNITS23,
                UNITS_24 as UNITS24,
                UNITS_25 as UNITS25,
                UNITS_26 as UNITS26,
                UNITS_27 as UNITS27,
                UNITS_28 as UNITS28,
                UNITS_29 as UNITS29,
                UNITS_30 as UNITS30
             FROM A.HSIPCORDTA.NPTSLD
             WHERE NBR_SHIP_OCC >= 5 and WAREHOUSE = 9";
$query_adbs_std = $aseriesconn->prepare($sql_adbs_std);
$query_adbs_std->execute();
$array_adbs_std = $query_adbs_std->fetchAll();

foreach ($array_adbs_std as $key => $value) {

    $WAREHOUSE = $array_adbs_std[$key]['WAREHOUSE'];
    $ITEM_NUMBER = $array_adbs_std[$key]['ITEM_NUMBER'];
    $PACKAGE_UNIT = $array_adbs_std[$key]['PACKAGE_UNIT'];
    $PACKAGE_TYPE = $array_adbs_std[$key]['PACKAGE_TYPE'];
    $DSL_TYPE = $array_adbs_std[$key]['DSL_TYPE'];

    $std_array = array();
    $keycounter = 1;


    while ($array_adbs_std[$key]['UNITS' . $keycounter] > 0 && $keycounter <= 30) {
        $std_array[] = intval($array_adbs_std[$key]['UNITS' . $keycounter]);
        $keycounter+=1;
    }

    $std_array_outliers_removed = remove_outliers($std_array);
    $count = count($std_array_outliers_removed);
    if ($count >= 2) {
        $std = standard_deviation($std_array_outliers_removed);
        $avg = array_sum($std_array_outliers_removed) / count($std_array_outliers_removed);

        $sql = "INSERT INTO units_std (UNITS_WAREHOUSE, UNITS_ITEM, UNITS_PKGU, UNITS_CSLS, UNITS_DSL, UNITS_AVG, UNITS_STD, UNITS_COUNT) VALUES (:UNITS_WAREHOUSE, :UNITS_ITEM, :UNITS_PKGU, :UNITS_CSLS, :UNITS_DSL, :UNITS_AVG, :UNITS_STD, :UNITS_COUNT)";
        $query = $conn1->prepare($sql);
        $query->execute(array(':UNITS_WAREHOUSE' => $WAREHOUSE, ':UNITS_ITEM' => $ITEM_NUMBER, ':UNITS_PKGU' => $PACKAGE_UNIT, ':UNITS_CSLS' => $PACKAGE_TYPE, ':UNITS_DSL' => $DSL_TYPE, ':UNITS_AVG' => $avg, ':UNITS_STD' => $std, ':UNITS_COUNT' => $count));
    }
}