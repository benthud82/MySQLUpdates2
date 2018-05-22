
<?php

//COUNT NUMBER OF LINES BY BILLTO/SHIPTO FOR CURRENT MONTH, CURRENT QUARTER, AND ROLLING 12 MONTHS

set_time_limit(99999);
include '../globalincludes/nahsi_mysql.php';
include '../globalincludes/usa_asys.php';


$sqldelete = "TRUNCATE TABLE cardinalbillto";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();


ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');


//pull in all customer returns for specific bill-to for NEW CARDINAL CUSTOMERS
$custlist = $aseriesconn->prepare("SELECT DISTINCT mapa8 from A.HSIPDTA71.F0101,A.HSIPDTA71.F0301,A.HSIPDTA71.F0150 WHERE a5badt in ('X','S') and ABAC28 ='CRD'  and maostp = 'BIL' and aban8 =  a5an8 and aban8 = maan8");
$custlist->execute();
$custlistarray = $custlist->fetchAll(pdo::FETCH_NUM);



foreach ($custlistarray as $key => $value) {
    $custtype = 'NEW';
    $BILLTONUM = $custlistarray[$key][0];


    $sql = "INSERT IGNORE INTO cardinalbillto (BILLTONUM, TYPE) VALUES (:BILLTONUM, :TYPE)";

    $query = $conn1->prepare($sql);
    $query->execute(array(':BILLTONUM' => $BILLTONUM, ':TYPE' => $custtype));
}



//pull in all customer returns for specific bill-to for TIGHT CARDINAL CUSTOMERS
$TIGHTcustlist = $aseriesconn->prepare("SELECT DISTINCT mapa8 FROM A.HSIPDTA71.F0101, a.HSIPDTA71.F0301, a.HSIPDTA71.F0150, a.HSIPDTA71.F5611 WHERE aban8 =  a5an8 and aban8 = maan8 and aban8 = qlan8 and a5badt in ('X','S')   and ABAC28 <>'CRD'  and maostp = 'BIL' and ql".'$xtp'." = 'CRD'  and length(trim(ql".'$xrn'.")) = 10");
$TIGHTcustlist->execute();
$TIGHTcustlistarray = $TIGHTcustlist->fetchAll(pdo::FETCH_NUM);



foreach ($TIGHTcustlistarray as $key => $value) {
    $custtype = 'TIGHT';
    $BILLTONUM = $TIGHTcustlistarray[$key][0];


    $sql = "INSERT IGNORE INTO cardinalbillto (BILLTONUM, TYPE) VALUES (:BILLTONUM, :TYPE)";

    $query = $conn1->prepare($sql);
    $query->execute(array(':BILLTONUM' => $BILLTONUM, ':TYPE' => $custtype));
}





