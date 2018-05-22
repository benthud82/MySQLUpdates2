<?php

set_time_limit(99999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 
$table = "transfergradesCAN"; // Table name
$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));



$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUDS1";
$pdo_password = "tucker1234";
$aseriesconn = new PDO($pdo_dsn, $pdo_username, $pdo_password, array());

$result = $aseriesconn->prepare("SELECT NPFPHO.HOWHSE as TOWHSE,NPFPDO.ITMCDE as ITMCDE,NPFPHO.PONUMB,(CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) as PODATE , cast(right(NPFPHO.SUPPLR,2) as int) as FromWhs,NPFPDO.PURQTY as XFER_QTY,NPFPHO.PQTYP1 FROM  ARCPCORDTA.NPFPDO NPFPDO, ARCPCORDTA.NPFPHO NPFPHO WHERE NPFPHO.PQTYP2 = 'TR'  And NPFPHO.HOWHSE = NPFPDO.DOWHSE AND NPFPHO.PONUMB = NPFPDO.PONUMB AND (CASE WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) <= 1  THEN (CURRENT DATE - 3 Days)  WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) > 1 THEN (CURRENT DATE - 1 Days) END) = (CASE WHEN (NPFPHO.PODATE<99999) THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) WHEN NPFPHO.PODATE>99999 THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) and NPFPHO.HOWHSE in (11,12,16) and cast(right(NPFPHO.SUPPLR,2) as int) in (11,12,16)");
$result->execute();
$resultarray = $result->fetchAll(PDO::FETCH_NUM);

foreach ($resultarray as $key => $value) {
    $var_towhse = intval($resultarray[$key][0]);
    $var_item = intval($resultarray[$key][1]);
    $var_ponumb = intval($resultarray[$key][2]);
    $var_podate = $resultarray[$key][3];
    $var_fromwhs = intval($resultarray[$key][4]);
    $var_qty = intval($resultarray[$key][5]);
    $var_type = $resultarray[$key][6];


    $towhseresult = $aseriesconn->prepare("SELECT cast(IWHSE as int) as WHS,IITEM as ITEM ,ISUPER,IDEM13/28 as DAILYDMD, IMXUNT as OUTLUNIT, IMXDYS as OUTLDAYS, IOPIUN ,IOPIDY as IOPDAY ,ILTFOR as LTFORE, IONHND as ONHAND, ISUPER as COPO, INAME as DESC, IBUYR AS BUYER, IVNDR AS SUPPLIER, IEFFPR AS AVGCOST, IDEM01 as ANNDEM, IDMPRF as PROFILE, ISOQA FROM A.E3TARC.E3ITEM E3ITEM WHERE ITIEHI = 'STK' and IACTV = 0 and cast(IWHSE as int) = $var_towhse and IITEM = '".$var_item."'");
    $towhseresult->execute();
    $towhseresultarray = $towhseresult->fetchAll(PDO::FETCH_ASSOC);

    //if nothing at to whse, continue
    $towhsecount = count($towhseresultarray);
    if ($towhsecount === 0) {
        continue;
    }

    foreach ($towhseresultarray as $key2 => $value) {
        $var_towhsdailydmd = floatval($towhseresultarray[$key2]['DAILYDMD']);
        $var_avgcost = floatval($towhseresultarray[$key2]['AVGCOST']);
        $var_tooh = intval($towhseresultarray[$key2]['ONHAND']);
        $var_suppleadtime = intval($towhseresultarray[$key2]['LTFORE']);

        if ($var_towhsdailydmd != 0) {
            $var_towhsedaysOH = floatval(($var_tooh) / $var_towhsdailydmd);
        } else {
            $var_towhsedaysOH = 0; // or print an error or whatever
        }


        if ($var_towhsdailydmd != 0) {
            $var_daysdmdxfer = intval(($var_qty) / ($var_towhsdailydmd));
        } else {
            $var_daysdmdxfer = 0; // or print an error or whatever
        }

        $var_towhseoutl = intval($towhseresultarray[$key2]['OUTLUNIT']);
        $var_towhseIOP = intval($towhseresultarray[$key2]['IOPIUN']);
        $var_towhseprof = intval($towhseresultarray[$key2]['PROFILE']);
        $var_towhseohplusxfer = intval($var_qty) + intval($var_tooh);
        $var_towhseanndem = intval($towhseresultarray[$key2]['ANNDEM']);
        $var_towhseexess = intval(intval($var_towhseoutl) + (90 * ($var_towhseanndem / 365)));
        $var_overunderexcess = intval($var_towhseohplusxfer - $var_towhseexess);
        $var_ISOQA = intval($towhseresultarray[$key2]['ISOQA']);

        if ($var_towhseIOP != 0) {
            $var_towhseOHtoIOP = floatval(($var_tooh / $var_towhseIOP));
        } else {
            $var_towhseOHtoIOP = 0; // or print an error or whatever
        }
    }


    if ($var_towhsdailydmd == null) {
        continue;
    }

    $fromwhseresult = $aseriesconn->prepare("SELECT cast(IWHSE as int) as WHS,IITEM as ITEM ,ISUPER,IDEM13/28 as DAILYDMD, IMXUNT as OUTLUNIT, IMXDYS as OUTLDAYS, IOPIUN ,IOPIDY as IOPDAY ,ILTFOR as LTFORE, IONHND as ONHAND, ISUPER as COPO, INAME as DESC, IBUYR AS BUYER, IVNDR AS SUPPLIER, IEFFPR AS AVGCOST, IDEM01 as ANNDEM FROM A.E3TARC.E3ITEM E3ITEM WHERE ITIEHI = 'STK' and cast(IWHSE as int) = $var_fromwhs and IITEM = '" . $var_item . "'");
    $fromwhseresult->execute();
    $fromwhseresultarray = $fromwhseresult->fetchAll(PDO::FETCH_ASSOC);

    //if nothing at from whse, continue
    $fromwhsecount = count($fromwhseresultarray);
    if ($fromwhsecount === 0) {
        continue;
    }

    foreach ($fromwhseresultarray as $key3 => $value) {

        $var_fromwhsdailydmd = floatval($fromwhseresultarray[$key3]['DAILYDMD']);
        $var_fromwhseOHB4 = intval($fromwhseresultarray[$key3]['ONHAND'] + $var_qty);
        $var_fromwhseOHafter = intval($fromwhseresultarray[$key3]['ONHAND']);
        $var_fromwhseIOP = intval($fromwhseresultarray[$key3]['IOPIUN']);
        $var_fromcopo = $fromwhseresultarray[$key3]['ISUPER'];
        $var_fromwhseanndem = $fromwhseresultarray[$key3]['ANNDEM'];
        $var_fromwhseoutl = intval($fromwhseresultarray[$key3]['OUTLUNIT']);


        if ($var_fromwhseanndem != 0) {
            $var_fromwhseexess = intval(intval($var_fromwhseoutl) + (90 * ($var_fromwhseanndem / 365)));
        } else {
            $var_fromwhseexess = 0; // or print an error or whatever
        }



        if ($var_fromwhseIOP != 0) {
            $var_fromwhseOHtoIOP = floatval(($var_fromwhseOHafter / $var_fromwhseIOP));
        } else {
            $var_fromwhseOHtoIOP = 0; // or print an error or whatever
        }
    }


    if ($var_fromcopo == '') {
        $var_fromcopo = "'-'";
    }

    // Individual Grade Calculation
    // To Whse OH in days grade
    if ($var_towhsedaysOH >= 120) {
        $var_grade_towhsohdays = 0;
    } elseif ($var_towhsedaysOH >= 90) {
        $var_grade_towhsohdays = .25;
    } elseif ($var_towhsedaysOH >= 60) {
        $var_grade_towhsohdays = .5;
    } elseif ($var_towhsedaysOH >= 30) {
        $var_grade_towhsohdays = .75;
    } else {
        $var_grade_towhsohdays = 1;
    }


    // To whse OH to IOP percent
    if ($var_towhseOHtoIOP > 100) {
        $var_grade_towhseOHtoIOP = 0;
    } else {
        $var_grade_towhseOHtoIOP = 1;
    }


    // To whse OH to excess after xfer %

    if ($var_towhseexess != 0) {
        $var_ohtoexcess = floatval($var_towhseohplusxfer / $var_towhseexess);
    } else {
        $var_ohtoexcess = 0; // or print an error or whatever
    }


    if ($var_ohtoexcess > 1.2) {
        $var_grade_ohtoexcess = 0;
    } elseif ($var_ohtoexcess > 1) {
        $var_grade_ohtoexcess = .25;
    } else {
        $var_grade_ohtoexcess = 1;
    }


    // Days dmd transferred
    if ($var_daysdmdxfer > 23) {
        $var_grade_daysdmdxfer = 1;
    } elseif ($var_daysdmdxfer > 20) {
        $var_grade_daysdmdxfer = .75;
    } else {
        $var_grade_daysdmdxfer = 0;
    }


    // From Whs OH to IOP after xfer Percent

    if ($var_fromwhseOHtoIOP > 200) {
        $var_grade_fromwhseOHtoIOP = 1;
    } elseif ($var_fromwhseOHtoIOP > 175) {
        $var_grade_fromwhseOHtoIOP = .75;
    } elseif ($var_fromwhseOHtoIOP > 150) {
        $var_grade_fromwhseOHtoIOP = .5;
    } elseif ($var_fromwhseOHtoIOP > 100) {
        $var_grade_fromwhseOHtoIOP = .25;
    } else {
        $var_grade_fromwhseOHtoIOP = 0;
    }

    //Final Grade Sum
    $var_finalgrade = floatval(($var_grade_towhsohdays * .2) + ($var_grade_towhseOHtoIOP * .2) + ($var_grade_ohtoexcess * .2) + ($var_grade_daysdmdxfer * .2) + ($var_grade_fromwhseOHtoIOP * .2));
    //    $subarray["$id"] = [$var_towhse, $var_item, $var_podate, $var_fromwhs, $var_qty, $var_towhsdailydmd, $var_suppleadtime, $var_tooh, $var_towhsedaysOH, $var_daysdmdxfer, $var_towhseoutl, $var_towhseIOP, $var_towhseOHtoIOP, $var_towhseohplusxfer, $var_towhseexess, $var_towhseprof, $var_overunderexcess, $var_fromwhsdailydmd, $var_fromwhseOHB4, $var_fromwhseOHafter, $var_fromwhseIOP, $var_fromwhseOHtoIOP, $var_fromwhseexess, $var_fromcopo, $var_type, $var_finalgrade];


    $sql = "INSERT IGNORE INTO $table (ToWhse, Item, PODate, FromWhs, XferQty, ToDailyDmd, SuppLT, ToWhsSOQ, ToOnHand, ToDaysOH, DaysXfer, ToOUTL, ToIOP, ToOHtoIOP, ToOHPlusXfer, ToExcess, ToProfile, ToOUExcess, FromDailyDmd, FromOHB4Xfer, FromOHAfterXfer, FromIOP, FromOHtoIOP, FromExcess, FromCOPO, POType, Grade) VALUES (:ToWhse, :Item, :PODate, :FromWhs, :XferQty, :ToDailyDmd, :SuppLT, :ToWhsSOQ, :ToOnHand, :ToDaysOH, :DaysXfer, :ToOUTL, :ToIOP, :ToOHtoIOP, :ToOHPlusXfer, :ToExcess, :ToProfile, :ToOUExcess, :FromDailyDmd, :FromOHB4Xfer, :FromOHAfterXfer, :FromIOP, :FromOHtoIOP, :FromExcess, :FromCOPO, :POType, :Grade)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':ToWhse' => $var_towhse, ':Item' => $var_item, ':PODate' => $var_podate, ':FromWhs' => $var_fromwhs, ':XferQty' => $var_qty, ':ToDailyDmd' => $var_towhsdailydmd, ':SuppLT' => $var_suppleadtime, ':ToWhsSOQ' => $var_ISOQA, ':ToOnHand' => $var_tooh, ':ToDaysOH' => $var_towhsedaysOH, ':DaysXfer' => $var_daysdmdxfer, ':ToOUTL' => $var_towhseoutl, ':ToIOP' => $var_towhseIOP, ':ToOHtoIOP' => $var_towhseOHtoIOP, ':ToOHPlusXfer' => $var_towhseohplusxfer, ':ToExcess' => $var_towhseexess, ':ToProfile' => $var_towhseprof, ':ToOUExcess' => $var_overunderexcess, ':FromDailyDmd' => $var_fromwhsdailydmd, ':FromOHB4Xfer' => $var_fromwhseOHB4, ':FromOHAfterXfer' => $var_fromwhseOHafter, ':FromIOP' => $var_fromwhseIOP, ':FromOHtoIOP' => $var_fromwhseOHtoIOP, ':FromExcess' => $var_fromwhseexess, ':FromCOPO' => $var_fromcopo, ':POType' => $var_type, ':Grade' => $var_finalgrade));

}

