
<?php

include_once '../globalincludes/usa_asys.php';
include_once '../globalincludes/nahsi_mysql.php';
include_once '../globalfunctions/custdbfunctions.php';
$today = date('Y-m-d');

$startday = date('Y-m-d', (strtotime('-8 days', strtotime($today))));
$startjday = _gregdatetoyyddd($startday);


$invlinesupdate = $aseriesconn->prepare("SELECT PDWHSE as WHSE, PBSHJD as JDATE, count(PDCOMP) as INVLINES FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NOTWPS WHERE PBCOMP = PDCOMP and PBWHSE = PDWHSE and PBWCS# = PDWCS# and PBWKNO = PDWKNO and PBBOX# = PDBOX# and PDLOC# <> '*MSDS' AND PDPCKL - ROUND(PDPCKL,0) = 0 and PDWHSE = 2 and PBSHJD >= $startjday GROUP BY PBSHJD, PDWHSE ORDER BY PBSHJD ASC, PDWHSE ASC");
$invlinesupdate->execute();
$invlinesupdatearray = $invlinesupdate->fetchAll(PDO::FETCH_ASSOC);

$columns = 'INVWHSE, INVDATE, INVLINES';

foreach ($invlinesupdatearray as $key => $value) {
    $var_PDWHSE = intval($invlinesupdatearray[$key]['WHSE']);
    $var_JDATE = intval($invlinesupdatearray[$key]['JDATE']);
    $var_INVLINES = intval($invlinesupdatearray[$key]['INVLINES']);

    $year = "20" . substr($var_JDATE, 0, 2);
    $days = substr($var_JDATE, 2, 3);

    $ts = mktime(0, 0, 0, 1, $days, $year);
    $mydate = "'" . date('Y-m-d', $ts) . "'";

    $data[] = "($var_PDWHSE, $mydate, $var_INVLINES)";
}
$values = array();
$values = implode(',', $data);

$sql = "INSERT IGNORE INTO 2invlinesshipped ($columns) VALUES $values";
$query = $conn1->prepare($sql);
$query->execute();
