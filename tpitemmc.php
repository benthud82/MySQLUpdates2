<?php
set_time_limit(99999);
ini_set('memory_limit', '-1');
include ("../globalincludes/nahsi_mysql.php");

// --------- Start of MC Percentages ---------
//Dallas MC percent
$_GET['whse'] = 7;

//include php file to process opp
include("../globaldata/movementclassissues.php");


//assign variable to insert into MySQL table
$whse = 7;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();


//Indy MC percent
$_GET['whse'] = 2;

//include php file to process opp
include("../globaldata/movementclassissues.php");


//assign variable to insert into MySQL table
$whse = 2;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();


//Sparks MC percent
$_GET['whse'] = 3;

//include php file to process opp
include("../globaldata/movementclassissues.php");


//assign variable to insert into MySQL table
$whse = 3;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();


//Denver MC percent
$_GET['whse'] = 6;

//include php file to process opp
include("../globaldata/movementclassissues.php");


//assign variable to insert into MySQL table
$whse = 6;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();

//Jax MC percent
$_GET['whse'] = 9;

//include php file to process opp
include("../globaldata/movementclassissues.php");


//assign variable to insert into MySQL table
$whse = 9;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();

//NOTL MC percent
$_GET['whse'] = 11;

//include php file to process opp
include("../globaldata/movementclassissues.php");

//assign variable to insert into MySQL table
$whse = 11;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();

//VANC MC percent
$_GET['whse'] = 12;

//include php file to process opp
include("../globaldata/movementclassissues.php");

//assign variable to insert into MySQL table
$whse = 12;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();


//Calgary MC percent
$_GET['whse'] = 16;

//include php file to process opp
include("../globaldata/movementclassissues.php");
//assign variable to insert into MySQL table
$whse = 16;
$today = "'" . date('Y-m-d') . "'";
$dashtable = "'mcissues'";
$varAcorrectpercent = floatval($varAcorrectpercent);
$varBcorrectpercent = floatval($varBcorrectpercent);
$varCcorrectpercent = floatval($varCcorrectpercent);
$varDcorrectpercent = floatval($varDcorrectpercent);
$varEcorrectpercent = floatval($varEcorrectpercent);
$varFcorrectpercent = floatval($varFcorrectpercent);
$varGcorrectpercent = floatval($varGcorrectpercent);
$varHcorrectpercent = floatval($varHcorrectpercent);
$varIcorrectpercent = floatval($varIcorrectpercent);
$varJcorrectpercent = floatval($varJcorrectpercent);
$varKcorrectpercent = floatval($varKcorrectpercent);
$varLcorrectpercent = floatval($varLcorrectpercent);
$varMcorrectpercent = floatval($varMcorrectpercent);
$varTotalCorrect = floatval($varTotalCorrect);

$result1 = $conn1->prepare("INSERT INTO mcperctodaypage (Whse, DashDate, DashTable, Aperc, Bperc, Cperc, Dperc, Eperc, Fperc, Gperc, Hperc, Iperc, Jperc, Kperc, Lperc, Mperc, Totalperc) VALUES ($whse, $today, $dashtable, $varAcorrectpercent, $varBcorrectpercent, $varCcorrectpercent, $varDcorrectpercent, $varEcorrectpercent, $varFcorrectpercent, $varGcorrectpercent, $varHcorrectpercent, $varIcorrectpercent, $varJcorrectpercent, $varKcorrectpercent, $varLcorrectpercent, $varMcorrectpercent, $varTotalCorrect) ON DUPLICATE KEY UPDATE Aperc=VALUES(Aperc), Bperc=VALUES(Bperc), Cperc=VALUES(Cperc), Dperc=VALUES(Dperc), Eperc=VALUES(Eperc), Fperc=VALUES(Fperc), Gperc=VALUES(Gperc), Hperc=VALUES(Hperc), Iperc=VALUES(Iperc), Jperc=VALUES(Jperc), Kperc=VALUES(Kperc), Lperc=VALUES(Lperc), Mperc=VALUES(Mperc), Totalperc=VALUES(Totalperc)");
$result1->execute();

