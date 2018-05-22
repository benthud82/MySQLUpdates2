<!--Code to update the MySQL table "itempickcount"-->
<?php
set_time_limit(99999);
ini_set('mysql.connect_timeout', 99999);
ini_set('default_socket_timeout', 99999);



$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUD01";
$pdo_password = "tucker1234";
$aseriesconn = new PDO(
        $pdo_dsn, $pdo_username, $pdo_password, array(
        )
);

$picks = $aseriesconn->prepare("Select PDWHSE, PDITEM, PDPKGU, case when PDBXSZ = 'CSE'  then 'C' else 'L' end as CORL, count(PDCOMP) as Count FROM A.HSIPCORDTA.NOTWPT WHERE PDPKGU <> 0 and PDWHSE = 7 GROUP BY PDWHSE, PDITEM, PDPKGU, case when PDBXSZ = 'CSE'  then 'C' else 'L' end");
$picks->execute();
$pickssarray = $picks->fetchAll(MYSQL_NUM);

$data = array();


foreach ($pickssarray as $row) {
    $whse = intval($row['PDWHSE']);
    $item = mysql_real_escape_string($row['PDITEM']);
    $pkgu = intval($row['PDPKGU']);
    $corl = mysql_real_escape_string($row['CORL']);
    $count = intval($row['COUNT']);
    $data[] = "($whse, '$item', $pkgu, '$corl', $count)";

    
}



$values = implode(',', $data);


$host = "nahsifljaws01"; // Host name 
$username = "slotadmin"; // Mysql username 
$password = "slotadmin"; // Mysql password 
$db_name = "slotting"; // Database name 
// Connect to server and select database. 
mysql_connect("$host", "$username", "$password") or die("cannot connect");
mysql_select_db("$db_name") or die("cannot select DB");

$sql0 = "Set Global max_allowed_Packet = 999999999M";
mysql_query($sql0);

$sql = "INSERT INTO itempickcount (Whse, ItemCode, Pkgu, CorL, PickCount) VALUES $values";
mysql_query($sql);


