<?php

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
include '../CustomerAudit/connection/connection_details.php';
include_once '../globalincludes/usa_asys.php';

$columns = 'pickpred_whse, pickpred_date, pickpred_tier, pickpred_picks, pickpred_units, pickpred_volume';

$tierresult = $aseriesconn->prepare("SELECT LMWHSE,
                                                                                  case when LMTIER = ' ' then 'RES' else LMTIER end as LMTIER, 
                                                                                  CASE WHEN (PDSHPD<99999) THEN 
                                                                                            (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2)))
                                                                                            WHEN PDSHPD>99999 THEN 
                                                                                            (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2)))
                                                                                  END as PDSHPD, 
                                                                                  count(*) as LINES, 
                                                                                  sum(PDPCKS) as UNITS, 
                                                                                  sum(PDLENG * PDWIDT * PDHEIG * PDPCKS) as VOLUME 
                                                                FROM HSIPCORDTA.NOTWPT, HSIPCORDTA.NPFLSM 
                                                                WHERE PDWHSE = lmwhse and pdloc# = lmloc# and LMWHSE in (2,3,6,7,9) 
                                                                GROUP BY LMWHSE, case when LMTIER = ' ' then 'RES' else LMTIER end, CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END");
$tierresult->execute();
$tierarray = $tierresult->fetchAll(pdo::FETCH_ASSOC);


$maxrange = 9999;
$counter = 0;
$rowcount = count($tierarray);

do {
    if ($maxrange > $rowcount) {  //prevent undefined offset
        $maxrange = $rowcount - 1;
    }

    $data = array();
    $values = array();
    while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table //sub loop through items by whse to pull in CPC settings by whse/item
        $pickpred_whse = $tierarray[$counter]['LMWHSE'];
        $pickpred_date = $tierarray[$counter]['PDSHPD'];

        $pickpred_tier = $tierarray[$counter]['LMTIER'];
        $pickpred_picks = intval($tierarray[$counter]['LINES']);
        $pickpred_units = intval($tierarray[$counter]['UNITS']);
        $pickpred_volume = intval($tierarray[$counter]['VOLUME']);

        $data[] = "($pickpred_whse,'$pickpred_date', '$pickpred_tier', $pickpred_picks,$pickpred_units, $pickpred_volume )";
        $counter += 1;
    }


    $values = implode(',', $data);

    if (empty($values)) {
        break;
    }
    $sql = "INSERT INTO slotting.pickpred ($columns) VALUES $values ON DUPLICATE KEY UPDATE pickpred_picks=VALUES(pickpred_picks), pickpred_units=VALUES(pickpred_units), pickpred_volume=VALUES(pickpred_volume)";
    $query = $conn1->prepare($sql);
    $query->execute();
    $maxrange += 10000;
} while ($counter <= $rowcount); //end of item by whse loop

