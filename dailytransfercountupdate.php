<!--Code to update the MySQL tables that count ASO and AUTO moves by day-->

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


$result1 = $conn1->prepare("DROP TABLE IF EXISTS transferdailycount");
$result1->execute();

$result2 = $conn1->prepare("CREATE TABLE transferdailycount(TranDate DATE, 2Count INT, 3Count INT, 6Count INT, 7Count INT, 9Count INT, TranCount INT)");
$result2->execute();

$result3 = $conn1->prepare("INSERT INTO transferdailycount(TranDate, 2Count, 3Count, 6Count, 7Count, 9Count, TranCount) SELECT TranDate, SUM(CASE WHEN FromWhs = 2 THEN 1 ELSE 0 END) as Whs2, SUM(CASE WHEN FromWhs = 3 THEN 1 ELSE 0 END) as Whs3, SUM(CASE WHEN FromWhs = 6 THEN 1 ELSE 0 END) as Whs6, SUM(CASE WHEN FromWhs = 7 THEN 1 ELSE 0 END) as Whs7, SUM(CASE WHEN FromWhs = 9 THEN 1 ELSE 0 END) as Whs9, COUNT(ToWhse) FROM transferdetail WHERE FromWhs <> 1 and DayNum between 1 and 3 GROUP BY TranDate");
$result3->execute();

//
//mysql_query($sql1);
//mysql_query($sql2);
//mysql_query($sql3);


$conn1 = null;
?>

