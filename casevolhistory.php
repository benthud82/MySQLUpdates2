
<?php

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
//include_once '../globalincludes/nahsi_mysql.php';
include_once '../globalincludes/usa_asys.php';
include_once '../CustomerAudit/functions/customer_audit_functions.php';
include_once '../CustomerAudit/connection/connection_details.php';
//include_once '../globalincludes/newcanada_asys.php';


$columns = 'casevol_whse, casevol_build, casevol_date, casevol_hour, casevol_equip, casevol_cubeinch, casevol_boxcount';

$whsearray = array(2,3,6,7,9);

foreach ($whsearray as $whsval) {

    $tierresult = $aseriesconn->prepare("SELECT 
                                                                            PBWHSE, 
                                                                            CASE WHEN PBWHSE = 3 and PDLOC# >= 'W400000' then 2 else 1 end as PBBUILD, 
                                                                            PBRCJD, 
                                                                            PBRCHR,
                                                                            case                                                                                                 
                                                                                when LMTIER = 'C01' then 'PALLETJACK' 
                                                                                when LMTIER = 'C02' then 'BELTLINE'
                                                                                when LMTIER = 'C03' and substr(LMLOC#,6,1) = '1' then 'PALLETJACK'
                                                                                when LMTIER in ('C05', 'C06') and substr(LMLOC#,6,1) >= '2'  then 'ORDERPICKER'
                                                                                else 'ORDERPICKER' 
                                                                            end as EQUIP_TYPE,
                                                                            SUM(case when PCCVOL = 0 then PCEVOL  * .0610237 else PCCVOL * .0610237 end) as CUBIC_INCH, 
                                                                            count(*) as BOX_COUNT
                                                                        FROM HSIPCORDTA.NOTWPT A  
                                                                        JOIN HSIPCORDTA.NPFCPC on PCITEM = PDITEM  
                                                                        JOIN HSIPCORDTA.NOTWPS on PDWCS# = PBWCS# and PDWKNO = PBWKNO and PBBOX# = PDBOX#
                                                                        LEFT JOIN HSIPCORDTA.NPFLSM on LMWHSE = PDWHSE and LMLOC# = PDLOC# 
                                                                        WHERE 
                                                                            PDWHSE = $whsval
                                                                            and PDBXSZ = 'CSE'
                                                                            and PDLOC# not like '%SDS%'
                                                                            and PCWHSE = 0  
                                                                        GROUP BY PBWHSE, CASE WHEN PBWHSE = 3 and PDLOC# >= 'W400000' then 2 else 1 end, PBRCJD, PBRCHR, case when LMTIER = 'C01' then 'PALLETJACK' when LMTIER = 'C02' then 'BELTLINE'  when LMTIER = 'C03' and substr(LMLOC#,6,1) = '1' then 'PALLETJACK' when LMTIER in ('C05', 'C06') and substr(LMLOC#,6,1) >= '2'  then 'ORDERPICKER' else 'ORDERPICKER' end");
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
            $casevol_whse = intval($tierarray[$counter]['PBWHSE']);
            $casevol_build = intval($tierarray[$counter]['PBBUILD']);
            $casevol_date = _jdatetomysqldate($tierarray[$counter]['PBRCJD']);
            $casevol_hour = intval($tierarray[$counter]['PBRCHR']);
            $casevol_equi = ($tierarray[$counter]['EQUIP_TYPE']);
            $casevol_cubeinch = ($tierarray[$counter]['CUBIC_INCH']);
            $casevol_boxcount = intval($tierarray[$counter]['BOX_COUNT']);





            $data[] = "($casevol_whse, $casevol_build, '$casevol_date', $casevol_hour, '$casevol_equi', '$casevol_cubeinch', $casevol_boxcount)";
            $counter += 1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.case_historicalvolume ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange += 10000;
    } while ($counter <= $rowcount); //end of item by whse loop
}

