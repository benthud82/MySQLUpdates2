<!--Code to update the MySQL table "caseopps"-->

<?php
//Load data from A-System to an array
//include_once("../globalfunctions/slottingfunctions.php");
include_once("globalfunctions.php");
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
ini_set('max_allowed_packet', 999999999);
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 

$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array());
$conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqldelete = "TRUNCATE TABLE caseoppsbycust_merge";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();




$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUD01";
$pdo_password = "tucker1234";
$aseriesconn = new PDO($pdo_dsn, $pdo_username, $pdo_password, array());

#Dallas
//$result = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, temp.CSEPKGU, sum(temp.OPP) as TOTOPP FROM A.HSIPCORDTA.NPFMVC INNER JOIN (SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 7 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU) temp on PDWHSE||PDITEM||PDPKGU = VCWHSE||VCITEM||VCPKGU WHERE VCITEM not in (select LMITEM from A.HSIPCORDTA.NPFLSM where LMWHSE = 7 and LMTIER like 'C%' or LMTIER like 'L17' or LMTIER like 'L18' or LMTIER like 'L03' or LMTIER like 'L15') and VCITEM not in (SELECT PCITEM FROM A.HSIPCORDTA.NPFCPC INNER JOIN (SELECT PCITEM as CORITEM, PCPFRC as CORP FROM  A.HSIPCORDTA.NPFCPC WHERE PCWHSE = 0 and PCPFRC = 'P') temp2 on CORITEM = PCITEM WHERE PCWHSE = 7 and PCPFRA = 'Y') and VCWHSE = 7 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')GROUP BY VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, temp.CSEPKGU");
//$dallasmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 7 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
//$dallasmvcresult->execute();
//$dallasmvcresultarray = $dallasmvcresult->fetchAll(PDO::FETCH_NUM);

$dallasoppresult = $aseriesconn->prepare("SELECT 
                                            PDWHSE,
                                            PDITEM,
                                            PDLOC#,
                                            PCCPKU,
                                            PDWCS#,
                                            PBRCJD,
                                            PBRCHM,
                                            PBPTJD,
                                            PBPTHM,
                                            PBRLJD,
                                            PBRLHM,
                                            PBSHAN,
                                            PBAN8,
                                            sum(PDPCKQ) as PICKQTY
                                        FROM
                                            A.HSIPCORDTA.NOTWPT,
                                            A.HSIPCORDTA.NPFCPC,
                                            A.HSIPCORDTA.NOTWPS
                                        WHERE
                                            PDWHSE = PBWHSE and PDWCS# = PBWCS#
                                                and PDWKNO = PBWKNO
                                                and PDBOX# = PBBOX#
                                                and PCWHSE = 0
                                                and PDITEM = PCITEM
                                                and PDBXSZ <> 'CSE'
                                                and PCCPKU > 0
                                                and PDPKGU = 1
                                                and PDITEM between '100000' and '999999'
                                                and PDPCKQ > 1
                                                and PCCPKU > 1
                                        group by PDWHSE , PDWCS# , PBRCJD , PBRCHM , PBPTJD , PBPTHM , PBRLJD , PBRLHM , PBSHAN , PBAN8 , PDITEM , PDLOC# , PDPKGU , PCCPKU
                                        HAVING sum(PDPCKQ) >= PCCPKU");
$dallasoppresult->execute();
$dallasoppresultarray = $dallasoppresult->fetchAll(PDO::FETCH_ASSOC);


$columns = 'VCWHSE, VCITEM, VCLOC, CASEPKGU, WCSNUM, RECDATE, PRINTDATE, RELDATE, SHIPTO, BILLTO, SHIPQTY';

$maxrange = 999;
$counter = 0;
$rowcount = count($dallasoppresultarray);

do {
    if ($maxrange > $rowcount) {  //prevent undefined offset
        $maxrange = $rowcount - 1;
    }

    $data = array();
    $values = array();
    while ($counter <= $maxrange) { //split into 10,000 lines segments to insert into merge table
        $PDWHSE = intval($dallasoppresultarray[$counter]['PDWHSE']);
        $PDITEM = $dallasoppresultarray[$counter]['PDITEM'];
        $PDLOC = $dallasoppresultarray[$counter]['PDLOC#'];
        $PCCPKU = intval($dallasoppresultarray[$counter]['PCCPKU']);
        $PDWCS = intval($dallasoppresultarray[$counter]['PDWCS#']);

        $PBRCJD = $dallasoppresultarray[$counter]['PBRCJD'];
        $PBRCHM = $dallasoppresultarray[$counter]['PBRCHM'];
        $RECDATE = date("Y-m-d", strtotime(_jdatetomysqldate($PBRCJD)));
        $RECTIME = _stringtimenoseconds($PBRCHM);
        $RECDATETIME = $RECDATE . ' ' . $RECTIME;

        $PBPTJD = $dallasoppresultarray[$counter]['PBPTJD'];
        $PBPTHM = $dallasoppresultarray[$counter]['PBPTHM'];
        $PRINTDATE = date("Y-m-d", strtotime(_jdatetomysqldate($PBPTJD)));
        $PRINTTIME = _stringtimenoseconds($PBPTHM);
        $PRINTDATETIME = $PRINTDATE . ' ' . $PRINTTIME;

        $PBRLJD = $dallasoppresultarray[$counter]['PBRLJD'];
        $PBRLHM = $dallasoppresultarray[$counter]['PBRLHM'];
        $RELDATE = date("Y-m-d", strtotime(_jdatetomysqldate($PBRLJD)));
        $RELTIME = _stringtimenoseconds($PBRLHM);
        $RELDATETIME = $RELDATE . ' ' . $RELTIME;

        $PBSHAN = intval($dallasoppresultarray[$counter]['PBSHAN']);
        $PBAN8 = intval($dallasoppresultarray[$counter]['PBAN8']);
        $PICKQTY = intval($dallasoppresultarray[$counter]['PICKQTY']);



        $data[] = "($PDWHSE, '$PDITEM', '$PDLOC', $PCCPKU, $PDWCS, '$RECDATETIME', '$PRINTDATETIME', '$RELDATETIME', $PBSHAN, $PBAN8, $PICKQTY)";
        $counter +=1;
    }


    $values = implode(',', $data);

    if (empty($values)) {
        break;
    }
    $sql = "INSERT INTO caseoppsbycust_merge ($columns) VALUES $values";
    $query = $conn1->prepare($sql);
    $query->execute();
    $maxrange +=1000;
} while ($counter <= $rowcount);

$sqlmerge = "INSERT IGNORE INTO caseoppsbycust
                                    (VCWHSE, 
                                     VCITEM, 
                                     VCLOC, 
                                     CASEPKGU, 
                                     WCSNUM, 
                                     RECDATE,
                                     PRINTDATE, 
                                     RELDATE,
                                     SHIPTO,
                                     BILLTO,
                                     SHIPQTY) 
                            SELECT 
                                *
                            from
                                caseoppsbycust_merge";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();
