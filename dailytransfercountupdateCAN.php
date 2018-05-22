<!--Code to update the MySQL tables that count ASO and AUTO moves by day-->

<?php
set_time_limit(99999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 
$table = "transferdetailCAN"; // Table name
$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array(
                                                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                                                PDO::ATTR_EMULATE_PREPARES   => false,
                                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));


$result1 = $conn1->prepare("DROP TABLE IF EXISTS transferdailycountCAN");
$result1->execute();

$result2 = $conn1->prepare("CREATE TABLE transferdailycountCAN(TranDate DATE, 11Count INT, 12Count INT, 16Count INT, TranCount INT)");
$result2->execute();

$result3 = $conn1->prepare("INSERT INTO transferdailycountCAN(TranDate, 11Count, 12Count, 16Count, TranCount) SELECT TranDate, SUM(CASE WHEN FromWhs = 11 THEN 1 ELSE 0 END) as Whs11, SUM(CASE WHEN FromWhs = 12 THEN 1 ELSE 0 END) as Whs12, SUM(CASE WHEN FromWhs = 16 THEN 1 ELSE 0 END) as Whs16, COUNT(ToWhse) FROM transferdetailCAN WHERE FromWhs <> 1 and DayNum between 1 and 3 GROUP BY TranDate");
$result3->execute();

//
//mysql_query($sql1);
//mysql_query($sql2);
//mysql_query($sql3);


$conn1 = null;
?>

