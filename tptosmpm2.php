<?php
set_time_limit(99999);
ini_set('memory_limit', '-1');
include ("../globalincludes/nahsi_mysql.php");

// --------- Start of To SMPM ---------    
//Dallas to SMPM
$_GET['whse'] = 2;

//include php file to process opp
include("../globaldata/tosmpm.php");

//assign variable to insert into MySQL table
$whse = 2;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'tosmpm'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();

