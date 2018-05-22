<?php
set_time_limit(99999);
ini_set('memory_limit', '-1');
include ("../globalincludes/nahsi_mysql.php");

// --------- Start of Max Upsize ---------    
//Dallas Max Upsize
$_GET['whse'] = 7;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 7;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();

//Indy Max Upsize
$_GET['whse'] = 2;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 2;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();

//Sparks Max Upsize
$_GET['whse'] = 3;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 3;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();

//Denver Max Upsize
$_GET['whse'] = 6;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 6;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();


//Jax Max Upsize
$_GET['whse'] = 9;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 9;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();

//NOTL Max Upsize
$_GET['whse'] = 11;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 11;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();


//VANC Max Upsize
$_GET['whse'] = 12;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 12;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();


//Calgary Max Upsize
$_GET['whse'] = 16;

//include php file to process opp
include("../globaldata/maxupsize.php");

//assign variable to insert into MySQL table
$whse = 16;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'maxupsize'";
$asomoves = intval($asomoves);
$automoves = intval($automoves);
$totalmoves = intval($totalmoves);
$itemcount = intval($itemcount);

$result1 = $conn1->prepare("INSERT INTO todaypage (Whse, DashDate, DashTable, ASOMoves, AUTOMoves, TotalMoves, ItemCount) VALUES ($whse, $today, $dashtable, $asomoves, $automoves, $totalmoves, $itemcount) ON DUPLICATE KEY UPDATE ASOMoves=VALUES(ASOMoves), AUTOMoves=VALUES(AUTOMoves), TotalMoves=VALUES(TotalMoves), ItemCount=VALUES(ItemCount)");
$result1->execute();
