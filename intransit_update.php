<?php

//code to update PODATE table

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
include '../globalincludes/nahsi_mysql.php';  //conn1
include '../globalincludes/usa_asys.php';  //$aseriesconn
include 'globalfunctions.php';

$sqldelete = "TRUNCATE TABLE slotting.intransit_merge";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();


//pull in holiday dates to array
$sqlholiday = $conn1->prepare("SELECT * from holidays");
$sqlholiday->execute();
$sqlholidayarray = $sqlholiday->fetchAll(pdo::FETCH_COLUMN);

$sql1 = $aseriesconn->prepare("SELECT DISTINCT PBWHSE,PBWCS#,PBWKNO,PBBOX#,PBBXSZ,PBRCJD,PBRCHM,PBRLJD,PBRLHM,PBSHPZ,PBZIP3,PDAN8,PDSHAN,XDDOCO,XDDDAT,XDDTIM,XDTRC#,XDLP9D,XDCRNM,XDASN FROM HSIPCORDTA.NOTDTL WHERE PBRLJD >= 15000 and PBRLJD <= 15024 and PBWHSE in (2,3,6,7,9)");
$sql1->execute();
$sql1array = $sql1->fetchAll(pdo::FETCH_ASSOC);

$columns = 'INTRAN_WHSE, INTRAN_WCSNUM, INTRAN_WONUM, INTRAN_BOXNUM, INTRAN_BOXSIZE, INTRAN_RECDATETIME, INTRAN_RELDATETIME, INTRAN_SHIPZONE, INTRAN_ZIP3, INTRAN_BILLTO, INTRAN_SHIPTO, INTRAN_DOCNUM, INTRAN_DELDATETIME, INTRAN_TRACER, INTRAN_LPNUM, INTRAN_CARR, INTRAN_PLANNUM, INTRAN_DAYSINTRAN';

$values = [];
$intranid = 0;
$maxrange = 999;
$counter = 0;
$rowcount = count($sql1array);

do {
    if ($maxrange > $rowcount) {  //prevent undefined offset
        $maxrange = $rowcount - 1;
    }

    $data = [];
    $values = [];
    while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table
        $INTRAN_WHSE = intval($sql1array[$counter]['PBWHSE']);
        $INTRAN_WCSNUM = intval($sql1array[$counter]['PBWCS#']);
        $INTRAN_WONUM = intval($sql1array[$counter]['PBWKNO']);
        $INTRAN_BOXNUM = intval($sql1array[$counter]['PBBOX#']);
        $INTRAN_BOXSIZE = $sql1array[$counter]['PBBXSZ'];
        $recjdate = _jdatetomysqldate($sql1array[$counter]['PBRCJD']);
        $rechrmn = _stringtimenoseconds($sql1array[$counter]['PBRCHM']);
        $INTRAN_RECDATETIME = date("Y-m-d H:i:s", strtotime($recjdate . ' ' . $rechrmn));
        $reljdate = _jdatetomysqldate($sql1array[$counter]['PBRLJD']);
        $relhrmn = _stringtimenoseconds($sql1array[$counter]['PBRLHM']);
        $INTRAN_RELDATETIME = date("Y-m-d H:i:s", strtotime($reljdate . ' ' . $relhrmn));
        $INTRAN_SHIPZONE = $sql1array[$counter]['PBSHPZ'];
        $INTRAN_ZIP3 = intval($sql1array[$counter]['PBZIP3']);
        $INTRAN_BILLTO = intval($sql1array[$counter]['PDAN8']);
        $INTRAN_SHIPTO = intval($sql1array[$counter]['PDSHAN']);
        $INTRAN_DOCNUM = intval($sql1array[$counter]['XDDOCO']);
//        $deldate = _jdatetomysqldate($sql1array[$counter]['XDDDAT']);
        $deldate = date("Y-m-d", strtotime($sql1array[$counter]['XDDDAT']));

        $deltime = _stringtimenoseconds($sql1array[$counter]['XDDTIM']);
        $INTRAN_DELDATETIME = date("Y-m-d H:i:s", strtotime($deldate . ' ' . $deltime));
        $INTRAN_TRACER = $sql1array[$counter]['XDTRC#'];
        $INTRAN_LPNUM = intval($sql1array[$counter]['XDLP9D']);
        $INTRAN_CARR = $sql1array[$counter]['XDCRNM'];
        $INTRAN_PLANNUM = $sql1array[$counter]['XDASN'];
        $INTRAN_DAYSINTRAN = getWorkingDays($INTRAN_RELDATETIME, $INTRAN_DELDATETIME, $sqlholidayarray);
        
        $data[] = "($INTRAN_WHSE, $INTRAN_WCSNUM, $INTRAN_WONUM, $INTRAN_BOXNUM, '$INTRAN_BOXSIZE', '$INTRAN_RECDATETIME', '$INTRAN_RELDATETIME', '$INTRAN_SHIPZONE', $INTRAN_ZIP3, $INTRAN_BILLTO, $INTRAN_SHIPTO, $INTRAN_DOCNUM, '$INTRAN_DELDATETIME', '$INTRAN_TRACER', $INTRAN_LPNUM, '$INTRAN_CARR', '$INTRAN_PLANNUM', '$INTRAN_DAYSINTRAN')";
        unset($sql1array[$counter]);
        $counter +=1;
    }
    $values = implode(',', $data);

    if (empty($values)) {
        break;
    }
    $sql = "INSERT IGNORE INTO slotting.intransit_merge ($columns) VALUES $values";
    $query = $conn1->prepare($sql);
    $query->execute();
    $maxrange +=1000;
} while ($counter <= $rowcount);

$sql1array = array();

$sqlmerge = "INSERT INTO intransit(INTRAN_WHSE, INTRAN_WCSNUM, INTRAN_WONUM, INTRAN_BOXNUM, INTRAN_BOXSIZE, INTRAN_RECDATETIME, INTRAN_RELDATETIME, INTRAN_SHIPZONE, INTRAN_ZIP3, INTRAN_BILLTO, INTRAN_SHIPTO, INTRAN_DOCNUM, INTRAN_DELDATETIME, INTRAN_TRACER, INTRAN_LPNUM, INTRAN_CARR, INTRAN_PLANNUM, INTRAN_DAYSINTRAN)
SELECT intransit_merge.INTRAN_WHSE, intransit_merge.INTRAN_WCSNUM, intransit_merge.INTRAN_WONUM, intransit_merge.INTRAN_BOXNUM, intransit_merge.INTRAN_BOXSIZE, intransit_merge.INTRAN_RECDATETIME, intransit_merge.INTRAN_RELDATETIME, intransit_merge.INTRAN_SHIPZONE, intransit_merge.INTRAN_ZIP3, intransit_merge.INTRAN_BILLTO, intransit_merge.INTRAN_SHIPTO, intransit_merge.INTRAN_DOCNUM, intransit_merge.INTRAN_DELDATETIME, intransit_merge.INTRAN_TRACER, intransit_merge.INTRAN_LPNUM, intransit_merge.INTRAN_CARR, intransit_merge.INTRAN_PLANNUM, intransit_merge.INTRAN_DAYSINTRAN FROM intransit_merge
ON DUPLICATE KEY UPDATE intransit.INTRAN_RECDATETIME = intransit_merge.INTRAN_RECDATETIME, intransit.INTRAN_RELDATETIME = intransit_merge.INTRAN_RELDATETIME, intransit.INTRAN_DELDATETIME = intransit_merge.INTRAN_DELDATETIME, intransit.INTRAN_CARR = intransit_merge.INTRAN_CARR, intransit.INTRAN_DAYSINTRAN = intransit_merge.INTRAN_DAYSINTRAN";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();