<!--Code to update the MySQL table "itempickcount"-->
<?php
set_time_limit(99999);
$host = "nahsifljaws01"; // Host name 
$username = "slotadmin"; // Mysql username 
$password = "slotadmin"; // Mysql password 
$db_name = "slotting"; // Database name 
// Connect to server and select database. 
mysql_connect("$host", "$username", "$password") or die("cannot connect");
mysql_select_db("$db_name") or die("cannot select DB");


$server = "Driver={Client Access ODBC Driver (32-bit)};System=A;Uid=user;Pwd=password;"; #the name of the iSeries
$user = "BHUD01"; #a valid username that will connect to the DB
$pass = "tucker1234"; #a password for the username

$conn = odbc_connect($server, $user, $pass); #you may have to remove quotes
if (!$conn) {
    print db2_conn_errormsg();
}
$invlinesupdatearray = array();

#Query the Database into a result set - 
$result = odbc_exec($conn, "SELECT VCWHSE, VCITEM, VCPKGU, substr(VCFTIR, 1,1), case when temp.Count >= 1 then temp.Count else 0 end as newCount FROM A.HSIPCORDTA.NPFMVC LEFT OUTER JOIN (Select  PDWHSE||PDITEM||PDPKGU||case when PDBXSZ = 'CSE'  then 'C' else 'L' end as KEY, count(PDCOMP) as Count FROM A.HSIPCORDTA.NOTWPT GROUP BY PDWHSE||PDITEM||PDPKGU|| case when PDBXSZ = 'CSE'  then 'C' else 'L' end) temp on KEY = VCWHSE||VCITEM||VCPKGU|| substr(VCFTIR, 1,1) WHERE  VCPKGU = 1 and VCCSLS = 'L' and VCFTIR = 'L04' and case when temp.Count >= 1 then temp.Count else 0 end < 6");

while (odbc_fetch_row($result)) {

    $var_PDWHSE = intval(odbc_result($result, 1));
    $var_ITEM = "'" . odbc_result($result, 2) . "'";
    $var_PKGU = intval(odbc_result($result, 3));
    $var_CORL = "'" . odbc_result($result, 4) . "'";
    $var_PICK = intval(odbc_result($result, 5));



    // update data in mysql database 
    $sql = "INSERT INTO toSMPMitempickcount (Whse, ItemCode, Pkgu, CorL, PickCount) VALUES ($var_PDWHSE, $var_ITEM, $var_PKGU, $var_CORL, $var_PICK) ";
    mysql_query($sql);
}
$sql2 = "ALTER IGNORE TABLE toSMPMitempickcount ADD UNIQUE (Whse, ItemCode, Pkgu, CorL)";
mysql_query($sql2);

