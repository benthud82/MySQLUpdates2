<?php

set_time_limit(99999);

include '../connections/conn_slotting.php';
include '../globalincludes/usa_asys.php';


$baycube = $conn1->prepare("SELECT NPFPHO.HOWHSE as TOWHSE
                                                            NPFPDO.ITMCDE as ITMCDE,
                                                            NPFPHO.PONUMB,
                                                            (CASE WHEN (NPFPHO.PODATE<99999) 
                                                                            THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) 
                                                                WHEN NPFPHO.PODATE>99999 
                                                                            THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) as PODATE , 
                                                            cast(right(NPFPHO.SUPPLR,2) as int) as FromWhs,
                                                            NPFPDO.PURQTY as XFER_QTY,
                                                            NPFPHO.PQTYP1 
                                                    FROM  HSIPCORDTA.NPFPDO, 
                                                    HSIPCORDTA.NPFPHO 
                                                    WHERE NPFPHO.PQTYP2 = 'TR'  And 
                                                                NPFPHO.HOWHSE = NPFPDO.DOWHSE AND 
                                                                NPFPHO.PONUMB = NPFPDO.PONUMB AND 
                                                                (CASE WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) <= 1  
                                                                    THEN (CURRENT DATE - 3 Days)  WHEN (DAYS(CURRENT DATE) - (DAYS(CURRENT DATE)/7)*7) > 1 
                                                                    THEN (CURRENT DATE - 1 Days) END) = (CASE WHEN (NPFPHO.PODATE<99999) 
                                                                    THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,1) || '-' || substr(NPFPHO.PODATE,2,2))) 
                                                                WHEN NPFPHO.PODATE>99999 
                                                                    THEN (date(('20' || RIGHT(NPFPHO.PODATE,2)) || '-' || substr(NPFPHO.PODATE,1,2) || '-' || substr(NPFPHO.PODATE,3,2))) END) 
                                                            and NPFPHO.HOWHSE in (2,3,6,7,9) and cast(right(NPFPHO.SUPPLR,2) as int) in (2,3,6,7,9)");
$baycube->execute();
$baycubearray = $baycube->fetchAll(pdo::FETCH_ASSOC);



while (odbc_fetch_row($result)) {
    $subarray = array();
    $var_ISOQA = null;
    $var_towhse = null;
    $var_item = null;
    $var_ponumb = null;
    $var_podate = null;
    $var_fromwhs = null;
    $var_qty = null;
    $var_towhsdailydmd = null;
    $var_avgcost = null;
    $var_tooh = null;
    $var_suppleadtime = null;
    $var_towhsedaysOH = null;
    $var_daysdmdxfer = null;
    $var_towhseoutl = null;
    $var_towhseIOP = null;
    $var_towhseohplusxfer = null;
    $var_towhseexess = null;
    $var_overunderexcess = null;
    $var_towhseOHtoIOP = null;
    $var_fromwhsdailydmd = null;
    $var_fromwhseOH = null;
    $var_fromwhseIOP = null;
    $var_fromwhseOHtoIOP = null;
    $cnt = null;
    $var_fromwhsdailydmd = null;
    $var_fromwhseOHB4 = null;
    $var_fromwhseOHafter = null;
    $var_fromwhseIOP = null;
    $var_fromcopo = null;
    $var_fromwhseanndem = null;
    $var_fromwhseoutl = null;
    $var_fromwhseexess = null;
    $var_towhse = intval(odbc_result($result, 1));
    $var_item = intval(odbc_result($result, 2));
    $var_ponumb = odbc_result($result, 3);
    $var_podate = "'" . odbc_result($result, 4) . "'";
    $var_fromwhs = intval(odbc_result($result, 5));
    $var_qty = intval(odbc_result($result, 6));
    $var_type = "'" . odbc_result($result, 7) . "'";



    $var_towhseE3 = odbc_exec($conn, "SELECT cast(IWHSE as int) as WHS,IITEM as ITEM ,ISUPER,IDEM13/28 as DAILYDMD, IMXUNT as OUTLUNIT, IMXDYS as OUTLDAYS, IOPIUN ,IOPIDY as IOPDAY ,ILTFOR as LTFORE, IONHND as ONHAND, ISUPER as COPO, INAME as DESC, IBUYR AS BUYER, IVNDR AS SUPPLIER, IEFFPR AS AVGCOST, IDEM01 as ANNDEM, IDMPRF as PROFILE, ISOQA FROM A.E3TSCHEIN.E3ITEM E3ITEM WHERE ITIEHI = 'STK' and IACTV = 0 and cast(IWHSE as int) = $var_towhse and IITEM = $var_item");




    while (false !== ($row = odbc_fetch_array($var_towhseE3))) {
        $var_towhsdailydmd = floatval(odbc_result($var_towhseE3, "DAILYDMD"));
        $var_avgcost = floatval((odbc_result($var_towhseE3, "AVGCOST")));



        $var_tooh = intval(odbc_result($var_towhseE3, "ONHAND"));
        $var_suppleadtime = intval(odbc_result($var_towhseE3, "LTFORE"));
        if ($var_towhsdailydmd != 0) {
            $var_towhsedaysOH = floatval((odbc_result($var_towhseE3, "ONHAND")) / $var_towhsdailydmd);
        } else {
            $var_towhsedaysOH = 0; // or print an error or whatever
        }

        if ($var_towhsdailydmd != 0) {
            $var_daysdmdxfer = intval(($var_qty) / ($var_towhsdailydmd));
        } else {
            $var_daysdmdxfer = 0; // or print an error or whatever
        }

        $var_towhseoutl = intval(odbc_result($var_towhseE3, "OUTLUNIT"));
        $var_towhseIOP = intval(odbc_result($var_towhseE3, "IOPIUN"));
        $var_towhseprof = intval(odbc_result($var_towhseE3, "PROFILE"));
        $var_towhseohplusxfer = intval($var_qty) + intval($var_tooh);
        $var_towhseanndem = odbc_result($var_towhseE3, "ANNDEM");
        $var_towhseexess = intval(intval($var_towhseoutl) + (90 * ($var_towhseanndem / 365)));
        $var_overunderexcess = intval($var_towhseohplusxfer - $var_towhseexess);
        $var_ISOQA = intval(odbc_result($var_towhseE3, "ISOQA"));

        if ($var_towhseIOP != 0) {
            $var_towhseOHtoIOP = floatval(($var_tooh / $var_towhseIOP));
        } else {
            $var_towhseOHtoIOP = 0; // or print an error or whatever
        }
    }
//                            if (!odbc_num_rows($var_towhseE3)) {
//                                continue;
//                            }

    if ($var_towhsdailydmd == null) {
        continue;
    }

    $var_fromwhseE3 = odbc_exec($conn, "SELECT cast(IWHSE as int) as WHS,IITEM as ITEM ,ISUPER,IDEM13/28 as DAILYDMD, IMXUNT as OUTLUNIT, IMXDYS as OUTLDAYS, IOPIUN ,IOPIDY as IOPDAY ,ILTFOR as LTFORE, IONHND as ONHAND, ISUPER as COPO, INAME as DESC, IBUYR AS BUYER, IVNDR AS SUPPLIER, IEFFPR AS AVGCOST, IDEM01 as ANNDEM FROM A.E3TSCHEIN.E3ITEM E3ITEM WHERE ITIEHI = 'STK' and IACTV = 0 and cast(IWHSE as int) = $var_fromwhs and IITEM = $var_item");

    while (false !== ($row = odbc_fetch_array($var_fromwhseE3))) {
        $var_fromwhsdailydmd = floatval(odbc_result($var_fromwhseE3, "DAILYDMD"));
        $var_fromwhseOHB4 = intval(odbc_result($var_fromwhseE3, "ONHAND") + $var_qty);
        $var_fromwhseOHafter = intval(odbc_result($var_fromwhseE3, "ONHAND"));
        $var_fromwhseIOP = intval(odbc_result($var_fromwhseE3, "IOPIUN"));
        $var_fromcopo = "'" . odbc_result($var_fromwhseE3, "ISUPER") . "'";
        $var_fromwhseanndem = odbc_result($var_fromwhseE3, "ANNDEM");
        $var_fromwhseoutl = intval(odbc_result($var_fromwhseE3, "OUTLUNIT"));
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
    if ($var_fromwhseOHB4 == null) {
        $var_fromwhsdailydmd = 0;
        $var_fromwhseOHB4 = 0;
        $var_fromwhseOHafter = 0;
        $var_fromwhseIOP = 0;
        $var_fromcopo = "''";
        $var_fromwhseanndem = 0;
        $var_fromwhseexess = 0;
        $var_fromwhseOHtoIOP = 0;
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

    $result1 = $conn1->prepare("INSERT IGNORE INTO $table (ToWhse, Item, PODate, FromWhs, XferQty, ToDailyDmd,SuppLT,ToWhsSOQ,ToOnHand,ToDaysOH,DaysXfer,ToOUTL,ToIOP,ToOHtoIOP,ToOHPlusXfer,ToExcess,ToProfile,ToOUExcess,FromDailyDmd,FromOHB4Xfer,FromOHAfterXfer,FromIOP,FromOHtoIOP,FromExcess,FromCOPO,POType,Grade) VALUES ($var_towhse, $var_item, $var_podate, $var_fromwhs, $var_qty, $var_towhsdailydmd, $var_suppleadtime,$var_ISOQA, $var_tooh, $var_towhsedaysOH, $var_daysdmdxfer, $var_towhseoutl, $var_towhseIOP, $var_towhseOHtoIOP, $var_towhseohplusxfer, $var_towhseexess, $var_towhseprof, $var_overunderexcess, $var_fromwhsdailydmd, $var_fromwhseOHB4, $var_fromwhseOHafter, $var_fromwhseIOP, $var_fromwhseOHtoIOP, $var_fromwhseexess, $var_fromcopo, $var_type, $var_finalgrade)");


    if (!$result1) {
        die('Could not update data: ' . mysql_error());
    }

    $result1->execute();
}

