<?php

include_once '../globalincludes/usa_asys.php';
//include_once '../globalincludes/ustxgpslotting_mysql.php';

//calculates ADBS standard deviation to be used in move predictor
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

$sqldelete = "DELETE from slotting.adbs_std WHERE ADBS_WAREHOUSE = 9";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();
//pull in dataset for ID and all ADBS calcs to determine STD
$sql_adbs_std = "SELECT 
                WAREHOUSE,
                ITEM_NUMBER,
                PACKAGE_UNIT,
                PACKAGE_TYPE,
                DSL_TYPE,
                DAYS_BTW_02 as ADBS2,
                DAYS_BTW_03 as ADBS3,
                DAYS_BTW_04 as ADBS4,
                DAYS_BTW_05 as ADBS5,
                DAYS_BTW_06 as ADBS6,
                DAYS_BTW_07 as ADBS7,
                DAYS_BTW_08 as ADBS8,
                DAYS_BTW_09 as ADBS9,
                DAYS_BTW_10 as ADBS10,
                DAYS_BTW_11 as ADBS11,
                DAYS_BTW_12 as ADBS12,
                DAYS_BTW_13 as ADBS13,
                DAYS_BTW_14 as ADBS14,
                DAYS_BTW_15 as ADBS15,
                DAYS_BTW_16 as ADBS16,
                DAYS_BTW_17 as ADBS17,
                DAYS_BTW_18 as ADBS18,
                DAYS_BTW_19 as ADBS19,
                DAYS_BTW_20 as ADBS20,
                DAYS_BTW_21 as ADBS21,
                DAYS_BTW_22 as ADBS22,
                DAYS_BTW_23 as ADBS23,
                DAYS_BTW_24 as ADBS24,
                DAYS_BTW_25 as ADBS25,
                DAYS_BTW_26 as ADBS26,
                DAYS_BTW_27 as ADBS27,
                DAYS_BTW_28 as ADBS28,
                DAYS_BTW_29 as ADBS29,
                DAYS_BTW_30 as ADBS30
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
    $keycounter = 2;


    while ($array_adbs_std[$key]['ADBS' . $keycounter] > 0 && $keycounter <= 30) {
        $std_array[] = intval($array_adbs_std[$key]['ADBS' . $keycounter]);
        $keycounter+=1;
    }

    $std_array_outliers_removed = remove_outliers($std_array);
    $count = count($std_array_outliers_removed);
    if ($count >= 2) {
        $std = standard_deviation($std_array_outliers_removed);
        $avg = array_sum($std_array_outliers_removed) / count($std_array_outliers_removed);

        $sql = "INSERT INTO adbs_std (ADBS_WAREHOUSE, ADBS_ITEM, ADBS_PKGU, ADBS_CSLS, ADBS_DSL, ADBS_AVG, ADBS_STD, ADBS_COUNT) VALUES (:ADBS_WAREHOUSE, :ADBS_ITEM, :ADBS_PKGU, :ADBS_CSLS, :ADBS_DSL, :ADBS_AVG, :ADBS_STD, :ADBS_COUNT)";
        $query = $conn1->prepare($sql);
        $query->execute(array(':ADBS_WAREHOUSE' => $WAREHOUSE, ':ADBS_ITEM' => $ITEM_NUMBER, ':ADBS_PKGU' => $PACKAGE_UNIT, ':ADBS_CSLS' => $PACKAGE_TYPE, ':ADBS_DSL' => $DSL_TYPE, ':ADBS_AVG' => $avg, ':ADBS_STD' => $std, ':ADBS_COUNT' => $count));
    }
}