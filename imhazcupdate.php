<?php

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
//include_once '../globalincludes/nahsi_mysql.php';
include_once '../globalincludes/ustxgpslotting_mysql.php';
include_once '../globalincludes/usa_asys.php';
include_once '../globalincludes/newcanada_asys.php';
include_once '../globalfunctions/slottingfunctions.php';


if (isset($var_whse)) {
    $sqldelete = "DELETE FROM slotting.imhazc WHERE Whse = $var_whse";
    $whsefilter = 'LOWHSE = ' . $var_whse;
} else {
    $sqldelete = "TRUNCATE TABLE slotting.imhazc";
    $whsefilter = 'LOWHSE in (2,3,6,7,9,11,12,16)';
}

$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$columns = 'IMITEM, COUNTRY, IMHAZC';

$whsearray = array(2, 3, 6, 7, 9);

foreach ($whsearray as $whsval) {

    $cpcresult = $aseriesconn->prepare("SELECT IMITEM, IMHAZC FROM HSIPCORDTA.NPFIMS WHERE IMHAZC <> ''");
    $cpcresult->execute();
    $NPFCPC_ALL_array = $cpcresult->fetchAll(pdo::FETCH_ASSOC);


    $maxrange = 9999;
    $counter = 0;
    $rowcount = count($NPFCPC_ALL_array);
    $country = 'USA';

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
            $IMITEM = intval($NPFCPC_ALL_array[$counter]['IMITEM']);
            $IMHAZC = ($NPFCPC_ALL_array[$counter]['IMHAZC']);
            if (!is_numeric($IMITEM) || $IMITEM < 1000000) {
                $counter +=1;
                continue;
            }

            $data[] = "($IMITEM, '$country','$IMHAZC')";
            $counter +=1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.imhazc ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=10000;
    } while ($counter <= $rowcount); //end of item by whse loop
}




$whsearray = array(11, 12, 16);

foreach ($whsearray as $whsval) {

    $cpcresult = $aseriesconn->prepare("SELECT IMITEM, IMHAZC FROM ARCPCORDTA.NPFIMS WHERE IMHAZC <> ''");
    $cpcresult->execute();
    $NPFCPC_ALL_array = $cpcresult->fetchAll(pdo::FETCH_ASSOC);


    $maxrange = 9999;
    $counter = 0;
    $rowcount = count($NPFCPC_ALL_array);
    $country = 'CAN';

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
            $IMITEM = intval($NPFCPC_ALL_array[$counter]['IMITEM']);
            $IMHAZC = ($NPFCPC_ALL_array[$counter]['IMHAZC']);
            if (!is_numeric($IMITEM) || $IMITEM < 1000000) {
                $counter +=1;
                continue;
            }

            $data[] = "($IMITEM, '$country','$IMHAZC')";
            $counter +=1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.imhazc ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=10000;
    } while ($counter <= $rowcount); //end of item by whse loop
}
