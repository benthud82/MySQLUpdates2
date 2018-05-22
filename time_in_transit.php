
<?php

date_default_timezone_set('America/New_York');
set_time_limit(99999);
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
ini_set('max_allowed_packet', 999999999);
include '../globalincludes/usa_asys.php';
include '../globalfunctions/custdbfunctions.php';
//include '../globalincludes/nahsi_mysql.php';  //production connection
include '../globalincludes/ustxgpslotting_mysql.php';  //modelling connection

$startdate = _roll10dayyyyymmdd();


$dates = $aseriesconn->prepare("SELECT DISTINCT XHDDAT FROM A.HSIPCORDTA.NOTHDR WHERE  XHDDAT  >= $startdate");
$dates->execute();
$datesarray = $dates->fetchAll(pdo::FETCH_COLUMN);

$columns = 'WHSE, WCSNUM, WONUM, BOXNUM, SHIPZONE, SHIPCLASS, TRACER, BOXSIZE, HAZCLASS, BOXLINES, BOXWEIGHT, ZIPCODE, BOXVALUE, DELIVERDATE, DELIVERTIME, LICENSE, CARRIER, SHIPDATE, SHIPTIME, BILLTO, SHIPTO';

$sqldelete = "TRUNCATE TABLE delivery_dates_merge";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

foreach ($datesarray as $value) {


    $result1 = $aseriesconn->prepare("SELECT
                                                        PBWHSE as WHSE,
                                                        PBWCS# as WCSNUM,
                                                        PBWKNO as WONUM,
                                                        PBBOX# as BOXNUM,
                                                        PBSHPZ as SHIPZONE,
                                                        PBSHPC as SHIPCLASS,
                                                        PBTRC# as TRACER,
                                                        PBBXSZ as BOXSIZE,
                                                        CPHAZT as HAZCLASS,
                                                        PBBOXL as BOXLINES,
                                                        PBBXAW as BOXWEIGHT,
                                                        GCZIP5 as ZIPCODE,
                                                        PBBXVS as BOXVALUE,
                                                        substring(XHDDAT,1,4) || '-' ||   substring(XHDDAT,5,2) || '-' ||   substring(XHDDAT,7,2) as DELIVERDATE,
                                                        case when  XHDTIM <= 999 then substring(XHDTIM,1,1) || ':' || substring(XHDTIM,2,2)  else  substring(XHDTIM,1,2) || ':' || substring(XHDTIM,3,2) end as DELIVERTIME ,
                                                        XHLP9D as LICENSE,
                                                        XHCRNM as CARRIER,
                                                        substring(XHSDAT,1,4) || '-' ||   substring(XHSDAT,5,2) || '-' ||   substring(XHSDAT,7,2) as SHIPDATE,
                                                        case when XHSTIM <= 99999 then    substring(XHSTIM,1,1) || ':' || substring(XHSTIM,2,2) else  substring(XHSTIM,1,2) || ':' || substring(XHSTIM,3,2) end  as SHIPTIME,
                                                        XHAN8 AS BILLTO,
                                                        XHSHAN AS SHIPTO
                                                      FROM A.HSIPCORDTA.NOTHDR
                                                      WHERE PBTRC# like '1Z%' and XHDDAT  = $value");
    $result1->execute();
    $masterdisplayarray = $result1->fetchAll(pdo::FETCH_ASSOC);

    $maxrange = 9999;
    $counter = 0;
    $rowcount = count($masterdisplayarray);


    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }
        $data = array();
        $values = array();

        while ($counter <= $maxrange) {
            $WHSE = intval($masterdisplayarray[$counter]['WHSE']);
            $WCSNUM = intval($masterdisplayarray[$counter]['WCSNUM']);
            $WONUM = intval($masterdisplayarray[$counter]['WONUM']);
            $BOXNUM = intval($masterdisplayarray[$counter]['BOXNUM']);
            $SHIPZONE = trim($masterdisplayarray[$counter]['SHIPZONE']);
            $SHIPCLASS = trim($masterdisplayarray[$counter]['SHIPCLASS']);
            $TRACER = trim($masterdisplayarray[$counter]['TRACER']);
            $BOXSIZE = trim($masterdisplayarray[$counter]['BOXSIZE']);
            $HAZCLASS = trim($masterdisplayarray[$counter]['HAZCLASS']);
            $BOXLINES = intval($masterdisplayarray[$counter]['BOXLINES']);
            $BOXWEIGHT = $masterdisplayarray[$counter]['BOXWEIGHT'];
            $ZIPCODE = intval($masterdisplayarray[$counter]['ZIPCODE']);
            $BOXVALUE = $masterdisplayarray[$counter]['BOXVALUE'];
            $DELIVERDATE = trim($masterdisplayarray[$counter]['DELIVERDATE']);
            $DELIVERTIME = trim($masterdisplayarray[$counter]['DELIVERTIME']);
            $LICENSE = intval($masterdisplayarray[$counter]['LICENSE']);
            $CARRIER = trim(preg_replace('/[^ \w]+/', '', $masterdisplayarray[$counter]['CARRIER']));
            $SHIPDATE = trim($masterdisplayarray[$counter]['SHIPDATE']);
            $SHIPTIME = trim($masterdisplayarray[$counter]['SHIPTIME']);
            $BILLTO = intval($masterdisplayarray[$counter]['BILLTO']);
            $SHIPTO = intval($masterdisplayarray[$counter]['SHIPTO']);

            $data[] = "($WHSE, $WCSNUM, $WONUM, $BOXNUM, '$SHIPZONE', '$SHIPCLASS', '$TRACER', '$BOXSIZE', '$HAZCLASS', $BOXLINES, '$BOXWEIGHT', $ZIPCODE, '$BOXVALUE', '$DELIVERDATE', '$DELIVERTIME', $LICENSE, '$CARRIER', '$SHIPDATE', '$SHIPTIME', $BILLTO, $SHIPTO)";
            $counter += 1;
        }

        $values = implode(',', $data);
        if (empty($values)) {
            break;
        }

        $sql = "INSERT IGNORE INTO slotting.delivery_dates_merge ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange += 10000;
    } while ($counter <= $rowcount);
}

//once merge table has been populated, insert on duplicate key update here.
$sqlmerge2 = "INSERT INTO slotting.delivery_dates (WHSE, WCSNUM, WONUM, BOXNUM, SHIPZONE, SHIPCLASS, TRACER, BOXSIZE, HAZCLASS, BOXLINES, BOXWEIGHT, ZIPCODE, BOXVALUE, DELIVERDATE, DELIVERTIME, LICENSE, CARRIER, SHIPDATE, SHIPTIME, BILLTO, SHIPTO)
SELECT A.WHSE, A.WCSNUM, A.WONUM, A.BOXNUM, A.SHIPZONE, A.SHIPCLASS, A.TRACER, A.BOXSIZE, A.HAZCLASS, A.BOXLINES, A.BOXWEIGHT, A.ZIPCODE, A.BOXVALUE, A.DELIVERDATE, A.DELIVERTIME, A.LICENSE, A.CARRIER, A.SHIPDATE, A.SHIPTIME, A.BILLTO, A.SHIPTO FROM slotting.delivery_dates_merge A
ON DUPLICATE KEY UPDATE  DELIVERDATE = A.DELIVERDATE,  DELIVERTIME = A.DELIVERTIME;  ";
$querymerge2 = $conn1->prepare($sqlmerge2);
$querymerge2->execute();

