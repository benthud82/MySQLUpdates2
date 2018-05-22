<?php

set_time_limit(99999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 

$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array());
$conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tbl_name = "transferdetailCAN"; // Table name

$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUDS1";
$pdo_password = "tucker1234";
$aseriesconn = new PDO($pdo_dsn, $pdo_username, $pdo_password, array());

//Pull in transfers that have already been added to table
$excl = $conn1->prepare("SELECT CONCAT(ToWhse,FromWhs,TranItem,TranDate,PONumb) as EXCLKEY FROM transferdetailCAN WHERE TranDate BETWEEN DATE_SUB(NOW(), INTERVAL 13 DAY) AND NOW()");
$excl->execute();
$exclarray = $excl->fetchAll(PDO::FETCH_NUM);


//create array of exclusion items
$output = array();
foreach ($exclarray as $current) {
    // create the array key if it doesn't exist already
    if (!array_key_exists($current[0], $output)) {
        $output[$current[0]] = 1;
    }
}


//Query the Database into a result set - 
$result = $aseriesconn->prepare("SELECT NPFPHO.HOWHSE,right(NPFPHO.SUPPLR,2),NPFPDO.ITMCDE, (CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END),NPFPHO.PONUMB FROM ARCPCORDTA.NPFPHO NPFPHO,ARCPCORDTA.NPFPDO NPFPDO WHERE (CASE WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) <= 1  THEN (CURRENT DATE - 7 Days)  WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) > 1 THEN (CURRENT DATE - 7 Days) END) <= (CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) and (NPFPHO.HOWHSE IN ( 11,12,16 )) AND (NPFPHO.PQTYP2 = 'TR') AND (NPFPHO.SUPPLR IN ( 'WHSE11','WHSE12','WHSE16')) AND NPFPHO.HOWHSE = NPFPDO.DOWHSE AND NPFPHO.PONUMB = NPFPDO.PONUMB ORDER BY(CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) DESC");
$result->execute();
$resultarray = $result->fetchAll(PDO::FETCH_NUM);


foreach ($resultarray as $key => $value) {

    $towhse = intval($resultarray[$key][0]);
    $fromwhs = intval($resultarray[$key][1]);
    $item = intval($resultarray[$key][2]);
    $date = $resultarray[$key][3];
    $ponumb = intval($resultarray[$key][4]);
    $dayofweek = intval(date('w', strtotime($resultarray[$key][3])));


    $testexcl = $towhse . $fromwhs . $item . $date . $ponumb;

    if (!array_key_exists($testexcl, $output)) {

        $sql = "INSERT INTO $tbl_name (ToWhse, FromWhs, TranItem, TranDate, PONumb, DayNum) VALUES (:ToWhse, :FromWhs, :TranItem, :TranDate, :PONumb, :DayNum)";
        $query = $conn1->prepare($sql);
        $query->execute(array(':ToWhse' => $towhse, ':FromWhs' => $fromwhs, ':TranItem' => $item, ':TranDate' => $date, ':PONumb' => $ponumb, ':DayNum' => $dayofweek));
    }

}
