<?php

set_time_limit(99999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 
$table = "transfergrades"; // Table name
$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));



$result1 = $conn1->prepare("SHOW TABLE STATUS FROM slotting ORDER BY Update_time desc");
$result1->execute();
foreach ($result1 as $msrow) {
    $tablename = $msrow['Name'];
    $picktimediff = $msrow['Update_time'];
    
    echo "$tablename   ";
       
    echo "$picktimediff <br>";
    
    
}