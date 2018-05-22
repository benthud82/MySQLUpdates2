<?php

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
include_once '../globalincludes/nahsi_mysql.php';
//include_once '../globalincludes/ustxgpslotting_mysql.php';
include_once '../globalincludes/usa_asys.php';
include_once '../globalfunctions/slottingfunctions.php';
include_once '../globalfunctions/newitem.php';


$sqldelete = "TRUNCATE slotting.casepicks";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$sqldelete2 = "TRUNCATE slotting.casehighopp";
$querydelete2 = $conn1->prepare($sqldelete2);
$querydelete2->execute();

$sqldelete3 = "TRUNCATE slotting.caseprimaries";
$querydelete3 = $conn1->prepare($sqldelete3);
$querydelete3->execute();



$whsarray = array(2, 3, 6, 7, 9);

foreach ($whsarray as $whse) {

    $columns = 'casepicks_whse, casepicks_item, casepicks_pkgu, casepicks_loc, casepicks_month, casepicks_year';

    $casesql = $aseriesconn->prepare("SELECT PDWHSE, PDITEM, PDPKGU, PDLOC#, sum(CASE WHEN( CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 30 Days) then 1 else 0 end) as CASE_LINES_30, sum(CASE WHEN( CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 365 Days) then 1 else 0 end) as CASE_LINES_365 FROM A.HSIPCORDTA.NOTWPT WHERE PDWHSE = $whse AND PDBXSZ='CSE' GROUP BY PDWHSE, PDITEM, PDLOC#, PDPKGU HAVING sum(CASE WHEN( CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 365 Days) then 1 else 0 end) > 0");
    $casesql->execute();
    $casesqlarray = $casesql->fetchAll(pdo::FETCH_ASSOC);


    $maxrange = 20000;
    $counter = 0;
    $rowcount = count($casesqlarray);

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
            $casepicks_whse = intval($casesqlarray[$counter]['PDWHSE']);
            $casepicks_item = intval($casesqlarray[$counter]['PDITEM']);
            $casepicks_pkgu = intval($casesqlarray[$counter]['PDPKGU']);
            $casepicks_loc = ($casesqlarray[$counter]['PDLOC#']);
            $casepicks_month = intval($casesqlarray[$counter]['CASE_LINES_30']);
            $casepicks_year = intval($casesqlarray[$counter]['CASE_LINES_365']);




            $data[] = "($casepicks_whse, $casepicks_item, $casepicks_pkgu, '$casepicks_loc', $casepicks_month, $casepicks_year)";
            $counter += 1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.casepicks ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange += 20000;
    } while ($counter <= $rowcount); //end of item by whse loop
//update case items with a primary
    $caseprimsql = $aseriesconn->prepare("SELECT DISTINCT LMWHSE, LMITEM FROM A.HSIPCORDTA.NPFLSM WHERE LMTIER LIKE 'C%' AND LMLOC# NOT LIKE 'Q%' and LMWHSE = $whse");
    $caseprimsql->execute();
    $caseprimarray = $caseprimsql->fetchAll(pdo::FETCH_ASSOC);


    $maxrange = 20000;
    $counter = 0;
    $rowcount = count($caseprimarray);

    $columns = 'caseprim_whse, caseprim_item';

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
            $casepicks_whse = intval($caseprimarray[$counter]['LMWHSE']);
            $casepicks_item = intval($caseprimarray[$counter]['LMITEM']);
            $data[] = "($casepicks_whse, $casepicks_item)";
            $counter += 1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.caseprimaries ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange += 20000;
    } while ($counter <= $rowcount); //end of item by whse loop
//Pull in opportunity, run case model to determine cost savings if moved down and store in table
//assumptions
    $deckpickLPH = 110;
    $palletpickLPH = 110;
    $reservepickLPH = 55;
    $palletreplen = 8;
    $deckreplen = 15;
    $palletminpercent = .8;


    $caseopp = $conn1->prepare("SELECT 
                                                            casepicks_whse,
                                                            casepicks_item,
                                                            casepicks_pkgu,
                                                            CPCCWID,
                                                            CPCCLEN,
                                                            CPCCHEI,
                                                            SUM(casepicks_month) AS tot_mnth,
                                                            SUM(casepicks_year) AS tot_year,
                                                            PERC_PERC,
                                                            AVG_INV_OH,
                                                            AVGD_BTW_SLE,
                                                            SHIP_QTY_MN
                                                        FROM
                                                            slotting.casepicks
                                                                LEFT JOIN
                                                            slotting.case_floor_locs ON casepicks_whse = WHSE
                                                                AND casepicks_loc = LOCATION
                                                                LEFT JOIN
                                                            slotting.npfcpcsettings ON CPCWHSE = casepicks_whse
                                                                AND CPCITEM = casepicks_item
                                                                LEFT JOIN
                                                            slotting.caseprimaries ON caseprim_whse = casepicks_whse
                                                                AND caseprim_item = casepicks_item
                                                                LEFT JOIN
                                                            slotting.pkgu_percent ON PERC_WHSE = casepicks_whse
                                                                AND PERC_ITEM = casepicks_item
                                                                AND PERC_PKGU = casepicks_pkgu
                                                                JOIN
                                                            slotting.mysql_nptsld ON casepicks_whse = WAREHOUSE
                                                                AND ITEM_NUMBER = casepicks_item
                                                                AND PACKAGE_UNIT = casepicks_pkgu
                                                        WHERE
                                                            (FLOOR IS NULL OR FLOOR = 'N')
                                                                AND caseprim_item IS NULL
                                                                AND CPCEWID * CPCCLEN * CPCCHEI > 0
                                                                AND PERC_PERC IS NOT NULL
                                                                and AVG_INV_OH > 0
                                                        GROUP BY casepicks_whse , casepicks_item , casepicks_pkgu , CPCEWID , CPCCLEN , CPCCHEI
                                                        HAVING (tot_mnth / tot_year) > .05");
    $caseopp->execute();
    $caseopparray = $caseopp->fetchAll(pdo::FETCH_ASSOC);



    $maxrange = 20000;
    $counter = 0;
    $rowcount = count($caseopparray);

    $columns = 'casehighopp_whse, casehighopp_item, casehighopp_pkgu, casehighopp_decktf, casehighopp_deckmax, casehighopp_deckmin, casehighopp_deckmoves, casehighopp_deckpickhours, casehighopp_deckreplenhours, casehighopp_decktotalhours, casehighopp_pallettf, casehighopp_palletmax, casehighopp_palletmin, casehighopp_palletmoves, casehighopp_palletpickhours, casehighopp_palletreplenhours, casehighopp_pallettotalhours, casehighopp_reservepickhours, casehighopp_reservetotalhours, casehighopp_finalrec';


    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
            $whse = $caseopparray[$counter]['casepicks_whse'];
            $item = $caseopparray[$counter]['casepicks_item'];
            $pkgu = $caseopparray[$counter]['casepicks_pkgu'];
            $wid = ($caseopparray[$counter]['CPCCWID'] * 0.393701);
            $hei = ($caseopparray[$counter]['CPCCHEI'] * 0.393701);
            $len = ($caseopparray[$counter]['CPCCLEN'] * 0.393701);
            $tot_mnth = $caseopparray[$counter]['tot_mnth'];
            $tot_year = $caseopparray[$counter]['tot_year'];
            $percent = $caseopparray[$counter]['PERC_PERC'];
            $avginv = $caseopparray[$counter]['AVG_INV_OH'];
            $AVGD_BTW_SLE = $caseopparray[$counter]['AVGD_BTW_SLE'];
            $SHIP_QTY_MN = $caseopparray[$counter]['SHIP_QTY_MN'];

            $dailypickyear = $tot_year / 253;
            $dailypickmnth = $tot_mnth / 23;

            if ($dailypickmnth > $dailypickyear) {
                $pickavg = $dailypickmnth;
            } else {
                $pickavg = $dailypickyear;
            }

            $pickunit = $pickavg * $pkgu;
            $theoreticalmax = intval($avginv * $percent);  //limit to average invenotry times pkgu percent

            $decktfarray = _truefitgrid2iterations_case('24D28', 24, 44, 24, ' ', $hei, $len, $wid, $pkgu);
            $decktf = $decktfarray[1];
            if ($decktf > $theoreticalmax) { //limit to theoritical max
                $deckmax = $theoreticalmax;
            } else {
                $deckmax = $decktf;
            }
            $deckmin = intval(_minloccase($deckmax, $SHIP_QTY_MN, $pkgu));
            $deckmoves = intval(_implied_daily_moves($deckmax, $deckmin, $pickunit, $avginv, $SHIP_QTY_MN, $AVGD_BTW_SLE) * 253);

            $pallettfarray = _truefitgrid2iterations_case('48D48', 48, 48, 48, ' ', $hei, $len, $wid, $pkgu);
            $pallettf = $pallettfarray[1];
            if ($pallettf > $theoreticalmax) {  //limit to theoritical max
                $palletmax = $theoreticalmax;
            } else {
                $palletmax = $pallettf;
            }

            if ($pallettf == 0) {
                $counter += 1;
                continue;
            }

            $palletmin = intval(_minloccase($palletmax, $SHIP_QTY_MN, $pkgu));
            $palletmoves = intval(_implied_daily_moves($palletmax, $palletmin, $pickunit, $avginv, $SHIP_QTY_MN, $AVGD_BTW_SLE) * 253);

            $deckpickhours = ($pickavg * 253) / $deckpickLPH;
            $pallpickhours = ($pickavg * 253) / $palletpickLPH;
            $reservepickhours = ($pickavg * 253) / $reservepickLPH;
            $deckmoveshours = $deckmoves / $deckreplen;
            $palletmoveshours = $palletmoves / $palletreplen;

            $decktotalhours = $deckpickhours + $deckmoveshours;
            $reservetotalhours = $reservepickhours;
            if ($palletmax / $pallettf < $palletminpercent) {
                $pallettotalhours = 99999999;
            } else {
                $pallettotalhours = $pallpickhours + $palletmoveshours;
            }

            $minzone = min($pallettotalhours, $decktotalhours, $reservetotalhours);

            if ($minzone == $pallettotalhours) {
                $rec = 'PALLET';
            } elseif ($minzone == $decktotalhours) {
                $rec = 'DECK';
            } else {
                $rec = 'PFR';
            }

//need to add data and see if inserts


            $data[] = "($whse, $item, $pkgu, $decktf, $deckmax, $deckmin, $deckmoves,  '$deckpickhours', '$deckmoveshours', '$decktotalhours', $pallettf, $palletmax,$palletmin, $palletmoves, '$pallpickhours', '$palletmoveshours',  '$pallettotalhours', '$reservepickhours', '$reservetotalhours' , '$rec' )";
            $counter += 1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.casehighopp ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange += 20000;
    } while ($counter <= $rowcount); //end of item by whse loop
}