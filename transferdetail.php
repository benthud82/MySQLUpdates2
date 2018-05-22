<?php

set_time_limit(99999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 
$table = "transferdetail"; // Table name
$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array(
                                                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                                                PDO::ATTR_EMULATE_PREPARES   => false,
                                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
$server = "Driver={Client Access ODBC Driver (32-bit)};System=A;Uid=user;Pwd=password;"; #the name of the iSeries
$user = "BHUD01"; #a valid username that will connect to the DB
$pass = "tucker1234"; #a password for the username
$conn = odbc_connect($server, $user, $pass); #you may have to remove quotes
if (!$conn) {
    print db2_conn_errormsg();
}

#Query the Database into a result set - 
$result = odbc_exec($conn, "SELECT NPFPHO.HOWHSE,right(NPFPHO.SUPPLR,2),NPFPDO.ITMCDE, (CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END),NPFPHO.PONUMB FROM HSIPCORDTA.NPFPHO NPFPHO,HSIPCORDTA.NPFPDO NPFPDO WHERE (CASE WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) <= 1  THEN (CURRENT DATE - 20 Days)  WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) > 1 THEN (CURRENT DATE - 20 Days) END) <= (CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) and (NPFPHO.HOWHSE IN ( 2,3,6,7,9 )) AND (NPFPHO.PQTYP2 = 'TR') AND (NPFPHO.SUPPLR IN ( 'WHSE01','WHSE02','WHSE03','WHSE06','WHSE07','WHSE09' )) AND NPFPHO.HOWHSE = NPFPDO.DOWHSE AND NPFPHO.PONUMB = NPFPDO.PONUMB ORDER BY(CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) DESC");

while (odbc_fetch_row($result)) {

    $towhse = intval(odbc_result($result, 1));
    $fromwhs = intval(odbc_result($result, 2));
    $item = intval(odbc_result($result, 3));
    $date = "'" . odbc_result($result, 4) . "'";
    $ponumb = intval(odbc_result($result, 5));
    $dayofweek = intval(date('w', strtotime(odbc_result($result, 4))));

$result1 = $conn1->prepare("INSERT IGNORE INTO $table (ToWhse, FromWhs, TranItem, TranDate, PONumb, DayNum) VALUES ($towhse, $fromwhs, $item, $date, $ponumb, $dayofweek) ");
$result1->execute();

}
$conn1 = null;

?>