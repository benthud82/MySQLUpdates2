<!--Code to update the MySQL table "caseopps"-->
<?php
//Load data from A-System to an array
include_once("../globalfunctions/slottingfunctions.php");
set_time_limit(99999);
ini_set('memory_limit', '-1');
$dbtype = "mysql";
$dbhost = "nahsifljaws01"; // Host name 
$dbuser = "slotadmin"; // Mysql username 
$dbpass = "slotadmin"; // Mysql password 
$dbname = "slotting"; // Database name 

$conn1 = new PDO("{$dbtype}:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass, array());
$conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqldelete = "TRUNCATE TABLE caseopps";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();




$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUD01";
$pdo_password = "tucker1234";
$aseriesconn = new PDO($pdo_dsn, $pdo_username, $pdo_password, array());

#Dallas
//$result = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, temp.CSEPKGU, sum(temp.OPP) as TOTOPP FROM A.HSIPCORDTA.NPFMVC INNER JOIN (SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 7 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU) temp on PDWHSE||PDITEM||PDPKGU = VCWHSE||VCITEM||VCPKGU WHERE VCITEM not in (select LMITEM from A.HSIPCORDTA.NPFLSM where LMWHSE = 7 and LMTIER like 'C%' or LMTIER like 'L17' or LMTIER like 'L18' or LMTIER like 'L03' or LMTIER like 'L15') and VCITEM not in (SELECT PCITEM FROM A.HSIPCORDTA.NPFCPC INNER JOIN (SELECT PCITEM as CORITEM, PCPFRC as CORP FROM  A.HSIPCORDTA.NPFCPC WHERE PCWHSE = 0 and PCPFRC = 'P') temp2 on CORITEM = PCITEM WHERE PCWHSE = 7 and PCPFRA = 'Y') and VCWHSE = 7 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')GROUP BY VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, temp.CSEPKGU");
$dallasmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 7 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$dallasmvcresult->execute();
$dallasmvcresultarray = $dallasmvcresult->fetchAll(PDO::FETCH_NUM);

$dallasoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 7 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$dallasoppresult->execute();
$dallasoppresultarray = $dallasoppresult->fetchAll(PDO::FETCH_ASSOC);

$dallasexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.HSIPCORDTA.NPFLSM where LMWHSE = 7 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$dallasexcl1->execute();
$dallasexcl1resultarray = $dallasexcl1->fetchAll(PDO::FETCH_ASSOC);

$dallasexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$dallasexcl2->execute();
$dallasexcl2resultarray = $dallasexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (7) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($dallasexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($dallasexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($dallasexcl2resultarray[$key]);
        continue;
    }
}

foreach ($dallasmvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($dallasmvcresultarray[$key][1], $dallasexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($dallasmvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($dallasmvcresultarray[$key][1], $dallasexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($dallasmvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($dallasmvcresultarray[$key][5], $dallasoppresultarray);

    if (!empty($keyvalindex)) {
        $dallasmvcresultarray[$key][6] = $dallasoppresultarray[$keyvalindex]['CSEPKGU'];
        $dallasmvcresultarray[$key][7] = $dallasoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($dallasmvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $dallasmvcresultarray[$key][0];
    $VCITEM = $dallasmvcresultarray[$key][1];
    $VCLOC = $dallasmvcresultarray[$key][2];
    $VCPKGU = $dallasmvcresultarray[$key][3];
    $VCGRD5 = $dallasmvcresultarray[$key][4];
    $CASEPKGU = $dallasmvcresultarray[$key][6];
    $TOTOPP = $dallasmvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$dallasmvcresultarray = NULL;
$dallasoppresultarray = NULL;
$dallasexcl1resultarray = NULL;
$dallasexcl2resultarray = NULL;

//INDY

$indymvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 2 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$indymvcresult->execute();
$indymvcresultarray = $indymvcresult->fetchAll(PDO::FETCH_NUM);

$indyoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 2 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$indyoppresult->execute();
$indyoppresultarray = $indyoppresult->fetchAll(PDO::FETCH_ASSOC);

$indyexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.HSIPCORDTA.NPFLSM where LMWHSE = 2 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$indyexcl1->execute();
$indyexcl1resultarray = $indyexcl1->fetchAll(PDO::FETCH_ASSOC);

$indyexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$indyexcl2->execute();
$indyexcl2resultarray = $indyexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (2) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($indyexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($indyexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($indyexcl2resultarray[$key]);
        continue;
    }
}



foreach ($indymvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($indymvcresultarray[$key][1], $indyexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($indymvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($indymvcresultarray[$key][1], $indyexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($indymvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($indymvcresultarray[$key][5], $indyoppresultarray);

    if (!empty($keyvalindex)) {
        $indymvcresultarray[$key][6] = $indyoppresultarray[$keyvalindex]['CSEPKGU'];
        $indymvcresultarray[$key][7] = $indyoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($indymvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $indymvcresultarray[$key][0];
    $VCITEM = $indymvcresultarray[$key][1];
    $VCLOC = $indymvcresultarray[$key][2];
    $VCPKGU = $indymvcresultarray[$key][3];
    $VCGRD5 = $indymvcresultarray[$key][4];
    $CASEPKGU = $indymvcresultarray[$key][6];
    $TOTOPP = $indymvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$indymvcresultarray = NULL;
$indyoppresultarray = NULL;
$indyexcl1resultarray = NULL;
$indyexcl2resultarray = NULL;


//SPARKS

$sparksmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 3 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$sparksmvcresult->execute();
$sparksmvcresultarray = $sparksmvcresult->fetchAll(PDO::FETCH_NUM);

$sparksoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 3 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$sparksoppresult->execute();
$sparksoppresultarray = $sparksoppresult->fetchAll(PDO::FETCH_ASSOC);

$sparksexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.HSIPCORDTA.NPFLSM where LMWHSE = 3 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$sparksexcl1->execute();
$sparksexcl1resultarray = $sparksexcl1->fetchAll(PDO::FETCH_ASSOC);

$sparksexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$sparksexcl2->execute();
$sparksexcl2resultarray = $sparksexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (3) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($sparksexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($sparksexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($sparksexcl2resultarray[$key]);
        continue;
    }
}



foreach ($sparksmvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($sparksmvcresultarray[$key][1], $sparksexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($sparksmvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($sparksmvcresultarray[$key][1], $sparksexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($sparksmvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($sparksmvcresultarray[$key][5], $sparksoppresultarray);

    if (!empty($keyvalindex)) {
        $sparksmvcresultarray[$key][6] = $sparksoppresultarray[$keyvalindex]['CSEPKGU'];
        $sparksmvcresultarray[$key][7] = $sparksoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($sparksmvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $sparksmvcresultarray[$key][0];
    $VCITEM = $sparksmvcresultarray[$key][1];
    $VCLOC = $sparksmvcresultarray[$key][2];
    $VCPKGU = $sparksmvcresultarray[$key][3];
    $VCGRD5 = $sparksmvcresultarray[$key][4];
    $CASEPKGU = $sparksmvcresultarray[$key][6];
    $TOTOPP = $sparksmvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$sparksmvcresultarray = NULL;
$sparksoppresultarray = NULL;
$sparksexcl1resultarray = NULL;
$sparksexcl2resultarray = NULL;


//DENVER

$denvermvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 6 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$denvermvcresult->execute();
$denvermvcresultarray = $denvermvcresult->fetchAll(PDO::FETCH_NUM);

$denveroppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 6 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$denveroppresult->execute();
$denveroppresultarray = $denveroppresult->fetchAll(PDO::FETCH_ASSOC);

$denverexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.HSIPCORDTA.NPFLSM where LMWHSE = 6 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$denverexcl1->execute();
$denverexcl1resultarray = $denverexcl1->fetchAll(PDO::FETCH_ASSOC);

$denverexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$denverexcl2->execute();
$denverexcl2resultarray = $denverexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (6) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($denverexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($denverexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($denverexcl2resultarray[$key]);
        continue;
    }
}



foreach ($denvermvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($denvermvcresultarray[$key][1], $denverexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($denvermvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($denvermvcresultarray[$key][1], $denverexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($denvermvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($denvermvcresultarray[$key][5], $denveroppresultarray);

    if (!empty($keyvalindex)) {
        $denvermvcresultarray[$key][6] = $denveroppresultarray[$keyvalindex]['CSEPKGU'];
        $denvermvcresultarray[$key][7] = $denveroppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($denvermvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $denvermvcresultarray[$key][0];
    $VCITEM = $denvermvcresultarray[$key][1];
    $VCLOC = $denvermvcresultarray[$key][2];
    $VCPKGU = $denvermvcresultarray[$key][3];
    $VCGRD5 = $denvermvcresultarray[$key][4];
    $CASEPKGU = $denvermvcresultarray[$key][6];
    $TOTOPP = $denvermvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$denvermvcresultarray = NULL;
$denveroppresultarray = NULL;
$denverexcl1resultarray = NULL;
$denverexcl2resultarray = NULL;


//JAX

$jaxmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.HSIPCORDTA.NPFMVC WHERE VCWHSE = 9 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$jaxmvcresult->execute();
$jaxmvcresultarray = $jaxmvcresult->fetchAll(PDO::FETCH_NUM);

$jaxoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.HSIPCORDTA.NOTWPT, A.HSIPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 9 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$jaxoppresult->execute();
$jaxoppresultarray = $jaxoppresult->fetchAll(PDO::FETCH_ASSOC);

$jaxexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.HSIPCORDTA.NPFLSM where LMWHSE = 9 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$jaxexcl1->execute();
$jaxexcl1resultarray = $jaxexcl1->fetchAll(PDO::FETCH_ASSOC);

$jaxexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$jaxexcl2->execute();
$jaxexcl2resultarray = $jaxexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.HSIPCORDTA.NPFCPC WHERE PCWHSE in (9) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($jaxexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($jaxexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($jaxexcl2resultarray[$key]);
        continue;
    }
}



foreach ($jaxmvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($jaxmvcresultarray[$key][1], $jaxexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($jaxmvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($jaxmvcresultarray[$key][1], $jaxexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($jaxmvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($jaxmvcresultarray[$key][5], $jaxoppresultarray);

    if (!empty($keyvalindex)) {
        $jaxmvcresultarray[$key][6] = $jaxoppresultarray[$keyvalindex]['CSEPKGU'];
        $jaxmvcresultarray[$key][7] = $jaxoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($jaxmvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $jaxmvcresultarray[$key][0];
    $VCITEM = $jaxmvcresultarray[$key][1];
    $VCLOC = $jaxmvcresultarray[$key][2];
    $VCPKGU = $jaxmvcresultarray[$key][3];
    $VCGRD5 = $jaxmvcresultarray[$key][4];
    $CASEPKGU = $jaxmvcresultarray[$key][6];
    $TOTOPP = $jaxmvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$jaxmvcresultarray = NULL;
$jaxoppresultarray = NULL;
$jaxexcl1resultarray = NULL;
$jaxexcl2resultarray = NULL;
$aseriesconn = null; //close aseries


//open connection for Canada
$pdo_dsn = "odbc:DRIVER={iSeries Access ODBC DRIVER};SYSTEM=A";
$pdo_username = "BHUDS1";
$pdo_password = "tucker1234";
$aseriesconn = new PDO($pdo_dsn, $pdo_username, $pdo_password, array());


//Calgary

$calgarymvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.ARCPCORDTA.NPFMVC WHERE VCWHSE = 16 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$calgarymvcresult->execute();
$calgarymvcresultarray = $calgarymvcresult->fetchAll(PDO::FETCH_NUM);

$calgaryoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.ARCPCORDTA.NOTWPT, A.ARCPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 16 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$calgaryoppresult->execute();
$calgaryoppresultarray = $calgaryoppresult->fetchAll(PDO::FETCH_ASSOC);

$calgaryexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.ARCPCORDTA.NPFLSM where LMWHSE = 16 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$calgaryexcl1->execute();
$calgaryexcl1resultarray = $calgaryexcl1->fetchAll(PDO::FETCH_ASSOC);

$calgaryexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$calgaryexcl2->execute();
$calgaryexcl2resultarray = $calgaryexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (16) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($calgaryexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($calgaryexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($calgaryexcl2resultarray[$key]);
        continue;
    }
}



foreach ($calgarymvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($calgarymvcresultarray[$key][1], $calgaryexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($calgarymvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($calgarymvcresultarray[$key][1], $calgaryexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($calgarymvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($calgarymvcresultarray[$key][5], $calgaryoppresultarray);

    if (!empty($keyvalindex)) {
        $calgarymvcresultarray[$key][6] = $calgaryoppresultarray[$keyvalindex]['CSEPKGU'];
        $calgarymvcresultarray[$key][7] = $calgaryoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($calgarymvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $calgarymvcresultarray[$key][0];
    $VCITEM = $calgarymvcresultarray[$key][1];
    $VCLOC = $calgarymvcresultarray[$key][2];
    $VCPKGU = $calgarymvcresultarray[$key][3];
    $VCGRD5 = $calgarymvcresultarray[$key][4];
    $CASEPKGU = $calgarymvcresultarray[$key][6];
    $TOTOPP = $calgarymvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$calgarymvcresultarray = NULL;
$calgaryoppresultarray = NULL;
$calgaryexcl1resultarray = NULL;
$calgaryexcl2resultarray = NULL;





//NOTL

$notlmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.ARCPCORDTA.NPFMVC WHERE VCWHSE = 11 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$notlmvcresult->execute();
$notlmvcresultarray = $notlmvcresult->fetchAll(PDO::FETCH_NUM);

$notloppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.ARCPCORDTA.NOTWPT, A.ARCPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 11 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$notloppresult->execute();
$notloppresultarray = $notloppresult->fetchAll(PDO::FETCH_ASSOC);

$notlexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.ARCPCORDTA.NPFLSM where LMWHSE = 11 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$notlexcl1->execute();
$notlexcl1resultarray = $notlexcl1->fetchAll(PDO::FETCH_ASSOC);

$notlexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$notlexcl2->execute();
$notlexcl2resultarray = $notlexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (11) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($notlexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($notlexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($notlexcl2resultarray[$key]);
        continue;
    }
}



foreach ($notlmvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($notlmvcresultarray[$key][1], $notlexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($notlmvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($notlmvcresultarray[$key][1], $notlexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($notlmvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($notlmvcresultarray[$key][5], $notloppresultarray);

    if (!empty($keyvalindex)) {
        $notlmvcresultarray[$key][6] = $notloppresultarray[$keyvalindex]['CSEPKGU'];
        $notlmvcresultarray[$key][7] = $notloppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($notlmvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $notlmvcresultarray[$key][0];
    $VCITEM = $notlmvcresultarray[$key][1];
    $VCLOC = $notlmvcresultarray[$key][2];
    $VCPKGU = $notlmvcresultarray[$key][3];
    $VCGRD5 = $notlmvcresultarray[$key][4];
    $CASEPKGU = $notlmvcresultarray[$key][6];
    $TOTOPP = $notlmvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$notlmvcresultarray = NULL;
$notloppresultarray = NULL;
$notlexcl1resultarray = NULL;
$notlexcl2resultarray = NULL;





//VANC

$vancmvcresult = $aseriesconn->prepare("SELECT VCWHSE, VCITEM, VCLOC#, VCPKGU, VCGRD5, VCWHSE||VCITEM||VCPKGU as KEYFIELD FROM A.ARCPCORDTA.NPFMVC WHERE VCWHSE = 12 and VCPKGU = 1 and VCFTIR in ('L02','L04','L06')");
$vancmvcresult->execute();
$vancmvcresultarray = $vancmvcresult->fetchAll(PDO::FETCH_NUM);

$vancoppresult = $aseriesconn->prepare("SELECT PDWHSE, PDWCS#, PDITEM, sum(PDPCKQ), PDPKGU, PCCPKU as CSEPKGU, sum(PDPCKQ) -  floor(sum(PDPCKQ) / PCCPKU) - mod(sum(PDPCKQ), PCCPKU) as OPP, PDWHSE||PDITEM||PDPKGU as KEYValue  FROM A.ARCPCORDTA.NOTWPT, A.ARCPCORDTA.NPFCPC WHERE (CASE WHEN (PDSHPD<99999) THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,1) || '-' || substr(PDSHPD,2,2))) WHEN PDSHPD>99999 THEN (date(('20' || RIGHT(PDSHPD,2)) || '-' || substr(PDSHPD,1,2) || '-' || substr(PDSHPD,3,2))) END) >= (CURRENT DATE - 180 Days) and PDWHSE = 12 and PCWHSE = 0 and PDITEM = PCITEM and PDBXSZ <> 'CSE' and (PCCPKU > 0 or PCIPKU > 0) and PDPKGU = 1 and PDITEM between '100000' and '999999' and PDPCKQ > 1 and PCCPKU > 1 group by PDWHSE, PDWCS#, PDITEM, PDPKGU, PCCPKU, PCIPKU HAVING sum(PDPCKQ) >= PCCPKU");
$vancoppresult->execute();
$vancoppresultarray = $vancoppresult->fetchAll(PDO::FETCH_ASSOC);

$vancexcl1 = $aseriesconn->prepare("select LMITEM as KEYValue from A.ARCPCORDTA.NPFLSM where LMWHSE = 12 and (LMTIER in('L17','L18','L03','L15') or LMTIER like 'C%')");
$vancexcl1->execute();
$vancexcl1resultarray = $vancexcl1->fetchAll(PDO::FETCH_ASSOC);

$vancexcl2 = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (0) and PCPFRC in ('P')");
$vancexcl2->execute();
$vancexcl2resultarray = $vancexcl2->fetchAll(PDO::FETCH_ASSOC);

$localpfrsetting = $aseriesconn->prepare("SELECT DISTINCT PCITEM as KEYValue, PCPFRC, PCPFRA FROM A.ARCPCORDTA.NPFCPC WHERE PCWHSE in (12) and PCPFRA in ('N')");
$localpfrsetting->execute();
$localpfrsettingarray = $localpfrsetting->fetchAll(PDO::FETCH_ASSOC);

//remove item from exclusion array if local setting is set to N
foreach ($vancexcl2resultarray as $key => $value) {
    $keyvalexcl = _exclusion($vancexcl2resultarray[$key]['KEYVALUE'], $localpfrsettingarray);
        if (!empty($keyvalexcl)) {
        unset($vancexcl2resultarray[$key]);
        continue;
    }
}



foreach ($vancmvcresultarray as $key => $value) {

    $keyvalexcl1 = _exclusion($vancmvcresultarray[$key][1], $vancexcl1resultarray);
    if (!empty($keyvalexcl1)) {
        unset($vancmvcresultarray[$key]);
        continue;
    }

    $keyvalexcl2 = _exclusion($vancmvcresultarray[$key][1], $vancexcl2resultarray);
    if (!empty($keyvalexcl2)) {
        unset($vancmvcresultarray[$key]);
        continue;
    }

    $keyvalindex = _exclusion($vancmvcresultarray[$key][5], $vancoppresultarray);

    if (!empty($keyvalindex)) {
        $vancmvcresultarray[$key][6] = $vancoppresultarray[$keyvalindex]['CSEPKGU'];
        $vancmvcresultarray[$key][7] = $vancoppresultarray[$keyvalindex]['OPP'];
    } else {
        unset($vancmvcresultarray[$key]);
        continue;
    }

    $VCWHSE = $vancmvcresultarray[$key][0];
    $VCITEM = $vancmvcresultarray[$key][1];
    $VCLOC = $vancmvcresultarray[$key][2];
    $VCPKGU = $vancmvcresultarray[$key][3];
    $VCGRD5 = $vancmvcresultarray[$key][4];
    $CASEPKGU = $vancmvcresultarray[$key][6];
    $TOTOPP = $vancmvcresultarray[$key][7];

    $sql = "INSERT INTO caseopps (VCWHSE, VCITEM, VCLOC, VCPKGU, VCGRD5, CASEPKGU, TOTOPP) VALUES (:VCWHSE, :VCITEM, :VCLOC, :VCPKGU, :VCGRD5, :CASEPKGU, :TOTOPP)";
    $query = $conn1->prepare($sql);
    $query->execute(array(':VCWHSE' => $VCWHSE, ':VCITEM' => $VCITEM, ':VCLOC' => $VCLOC, ':VCPKGU' => $VCPKGU, ':VCGRD5' => $VCGRD5, ':CASEPKGU' => $CASEPKGU, ':TOTOPP' => $TOTOPP));
}

$vancmvcresultarray = NULL;
$vancoppresultarray = NULL;
$vancexcl1resultarray = NULL;
$vancexcl2resultarray = NULL;
























$conn1 = null;


