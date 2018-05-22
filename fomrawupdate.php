<?php

include "../connections/conn_slotting.php";
include "../globalincludes/usa_asys.php";
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
$ISFOM = 'N';
//this code updates the "fomraw" mysql table 

$fomdates = $conn1->prepare("SELECT FOMDATES from FOMDATES");
$fomdates->execute();
$fomdatesarray = $fomdates->fetchAll(PDO::FETCH_COLUMN);


$whsearray = array(2,3,6,7,9);

foreach ($whsearray as $whse) {
    



$result = $aseriesconn->prepare("select PDWHSE, PDITEM, PDPKGU, CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END, sum(PDPCKQ) from A.HSIPCORDTA.NOTWPT WHERE((CURRENT DATE) -  CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) <= 4 and PDBXSZ <> 'CSE' and PDPKGU <> 0  and LENGTH(RTRIM(TRANSLATE(PDITEM, '*', ' 0123456789'))) = 0 and PDWHSE = $whse  group by PDWHSE, PDITEM, PDPKGU, PDSHPD order by PDWHSE asc , PDITEM asc , PDPKGU asc");
$result->execute();
$resultarray = $result->fetchAll(PDO::FETCH_NUM);


$columns = 'FOMWHSE, FOMITEM, FOMPKGU, FOMDATE, FOMPQTY, ISFOM';


$values = array();

$maxrange = 9999;
$counter = 0;
$rowcount = count($resultarray);

do {
    if ($maxrange > $rowcount) {  //prevent undefined offset
        $maxrange = $rowcount - 1;
    }

    $data = array();
    $values = array();
    while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table
        $whse = intval($resultarray[$counter][0]);
        $item = $resultarray[$counter][1];
        $pkgu = intval($resultarray[$counter][2]);
        $date = $resultarray[$counter][3];
        $pickavg = intval($resultarray[$counter][4]);

        $data[] = "($whse, $item, $pkgu, '$date', $pickavg, '$ISFOM')";
        $counter += 1;
    }


    $values = implode(',', $data);

    if (empty($values)) {
        break;
    }
    $sql = "INSERT INTO slotting.fomraw ($columns) VALUES $values ON DUPLICATE KEY UPDATE FOMPQTY=values(FOMPQTY)";
    $query = $conn1->prepare($sql);
    $query->execute();
    $maxrange += 10000;
} while ($counter <= $rowcount);


//update the FOM flag
$sqlupdate = "UPDATE slotting.fomraw
                                    LEFT JOIN
                                fomdates ON FOMDATE = FOMDATES 
                            SET 
                                ISFOM = 'Y'
                            WHERE
                                FOMDATES IS NOT NULL";
$queryupdate = $conn1->prepare($sqlupdate);
$queryupdate->execute();

}