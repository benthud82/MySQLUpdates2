
<?php
date_default_timezone_set('America/New_York');
set_time_limit(99999);
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
ini_set('max_allowed_packet', 999999999);
//include '../../globalincludes/usa_asys.php';
include '../globalfunctions/custdbfunctions.php';
include '../globalincludes/nahsi_mysql.php';  //production connection
//include '../globalincludes/ustxgpslotting_mysql.php';  //modelling connection



$sqldelete = "TRUNCATE TABLE customerscores_salesplan_merge";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$RECORDDATE = date('Y-m-d');

//Find the first day of current month 1yyddd
$roll_month_start_1yyddd = _rollmonth1yyddd();  //call current month function to find start for for current month for sql
//Find the first day of current quarter 1yyddd
$roll_quarter_start_1yyddd = _rollquarter1yyddd();  //call current quarter function to find start for for current quarter for sql
//Find first day for rolling 12 month 1yyddd
$rolling_12_start_1yyddd = _rolling12start1yyddd();  //call rolling start function to find start date for rolling 12 month sql
//Bill to info for summary Overall Summary Widget
$result1 = $conn1->prepare("SELECT 
    S.SALESPLAN,
    sum(CUR_MONTH_LINES) as TOTMONTHLINES,
    sum(CUR_MONTH_COGS) as TOTMONTHCOGS,
    sum(CUR_MONTH_SALES) as TOTMONTHSALES,
    sum(CUR_QTR_LINES) as TOTQTRLINES,
    sum(CUR_QTR_COGS) as TOTQTRCOGS,
    sum(CUR_QTR_SALES) as TOTQTRSALES,
    sum(ROLL_12_LINES) as TOTR12LINES,
    sum(ROLL_12_COGS) as TOTR12COGS,
    sum(ROLL_12_SALES) as TOTR12SALES,
    sum(CUR_MNT_BO) as TOTMNTBO,
    sum(CUR_QTR_BO) as TOTQTRBO,
    sum(R12_BO) as TOTR12BO,
    sum(CUR_MNT_BE) as TOTMNTBE,
    sum(CUR_QTR_BE) as TOTQTRBE,
    sum(R12_BE) as TOTR12BE,
    sum(CUR_MNT_D) as TOTMNTD,
    sum(CUR_QTR_D) as TOTQTRD,
    sum(R12_D) as TOTR12D,
    sum(CUR_MNT_XD) as TOTMNTXD,
    sum(CUR_QTR_XD) as TOTQTRXD,
    sum(R12_XD) as TOTR12XD,
    sum(CUR_MNT_XE) as TOTMNTXE,
    sum(CUR_QTR_XE) as TOTQTRXE,
    sum(R12_XE) as TOTR12XE,
    sum(CUR_MNT_XS) as TOTMNTXS,
    sum(CUR_QTR_XS) as TOTQTRXS,
    sum(R12_XS) as TOTR12XS,
    CASE
        WHEN sum(CUR_MONTH_LINES) = 0 THEN 1
        ELSE 1 - ((sum(CUR_MNT_BO) + sum(CUR_MNT_D) + sum(CUR_MNT_XD) + sum(CUR_MNT_XE) + sum(CUR_MNT_XS)) / sum(CUR_MONTH_LINES))
    END as BEFFRMNT,
    CASE
        WHEN sum(CUR_MONTH_LINES) = 0 THEN 1
        ELSE 1 - ((sum(CUR_MNT_BO) + sum(CUR_MNT_D) + sum(CUR_MNT_XD)) / sum(CUR_MONTH_LINES))
    END as AFTFRMNT,
    CASE
        WHEN sum(CUR_QTR_LINES) = 0 THEN 1
        ELSE 1 - ((sum(CUR_QTR_BO) + sum(CUR_QTR_D) + sum(CUR_QTR_XD) + sum(CUR_QTR_XE) + sum(CUR_QTR_XS)) / sum(CUR_QTR_LINES))
    END as BEFFRQTR,
    CASE
        WHEN sum(CUR_QTR_LINES) = 0 THEN 1
        Else 1 - ((sum(CUR_QTR_BO) + sum(CUR_QTR_D) + sum(CUR_QTR_XD)) / sum(CUR_QTR_LINES))
    END as AFTFRQTR,
    CASE
        WHEN sum(ROLL_12_LINES) = 0 then 1
        else 1 - ((sum(R12_BO) + sum(R12_D) + sum(R12_XD) + sum(R12_XE) + sum(R12_XS)) / sum(ROLL_12_LINES))
    END as BEFFRR12,
    CASE
        WHEN sum(ROLL_12_LINES) = 0 then 1
        else 1 - ((sum(R12_BO) + sum(R12_D) + sum(R12_XD)) / sum(ROLL_12_LINES))
    END as AFTFRR12,
    CASE
        WHEN sum(CUR_MONTH_LINES) = 0 then 1
        else 1 - ((sum(CUR_MNT_BO) + sum(CUR_MNT_XD) + sum(CUR_MNT_XE) + sum(CUR_MNT_XS)) / sum(CUR_MONTH_LINES))
    end as BEFFRMNT_EXCLDS,
    case
        when sum(CUR_MONTH_LINES) = 0 then 1
        else 1 - ((sum(CUR_MNT_BO) + sum(CUR_MNT_XD)) / sum(CUR_MONTH_LINES))
    end as AFTFRMNT_EXCLDS,
    case
        when sum(CUR_QTR_LINES) = 0 then 1
        else 1 - ((sum(CUR_QTR_BO) + sum(CUR_QTR_XD) + sum(CUR_QTR_XE) + sum(CUR_QTR_XS)) / sum(CUR_QTR_LINES))
    end as BEFFRQTR_EXCLDS,
    case
        when sum(CUR_QTR_LINES) = 0 then 1
        else 1 - ((sum(CUR_QTR_BO) + sum(CUR_QTR_XD)) / sum(CUR_QTR_LINES))
    end as AFTFRQTR_EXCLDS,
    case
        when sum(ROLL_12_LINES) = 0 then 1
        else 1 - ((sum(R12_BO) + sum(R12_XD) + sum(R12_XE) + sum(R12_XS)) / sum(ROLL_12_LINES))
    end as BEFFRR12_EXCLDS,
    case
        when sum(ROLL_12_LINES) = 0 then 1
        else 1 - ((sum(R12_BO) + sum(R12_XD)) / sum(ROLL_12_LINES))
    end as AFTFRR12_EXCLDS,
    sum(CUR_MNT_P_LINES) as CUR_MNT_P_LINES,
    sum(CUR_QTR_P_LINES) as CUR_QTR_P_LINES,
    sum(R12_P_LINES) as R12_P_LINES,
    sum(CUR_MNT_P_FR) as CUR_MNT_P_FR,
    sum(CUR_QTR_P_FR) as CUR_QTR_P_FR,
    sum(R12_P_FR) as R12_P_FR,
    case
        when sum(CUR_MNT_P_FR) * sum(CUR_MNT_P_LINES) = 0 then 1
        else 1 - (sum(CUR_MNT_P_FR) / sum(CUR_MNT_P_LINES))
    end as PBFRMNT,
    case
        when sum(CUR_QTR_P_FR) * sum(CUR_QTR_P_LINES) = 0 then 1
        else 1 - (sum(CUR_QTR_P_FR) / sum(CUR_QTR_P_LINES))
    end as PBFRQTR,
    case
        when sum(R12_P_FR) * sum(R12_P_LINES) = 0 then 1
        else 1 - (sum(R12_P_FR) / sum(R12_P_LINES))
    end as PBFRR12,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as shippingacc_monthly,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as shippingacc_quarter,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as shippingacc_rolling12,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('CRID' , 'TDNR')
                and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as damages_monthly,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('CRID' , 'TDNR')
                and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as damages_quarter,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('CRID' , 'TDNR')
                and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as damages_rolling12,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('EXPR' , 'SDAT',
                'TEMP',
                'LITR',
                'WIOD',
                'IBNO',
                'CNCL',
                'NRSP',
                'WQTY')
                and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as otherscdisc_monthly,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('EXPR' , 'SDAT',
                'TEMP',
                'LITR',
                'WIOD',
                'IBNO',
                'CNCL',
                'NRSP',
                'WQTY')
                and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as otherscdisc_quarter,
    (SELECT 
            count(ITEMCODE)
        FROM
            custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
        WHERE
            RETURNCODE in ('EXPR' , 'SDAT',
                'TEMP',
                'LITR',
                'WIOD',
                'IBNO',
                'CNCL',
                'NRSP',
                'WQTY')
                and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) as otherscdisc_rolling12,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                        and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_MONTH_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                    and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_MONTH_LINES))
    end) as SHIPACCPERCMNT,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                        and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_QTR_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                    and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_QTR_LINES))
    end) as SHIPACCPERCQTR,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                        and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(ROLL_12_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE 
                RETURNCODE in ('WISP' , 'WQSP', 'IBNS')
                    and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(ROLL_12_LINES))
    end) as SHIPACCPERCR12,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('CRID' , 'TDNR')
                        and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_MONTH_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('CRID' , 'TDNR')
                    and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_MONTH_LINES))
    end) as DAMAGEACCPERCMNT,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('CRID' , 'TDNR')
                        and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_QTR_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('CRID' , 'TDNR')
                    and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_QTR_LINES))
    end) as DAMAGEACCPERCQTR,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE 
                    RETURNCODE in ('CRID' , 'TDNR')
                        and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(ROLL_12_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('CRID' , 'TDNR')
                    and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(ROLL_12_LINES))
    end) as DAMAGEACCPERCR12,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('EXPR' , 'SDAT',
                        'TEMP',
                        'LITR',
                        'WIOD',
                        'IBNO',
                        'CNCL',
                        'NRSP',
                        'WQTY')
                        and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_MONTH_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('EXPR' , 'SDAT',
                    'TEMP',
                    'LITR',
                    'WIOD',
                    'IBNO',
                    'CNCL',
                    'NRSP',
                    'WQTY')
                    and RETURNDATE >= $roll_month_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_MONTH_LINES))
    end) as ADDSCACCPERCMNT,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('EXPR' , 'SDAT',
                        'TEMP',
                        'LITR',
                        'WIOD',
                        'IBNO',
                        'CNCL',
                        'NRSP',
                        'WQTY')
                        and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(CUR_QTR_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('EXPR' , 'SDAT',
                    'TEMP',
                    'LITR',
                    'WIOD',
                    'IBNO',
                    'CNCL',
                    'NRSP',
                    'WQTY')
                    and RETURNDATE >= $roll_quarter_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(CUR_QTR_LINES))
    end) as ADDSCACCPERCQTR,
    (case
        when
            (SELECT 
                    count(ITEMCODE)
                FROM
                    custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
                WHERE
                    RETURNCODE in ('EXPR' , 'SDAT',
                        'TEMP',
                        'LITR',
                        'WIOD',
                        'IBNO',
                        'CNCL',
                        'NRSP',
                        'WQTY')
                        and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) * sum(ROLL_12_LINES) = 0
        then
            1
        else 1 - ((SELECT 
                count(ITEMCODE)
            FROM
                custreturns R JOIN salesplan Q on Q.BILLTO = R.BILLTONUM and Q.SHIPTO = R.SHIPTONUM
            WHERE
                RETURNCODE in ('EXPR' , 'SDAT',
                    'TEMP',
                    'LITR',
                    'WIOD',
                    'IBNO',
                    'CNCL',
                    'NRSP',
                    'WQTY')
                    and RETURNDATE >= $rolling_12_start_1yyddd and Q.SALESPLAN = S.SALESPLAN) / sum(ROLL_12_LINES))
    end) as ADDSCACCPERCR12
FROM
    invlinesbyshipto L
        LEFT JOIN
    salesplan S ON S.BILLTO = L.BILLTONUM and S.SHIPTO = L.SHIPTONUM,
    fillratebyshipto F
WHERE
    F.BILLTO = L.BILLTONUM
        and F.SHIPTO = L.SHIPTONUM
GROUP BY S.SALESPLAN;");

$result1->execute();
$masterdisplayarray = $result1->fetchAll(pdo::FETCH_ASSOC);
foreach ($masterdisplayarray as $key => $value) {

    $SALESPLAN = $masterdisplayarray[$key]['SALESPLAN'];
    $TOTMONTHLINES = $masterdisplayarray[$key]['TOTMONTHLINES'];
    $TOTMONTHCOGS = $masterdisplayarray[$key]['TOTMONTHCOGS'];
    $TOTMONTHSALES = $masterdisplayarray[$key]['TOTMONTHSALES'];
    $TOTQTRLINES = $masterdisplayarray[$key]['TOTQTRLINES'];
    $TOTQTRCOGS = $masterdisplayarray[$key]['TOTQTRCOGS'];
    $TOTQTRSALES = $masterdisplayarray[$key]['TOTQTRSALES'];
    $TOTR12LINES = $masterdisplayarray[$key]['TOTR12LINES'];
    $TOTR12COGS = $masterdisplayarray[$key]['TOTR12COGS'];
    $TOTR12SALES = $masterdisplayarray[$key]['TOTR12SALES'];
    $TOTMNTBO = $masterdisplayarray[$key]['TOTMNTBO'];
    $TOTQTRBO = $masterdisplayarray[$key]['TOTQTRBO'];
    $TOTR12BO = $masterdisplayarray[$key]['TOTR12BO'];
    $TOTMNTBE = $masterdisplayarray[$key]['TOTMNTBE'];
    $TOTQTRBE = $masterdisplayarray[$key]['TOTQTRBE'];
    $TOTR12BE = $masterdisplayarray[$key]['TOTR12BE'];
    $TOTMNTD = $masterdisplayarray[$key]['TOTMNTD'];
    $TOTQTRD = $masterdisplayarray[$key]['TOTQTRD'];
    $TOTR12D = $masterdisplayarray[$key]['TOTR12D'];
    $TOTMNTXD = $masterdisplayarray[$key]['TOTMNTXD'];
    $TOTQTRXD = $masterdisplayarray[$key]['TOTQTRXD'];
    $TOTR12XD = $masterdisplayarray[$key]['TOTR12XD'];
    $TOTMNTXE = $masterdisplayarray[$key]['TOTMNTXE'];
    $TOTQTRXE = $masterdisplayarray[$key]['TOTQTRXE'];
    $TOTR12XE = $masterdisplayarray[$key]['TOTR12XE'];
    $TOTMNTXS = $masterdisplayarray[$key]['TOTMNTXS'];
    $TOTQTRXS = $masterdisplayarray[$key]['TOTQTRXS'];
    $TOTR12XS = $masterdisplayarray[$key]['TOTR12XS'];
    $BEFFRMNT = $masterdisplayarray[$key]['BEFFRMNT'];
    $AFTFRMNT = $masterdisplayarray[$key]['AFTFRMNT'];
    $BEFFRQTR = $masterdisplayarray[$key]['BEFFRQTR'];
    $AFTFRQTR = $masterdisplayarray[$key]['AFTFRQTR'];
    $BEFFRR12 = $masterdisplayarray[$key]['BEFFRR12'];
    $AFTFRR12 = $masterdisplayarray[$key]['AFTFRR12'];
    $BEFFRMNT_EXCLDS = $masterdisplayarray[$key]['BEFFRMNT_EXCLDS'];
    $AFTFRMNT_EXCLDS = $masterdisplayarray[$key]['AFTFRMNT_EXCLDS'];
    $BEFFRQTR_EXCLDS = $masterdisplayarray[$key]['BEFFRQTR_EXCLDS'];
    $AFTFRQTR_EXCLDS = $masterdisplayarray[$key]['AFTFRQTR_EXCLDS'];
    $BEFFRR12_EXCLDS = $masterdisplayarray[$key]['BEFFRR12_EXCLDS'];
    $AFTFRR12_EXCLDS = $masterdisplayarray[$key]['AFTFRR12_EXCLDS'];
    $CUR_MNT_P_LINES = $masterdisplayarray[$key]['CUR_MNT_P_LINES'];
    $CUR_QTR_P_LINES = $masterdisplayarray[$key]['CUR_QTR_P_LINES'];
    $R12_P_LINES = $masterdisplayarray[$key]['R12_P_LINES'];
    $CUR_MNT_P_FR = $masterdisplayarray[$key]['CUR_MNT_P_FR'];
    $CUR_QTR_P_FR = $masterdisplayarray[$key]['CUR_QTR_P_FR'];
    $R12_P_FR = $masterdisplayarray[$key]['R12_P_FR'];
    $PBFRMNT = $masterdisplayarray[$key]['PBFRMNT'];
    $PBFRQTR = $masterdisplayarray[$key]['PBFRQTR'];
    $PBFRR12 = $masterdisplayarray[$key]['PBFRR12'];
    $SHIPACCPERCMNT = $masterdisplayarray[$key]['SHIPACCPERCMNT'];
    $DAMAGEACCPERCMNT = $masterdisplayarray[$key]['DAMAGEACCPERCMNT'];
    $ADDSCACCPERCMNT = $masterdisplayarray[$key]['ADDSCACCPERCMNT'];
    $SHIPACCPERCQTR = $masterdisplayarray[$key]['SHIPACCPERCQTR'];
    $DAMAGEACCPERCQTR = $masterdisplayarray[$key]['DAMAGEACCPERCQTR'];
    $ADDSCACCPERCQTR = $masterdisplayarray[$key]['ADDSCACCPERCQTR'];
    $SHIPACCPERCR12 = $masterdisplayarray[$key]['SHIPACCPERCR12'];
    $DAMAGEACCPERCR12 = $masterdisplayarray[$key]['DAMAGEACCPERCR12'];
    $ADDSCACCPERCR12 = $masterdisplayarray[$key]['ADDSCACCPERCR12'];


//Order Shipped Complete Data
    $result2 = $conn1->prepare("SELECT 
    sum(ORDERS_COMPLETE_MNTH) / sum(TOTAL_ORDERS_MNTH) AS MNTHOSC,
    sum(ORDERS_COMPLETE_QTR) / sum(TOTAL_ORDERS_QTR) AS QTROSC,
    sum(ORDERS_COMPLETE_R12) / sum(TOTAL_ORDERS_R12) AS R12OSC,
    sum(ORDERS_COMPLETE_MNTH_EXCLDS) / sum(TOTAL_ORDERS_MNTH) AS MNTHOSC_EXCLDS,
    sum(ORDERS_COMPLETE_QTR_EXCLDS) / sum(TOTAL_ORDERS_QTR) AS QTROSC_EXCLDS,
    sum(ORDERS_COMPLETE_R12_EXCLDS) / sum(TOTAL_ORDERS_R12) AS R12OSC_EXCLDS
FROM
    oscbyshipto C
JOIN salesplan S on BILLTONUM = BILLTO and SHIPTONUM = SHIPTO
WHERE SALESPLAN = '$SALESPLAN'
GROUP BY SALESPLAN");
    $result2->execute();
    foreach ($result2 as $row) {
        $MNTHOSC = $row['MNTHOSC'];
        $masterdisplayarray[$key]['MNTHOSC'] = $MNTHOSC;
        $QTROSC = $row['QTROSC'];
        $masterdisplayarray[$key]['QTROSC'] = $QTROSC;
        $R12OSC = $row['R12OSC'];
        $masterdisplayarray[$key]['R12OSC'] = $R12OSC;
        $MNTHOSC_EXCLDS = $row['MNTHOSC_EXCLDS'];
        $masterdisplayarray[$key]['MNTHOSC_EXCLDS'] = $MNTHOSC_EXCLDS;
        $QTROSC_EXCLDS = $row['QTROSC_EXCLDS'];
        $masterdisplayarray[$key]['QTROSC_EXCLDS'] = $QTROSC_EXCLDS;
        $R12OSC_EXCLDS = $row['R12OSC_EXCLDS'];
        $masterdisplayarray[$key]['R12OSC_EXCLDS'] = $R12OSC_EXCLDS;
    }

//calculated scores for roll12, qtr, and month
    $masterdisplayarray[$key]['CUSTSCOREMNT'] = $BEFFRMNT * $AFTFRMNT * $SHIPACCPERCMNT * $DAMAGEACCPERCMNT * $ADDSCACCPERCMNT * $MNTHOSC;
    $masterdisplayarray[$key]['CUSTSCOREQTR'] = $BEFFRQTR * $AFTFRQTR * $SHIPACCPERCQTR * $DAMAGEACCPERCQTR * $ADDSCACCPERCQTR * $QTROSC;
    $masterdisplayarray[$key]['CUSTSCORER12'] = $BEFFRR12 * $AFTFRR12 * $SHIPACCPERCR12 * $DAMAGEACCPERCR12 * $ADDSCACCPERCR12 * $R12OSC;
    $masterdisplayarray[$key]['CUSTSCOREMNT_EXCLDS'] = $BEFFRMNT_EXCLDS * $AFTFRMNT_EXCLDS * $SHIPACCPERCMNT * $DAMAGEACCPERCMNT * $ADDSCACCPERCMNT * $MNTHOSC_EXCLDS;
    $masterdisplayarray[$key]['CUSTSCOREQTR_EXCLDS'] = $BEFFRQTR_EXCLDS * $AFTFRQTR_EXCLDS * $SHIPACCPERCQTR * $DAMAGEACCPERCQTR * $ADDSCACCPERCQTR * $QTROSC_EXCLDS;
    $masterdisplayarray[$key]['CUSTSCORER12_EXCLDS'] = $BEFFRR12_EXCLDS * $AFTFRR12_EXCLDS * $SHIPACCPERCR12 * $DAMAGEACCPERCR12 * $ADDSCACCPERCR12 * $R12OSC_EXCLDS;

    //SQL Query for trend

    $xcordarray = array();
    $resulttrend = $conn1->prepare("SELECT * FROM slotting.custscoresbyday_salesplan WHERE RECORDDATE BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() and SALESPLAN = '$SALESPLAN' ORDER BY RECORDDATE asc");
    $resulttrend->execute();
    $resultarraytrend = $resulttrend->fetchAll(PDO::FETCH_ASSOC);
    $trendarraycount = count($resultarraytrend);

    if ($trendarraycount <= 1) {  //no slope with 1 occurrences
        $masterdisplayarray[$key]['slope30day'] = $masterdisplayarray[$key]['slope90day'] = $masterdisplayarray[$key]['slope12mon'] = $masterdisplayarray[$key]['slopelines30day'] = $masterdisplayarray[$key]['slopelines90day'] = $masterdisplayarray[$key]['slopelines12mon'] = $masterdisplayarray[$key]['slopeBO30day'] = $masterdisplayarray[$key]['slopeBO90day'] = $masterdisplayarray[$key]['slopeBO12mon'] = $masterdisplayarray[$key]['slopeBE30day'] = $masterdisplayarray[$key]['slopeBE90day'] = $masterdisplayarray[$key]['slopeBE12mon'] = $masterdisplayarray[$key]['slopeD30day'] = $masterdisplayarray[$key]['slopeD90day'] = $masterdisplayarray[$key]['slopeD12mon'] = $masterdisplayarray[$key]['slopeXD30day'] = $masterdisplayarray[$key]['slopeXD90day'] = $masterdisplayarray[$key]['slopeXD12mon'] = $masterdisplayarray[$key]['slopeXS30day'] = $masterdisplayarray[$key]['slopeXS90day'] = $masterdisplayarray[$key]['slopeXS12mon'] = $masterdisplayarray[$key]['slopeXE30day'] = $masterdisplayarray[$key]['slopeXE90day'] = $masterdisplayarray[$key]['slopeXE12mon'] = $masterdisplayarray[$key]['slopeBFFR30day'] = $masterdisplayarray[$key]['slopeBFFR90day'] = $masterdisplayarray[$key]['slopeBFFR12mon'] = $masterdisplayarray[$key]['slopeAFTFR30day'] = $masterdisplayarray[$key]['slopeAFTFR90day'] = $masterdisplayarray[$key]['slopeAFTFR12mon'] = $masterdisplayarray[$key]['slopeSHIPACC30day'] = $masterdisplayarray[$key]['slopeSHIPACC90day'] = $masterdisplayarray[$key]['slopeSHIPACC12mon'] = $masterdisplayarray[$key]['slopeDMGACC30day'] = $masterdisplayarray[$key]['slopeDMGACC90day'] = $masterdisplayarray[$key]['slopeDMGACC12mon'] = $masterdisplayarray[$key]['slopeADDSC30day'] = $masterdisplayarray[$key]['slopeADDSC90day'] = $masterdisplayarray[$key]['slopeADDSC12mon'] = $masterdisplayarray[$key]['slopeOSC30day'] = $masterdisplayarray[$key]['slopeOSC90day'] = $masterdisplayarray[$key]['slopeOSC12mon'] = $masterdisplayarray[$key]['slope30day_exclds'] = $masterdisplayarray[$key]['slope90day_exclds'] = $masterdisplayarray[$key]['slope12mon_exclds'] = $masterdisplayarray[$key]['slopeBFFR30day_exclds'] = $masterdisplayarray[$key]['slopeBFFR90day_exclds'] = $masterdisplayarray[$key]['slopeBFFR12mon_exclds'] = $masterdisplayarray[$key]['slopeAFTFR30day_exclds'] = $masterdisplayarray[$key]['slopeAFTFR90day_exclds'] = $masterdisplayarray[$key]['slopeAFTFR12mon_exclds'] = $masterdisplayarray[$key]['slopeOSC30day_exclds'] = $masterdisplayarray[$key]['slopeOSC90day_exclds'] = $masterdisplayarray[$key]['slopeOSC12mon_exclds'] = $masterdisplayarray[$key]['SLOPEPBFRMNT'] = $masterdisplayarray[$key]['SLOPEPBFRQTR'] = $masterdisplayarray[$key]['SLOPEPBFRR12'] = 0;
    } else {

        for ($i = 1; $i <= $trendarraycount; ++$i) {
            $xcordarray[] = $i;
        }
        $moveycordarray30day = $moveycordarray90day = $moveycordarray12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray30day[] = $resultarraytrend[$key2]['SCOREMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray90day[] = $resultarraytrend[$key2]['SCOREQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray12mon[] = $resultarraytrend[$key2]['SCOREROLL12'];
        }


        $moveycordarraylines30day = $moveycordarraylines90day = $moveycordarraylines12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraylines30day[] = $resultarraytrend[$key2]['LINESMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraylines90day[] = $resultarraytrend[$key2]['LINESQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraylines12mon[] = $resultarraytrend[$key2]['LINESROLL12'];
        }

        $moveycordarrayBO30day = $moveycordarrayBO90day = $moveycordarrayBO12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayBO30day[] = 0;
            } else {
                $moveycordarrayBO30day[] = $resultarraytrend[$key2]['BOMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayBO90day[] = 0;
            } else {
                $moveycordarrayBO90day[] = $resultarraytrend[$key2]['BOQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayBO12mon[] = 0;
            } else {
                $moveycordarrayBO12mon[] = $resultarraytrend[$key2]['BOROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayBE30day = $moveycordarrayBE90day = $moveycordarrayBE12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayBE30day[] = 0;
            } else {
                $moveycordarrayBE30day[] = $resultarraytrend[$key2]['BEMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayBE90day[] = 0;
            } else {
                $moveycordarrayBE90day[] = $resultarraytrend[$key2]['BEQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayBE12mon[] = 0;
            } else {
                $moveycordarrayBE12mon[] = $resultarraytrend[$key2]['BEROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayD30day = $moveycordarrayD90day = $moveycordarrayD12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayD30day[] = 0;
            } else {
                $moveycordarrayD30day[] = $resultarraytrend[$key2]['DMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayD90day[] = 0;
            } else {
                $moveycordarrayD90day[] = $resultarraytrend[$key2]['DQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayD12mon[] = 0;
            } else {
                $moveycordarrayD12mon[] = $resultarraytrend[$key2]['DROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayXD30day = $moveycordarrayXD90day = $moveycordarrayXD12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayXD30day[] = 0;
            } else {
                $moveycordarrayXD30day[] = $resultarraytrend[$key2]['XDMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayXD90day[] = 0;
            } else {
                $moveycordarrayXD90day[] = $resultarraytrend[$key2]['XDQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayXD12mon[] = 0;
            } else {
                $moveycordarrayXD12mon[] = $resultarraytrend[$key2]['XDROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayXE30day = $moveycordarrayXE90day = $moveycordarrayXE12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayXE30day[] = 0;
            } else {
                $moveycordarrayXE30day[] = $resultarraytrend[$key2]['XEMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayXE90day[] = 0;
            } else {
                $moveycordarrayXE90day[] = $resultarraytrend[$key2]['XEQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayXE12mon[] = 0;
            } else {
                $moveycordarrayXE12mon[] = $resultarraytrend[$key2]['XEROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayXS30day = $moveycordarrayXS90day = $moveycordarrayXS12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESMONTH'] == 0) {
                $moveycordarrayXS30day[] = 0;
            } else {
                $moveycordarrayXS30day[] = $resultarraytrend[$key2]['XSMONTH'] / $resultarraytrend[$key2]['LINESMONTH'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESQUARTER'] == 0) {
                $moveycordarrayXS90day[] = 0;
            } else {
                $moveycordarrayXS90day[] = $resultarraytrend[$key2]['XSQUARTER'] / $resultarraytrend[$key2]['LINESQUARTER'];
            }
        }
        foreach ($resultarraytrend as $key2 => $value) {
            if ($resultarraytrend[$key2]['LINESROLL12'] == 0) {
                $moveycordarrayXS12mon[] = 0;
            } else {
                $moveycordarrayXS12mon[] = $resultarraytrend[$key2]['XSROLL12'] / $resultarraytrend[$key2]['LINESROLL12'];
            }
        }


        $moveycordarrayBFFR30day = $moveycordarrayBFFR90day = $moveycordarrayBFFR12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR30day[] = $resultarraytrend[$key2]['BEFFRMNT'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR90day[] = $resultarraytrend[$key2]['BEFFRQTR'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR12mon[] = $resultarraytrend[$key2]['BEFFRR12'];
        }


        $moveycordarrayAFTFR30day = $moveycordarrayAFTFR90day = $moveycordarrayAFTFR12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR30day[] = $resultarraytrend[$key2]['AFTFRMNT'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR90day[] = $resultarraytrend[$key2]['AFTFRQTR'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR12mon[] = $resultarraytrend[$key2]['AFTFRR12'];
        }





        $moveycordarraySHIPACC30day = $moveycordarraySHIPACC90day = $moveycordarraySHIPACC12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraySHIPACC30day[] = $resultarraytrend[$key2]['SHIPACCMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraySHIPACC90day[] = $resultarraytrend[$key2]['SHIPACCQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarraySHIPACC12mon[] = $resultarraytrend[$key2]['SHIPACCROLL12'];
        }


        $moveycordarrayDMGACC30day = $moveycordarrayDMGACC90day = $moveycordarrayDMGACC12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayDMGACC30day[] = $resultarraytrend[$key2]['DMGACCMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayDMGACC90day[] = $resultarraytrend[$key2]['DMGACCQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayDMGACC12mon[] = $resultarraytrend[$key2]['DMGACCROLL12'];
        }


        $moveycordarrayADDSC30day = $moveycordarrayADDSC90day = $moveycordarrayADDSC12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayADDSC30day[] = $resultarraytrend[$key2]['ADDSCACCMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayADDSC90day[] = $resultarraytrend[$key2]['ADDSCACCQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayADDSC12mon[] = $resultarraytrend[$key2]['ADDSCACCROLL12'];
        }


        $moveycordarrayOSC30day = $moveycordarrayOSC90day = $moveycordarrayOSC12mon = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC30day[] = $resultarraytrend[$key2]['OSCMONTH'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC90day[] = $resultarraytrend[$key2]['OSCQUARTER'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC12mon[] = $resultarraytrend[$key2]['OSCROLL12'];
        }

        $moveycordarray30day_exclds = $moveycordarray90day_exclds = $moveycordarray12mon_exclds = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray30day_exclds[] = $resultarraytrend[$key2]['SCOREMONTH_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray90day_exclds[] = $resultarraytrend[$key2]['SCOREQUARTER_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarray12mon_exclds[] = $resultarraytrend[$key2]['SCOREROLL12_EXCLDS'];
        }



        $moveycordarrayBFFR30day_exclds = $moveycordarrayBFFR90day_exclds = $moveycordarrayBFFR12mon_exclds = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR30day_exclds[] = $resultarraytrend[$key2]['BEFFRMNT_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR90day_exclds[] = $resultarraytrend[$key2]['BEFFRQTR_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayBFFR12mon_exclds[] = $resultarraytrend[$key2]['BEFFRR12_EXCLDS'];
        }


        $moveycordarrayAFTFR30day_exclds = $moveycordarrayAFTFR90day_exclds = $moveycordarrayAFTFR12mon_exclds = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR30day_exclds[] = $resultarraytrend[$key2]['AFTFRMNT_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR90day_exclds[] = $resultarraytrend[$key2]['AFTFRQTR_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayAFTFR12mon_exclds[] = $resultarraytrend[$key2]['AFTFRR12_EXCLDS'];
        }


        $moveycordarrayOSC30day_exclds = $moveycordarrayOSC90day_exclds = $moveycordarrayOSC12mon_exclds = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC30day_exclds[] = $resultarraytrend[$key2]['OSCMONTH_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC90day_exclds[] = $resultarraytrend[$key2]['OSCQUARTER_EXCLDS'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayOSC12mon_exclds[] = $resultarraytrend[$key2]['OSCROLL12_EXCLDS'];
        }


        $moveycordarrayPBFRMNT = $moveycordarrayPBFRQTR = $moveycordarrayPBFRR12 = array();
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayPBFRMNT[] = $resultarraytrend[$key2]['PBFRMNT'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayPBFRQTR[] = $resultarraytrend[$key2]['PBFRQTR'];
        }
        foreach ($resultarraytrend as $key2 => $value) {
            $moveycordarrayPBFRR12[] = $resultarraytrend[$key2]['PBFRQTR'];
        }


        $linearregressionarray30day = linear_regression($xcordarray, $moveycordarray30day);
        $linearregressionarray90day = linear_regression($xcordarray, $moveycordarray90day);
        $linearregressionarray12mon = linear_regression($xcordarray, $moveycordarray12mon);

        $linearregressionarraylines30day = linear_regression($xcordarray, $moveycordarraylines30day);
        $linearregressionarraylines90day = linear_regression($xcordarray, $moveycordarraylines90day);
        $linearregressionarraylines12mon = linear_regression($xcordarray, $moveycordarray12mon);

        $linearregressionarrayBO30day = linear_regression($xcordarray, $moveycordarrayBO30day);
        $linearregressionarrayBO90day = linear_regression($xcordarray, $moveycordarrayBO90day);
        $linearregressionarrayBO12mon = linear_regression($xcordarray, $moveycordarrayBO12mon);

        $linearregressionarrayBE30day = linear_regression($xcordarray, $moveycordarrayBE30day);
        $linearregressionarrayBE90day = linear_regression($xcordarray, $moveycordarrayBE90day);
        $linearregressionarrayBE12mon = linear_regression($xcordarray, $moveycordarrayBE12mon);

        $linearregressionarrayD30day = linear_regression($xcordarray, $moveycordarrayD30day);
        $linearregressionarrayD90day = linear_regression($xcordarray, $moveycordarrayD90day);
        $linearregressionarrayD12mon = linear_regression($xcordarray, $moveycordarrayD12mon);

        $linearregressionarrayXD30day = linear_regression($xcordarray, $moveycordarrayXD30day);
        $linearregressionarrayXD90day = linear_regression($xcordarray, $moveycordarrayXD90day);
        $linearregressionarrayXD12mon = linear_regression($xcordarray, $moveycordarrayXD12mon);

        $linearregressionarrayXS30day = linear_regression($xcordarray, $moveycordarrayXS30day);
        $linearregressionarrayXS90day = linear_regression($xcordarray, $moveycordarrayXS90day);
        $linearregressionarrayXS12mon = linear_regression($xcordarray, $moveycordarrayXS12mon);

        $linearregressionarrayXE30day = linear_regression($xcordarray, $moveycordarrayXE30day);
        $linearregressionarrayXE90day = linear_regression($xcordarray, $moveycordarrayXE90day);
        $linearregressionarrayXE12mon = linear_regression($xcordarray, $moveycordarrayXE12mon);

        $linearregressionarrayBFFR30day = linear_regression($xcordarray, $moveycordarrayBFFR30day);
        $linearregressionarrayBFFR90day = linear_regression($xcordarray, $moveycordarrayBFFR90day);
        $linearregressionarrayBFFR12mon = linear_regression($xcordarray, $moveycordarrayBFFR12mon);

        $linearregressionarrayAFTFR30day = linear_regression($xcordarray, $moveycordarrayAFTFR30day);
        $linearregressionarrayAFTFR90day = linear_regression($xcordarray, $moveycordarrayAFTFR90day);
        $linearregressionarrayAFTFR12mon = linear_regression($xcordarray, $moveycordarrayAFTFR12mon);

        $linearregressionarraySHIPACC30day = linear_regression($xcordarray, $moveycordarraySHIPACC30day);
        $linearregressionarraySHIPACC90day = linear_regression($xcordarray, $moveycordarraySHIPACC90day);
        $linearregressionarraySHIPACC12mon = linear_regression($xcordarray, $moveycordarraySHIPACC12mon);

        $linearregressionarrayDMGACC30day = linear_regression($xcordarray, $moveycordarrayDMGACC30day);
        $linearregressionarrayDMGACC90day = linear_regression($xcordarray, $moveycordarrayDMGACC90day);
        $linearregressionarrayDMGACC12mon = linear_regression($xcordarray, $moveycordarrayDMGACC12mon);

        $linearregressionarrayADDSC30day = linear_regression($xcordarray, $moveycordarrayADDSC30day);
        $linearregressionarrayADDSC90day = linear_regression($xcordarray, $moveycordarrayADDSC90day);
        $linearregressionarrayADDSC12mon = linear_regression($xcordarray, $moveycordarrayADDSC12mon);

        $linearregressionarrayOSC30day = linear_regression($xcordarray, $moveycordarrayOSC30day);
        $linearregressionarrayOSC90day = linear_regression($xcordarray, $moveycordarrayOSC90day);
        $linearregressionarrayOSC12mon = linear_regression($xcordarray, $moveycordarrayOSC12mon);

        $linearregressionarray30day_exclds = linear_regression($xcordarray, $moveycordarray30day_exclds);
        $linearregressionarray90day_exclds = linear_regression($xcordarray, $moveycordarray90day_exclds);
        $linearregressionarray12mon_exclds = linear_regression($xcordarray, $moveycordarray12mon_exclds);

        $linearregressionarrayBFFR30day_exclds = linear_regression($xcordarray, $moveycordarrayBFFR30day_exclds);
        $linearregressionarrayBFFR90day_exclds = linear_regression($xcordarray, $moveycordarrayBFFR90day_exclds);
        $linearregressionarrayBFFR12mon_exclds = linear_regression($xcordarray, $moveycordarrayBFFR12mon_exclds);

        $linearregressionarrayAFTFR30day_exclds = linear_regression($xcordarray, $moveycordarrayAFTFR30day_exclds);
        $linearregressionarrayAFTFR90day_exclds = linear_regression($xcordarray, $moveycordarrayAFTFR90day_exclds);
        $linearregressionarrayAFTFR12mon_exclds = linear_regression($xcordarray, $moveycordarrayAFTFR12mon_exclds);

        $linearregressionarrayOSC30day_exclds = linear_regression($xcordarray, $moveycordarrayOSC30day_exclds);
        $linearregressionarrayOSC90day_exclds = linear_regression($xcordarray, $moveycordarrayOSC90day_exclds);
        $linearregressionarrayOSC12mon_exclds = linear_regression($xcordarray, $moveycordarrayOSC12mon_exclds);


        $linearregressionarrayPBFRMNT = linear_regression($xcordarray, $moveycordarrayPBFRMNT);
        $linearregressionarrayPBFRQTR = linear_regression($xcordarray, $moveycordarrayPBFRQTR);
        $linearregressionarrayPBFRR12 = linear_regression($xcordarray, $moveycordarrayPBFRR12);


        $masterdisplayarray[$key]['slope30day'] = number_format($linearregressionarray30day['m'], 4);
        $masterdisplayarray[$key]['slope90day'] = number_format($linearregressionarray90day['m'], 4);
        $masterdisplayarray[$key]['slope12mon'] = number_format($linearregressionarray12mon['m'], 4);

        $masterdisplayarray[$key]['slopelines30day'] = number_format($linearregressionarraylines30day['m'], 4);
        $masterdisplayarray[$key]['slopelines90day'] = number_format($linearregressionarraylines90day['m'], 4);
        $masterdisplayarray[$key]['slopelines12mon'] = number_format($linearregressionarraylines12mon['m'], 4);

        $masterdisplayarray[$key]['slopeBO30day'] = number_format($linearregressionarrayBO30day['m'], 4);
        $masterdisplayarray[$key]['slopeBO90day'] = number_format($linearregressionarrayBO90day['m'], 4);
        $masterdisplayarray[$key]['slopeBO12mon'] = number_format($linearregressionarrayBO12mon['m'], 4);

        $masterdisplayarray[$key]['slopeBE30day'] = number_format($linearregressionarrayBE30day['m'], 4);
        $masterdisplayarray[$key]['slopeBE90day'] = number_format($linearregressionarrayBE90day['m'], 4);
        $masterdisplayarray[$key]['slopeBE12mon'] = number_format($linearregressionarrayBE12mon['m'], 4);

        $masterdisplayarray[$key]['slopeD30day'] = number_format($linearregressionarrayD30day['m'], 4);
        $masterdisplayarray[$key]['slopeD90day'] = number_format($linearregressionarrayD90day['m'], 4);
        $masterdisplayarray[$key]['slopeD12mon'] = number_format($linearregressionarrayD12mon['m'], 4);

        $masterdisplayarray[$key]['slopeXD30day'] = number_format($linearregressionarrayXD30day['m'], 4);
        $masterdisplayarray[$key]['slopeXD90day'] = number_format($linearregressionarrayXD90day['m'], 4);
        $masterdisplayarray[$key]['slopeXD12mon'] = number_format($linearregressionarrayXD12mon['m'], 4);

        $masterdisplayarray[$key]['slopeXS30day'] = number_format($linearregressionarrayXS30day['m'], 4);
        $masterdisplayarray[$key]['slopeXS90day'] = number_format($linearregressionarrayXS90day['m'], 4);
        $masterdisplayarray[$key]['slopeXS12mon'] = number_format($linearregressionarrayXS12mon['m'], 4);

        $masterdisplayarray[$key]['slopeXE30day'] = number_format($linearregressionarrayXE30day['m'], 4);
        $masterdisplayarray[$key]['slopeXE90day'] = number_format($linearregressionarrayXE90day['m'], 4);
        $masterdisplayarray[$key]['slopeXE12mon'] = number_format($linearregressionarrayXE12mon['m'], 4);

        $masterdisplayarray[$key]['slopeBFFR30day'] = number_format($linearregressionarrayBFFR30day['m'], 4);
        $masterdisplayarray[$key]['slopeBFFR90day'] = number_format($linearregressionarrayBFFR90day['m'], 4);
        $masterdisplayarray[$key]['slopeBFFR12mon'] = number_format($linearregressionarrayBFFR12mon['m'], 4);

        $masterdisplayarray[$key]['slopeAFTFR30day'] = number_format($linearregressionarrayAFTFR30day['m'], 4);
        $masterdisplayarray[$key]['slopeAFTFR90day'] = number_format($linearregressionarrayAFTFR90day['m'], 4);
        $masterdisplayarray[$key]['slopeAFTFR12mon'] = number_format($linearregressionarrayAFTFR12mon['m'], 4);

        $masterdisplayarray[$key]['slopeSHIPACC30day'] = number_format($linearregressionarraySHIPACC30day['m'], 4);
        $masterdisplayarray[$key]['slopeSHIPACC90day'] = number_format($linearregressionarraySHIPACC90day['m'], 4);
        $masterdisplayarray[$key]['slopeSHIPACC12mon'] = number_format($linearregressionarraySHIPACC12mon['m'], 4);

        $masterdisplayarray[$key]['slopeDMGACC30day'] = number_format($linearregressionarrayDMGACC30day['m'], 4);
        $masterdisplayarray[$key]['slopeDMGACC90day'] = number_format($linearregressionarrayDMGACC90day['m'], 4);
        $masterdisplayarray[$key]['slopeDMGACC12mon'] = number_format($linearregressionarrayDMGACC12mon['m'], 4);

        $masterdisplayarray[$key]['slopeADDSC30day'] = number_format($linearregressionarrayADDSC30day['m'], 4);
        $masterdisplayarray[$key]['slopeADDSC90day'] = number_format($linearregressionarrayADDSC90day['m'], 4);
        $masterdisplayarray[$key]['slopeADDSC12mon'] = number_format($linearregressionarrayADDSC12mon['m'], 4);

        $masterdisplayarray[$key]['slopeOSC30day'] = number_format($linearregressionarrayOSC30day['m'], 4);
        $masterdisplayarray[$key]['slopeOSC90day'] = number_format($linearregressionarrayOSC90day['m'], 4);
        $masterdisplayarray[$key]['slopeOSC12mon'] = number_format($linearregressionarrayOSC12mon['m'], 4);

        $masterdisplayarray[$key]['slope30day_exclds'] = number_format($linearregressionarray30day_exclds['m'], 4);
        $masterdisplayarray[$key]['slope90day_exclds'] = number_format($linearregressionarray90day_exclds['m'], 4);
        $masterdisplayarray[$key]['slope12mon_exclds'] = number_format($linearregressionarray12mon_exclds['m'], 4);

        $masterdisplayarray[$key]['slopeBFFR30day_exclds'] = number_format($linearregressionarrayBFFR30day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeBFFR90day_exclds'] = number_format($linearregressionarrayBFFR90day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeBFFR12mon_exclds'] = number_format($linearregressionarrayBFFR12mon_exclds['m'], 4);

        $masterdisplayarray[$key]['slopeAFTFR30day_exclds'] = number_format($linearregressionarrayAFTFR30day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeAFTFR90day_exclds'] = number_format($linearregressionarrayAFTFR90day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeAFTFR12mon_exclds'] = number_format($linearregressionarrayAFTFR12mon_exclds['m'], 4);

        $masterdisplayarray[$key]['slopeOSC30day_exclds'] = number_format($linearregressionarrayOSC30day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeOSC90day_exclds'] = number_format($linearregressionarrayOSC90day_exclds['m'], 4);
        $masterdisplayarray[$key]['slopeOSC12mon_exclds'] = number_format($linearregressionarrayOSC12mon_exclds['m'], 4);

        $masterdisplayarray[$key]['SLOPEPBFRMNT'] = number_format($linearregressionarrayPBFRMNT['m'], 4);
        $masterdisplayarray[$key]['SLOPEPBFRQTR'] = number_format($linearregressionarrayPBFRQTR['m'], 4);
        $masterdisplayarray[$key]['SLOPEPBFRR12'] = number_format($linearregressionarrayPBFRR12['m'], 4);
    }
}
//Need to push to Mysql table`
//columns for customerscoresbybilltomerge
$columns = 'SALESPLAN, TOTMONTHLINES, TOTMONTHCOGS, TOTMONTHSALES, TOTQTRLINES, TOTQTRCOGS, TOTQTRSALES, TOTR12LINES, TOTR12COGS, TOTR12SALES, TOTMNTBO, TOTQTRBO, TOTR12BO, TOTMNTBE, TOTQTRBE, TOTR12BE, TOTMNTD, TOTQTRD, TOTR12D, TOTMNTXD, TOTQTRXD, TOTR12XD, TOTMNTXE, TOTQTRXE, TOTR12XE, TOTMNTXS, TOTQTRXS, TOTR12XS, BEFFRMNT, AFTFRMNT, BEFFRQTR, AFTFRQTR, BEFFRR12, AFTFRR12, SHIPACCPERCMNT, SHIPACCPERCQTR, SHIPACCPERCR12, DAMAGEACCPERCMNT, DAMAGEACCPERCQTR, DAMAGEACCPERCR12, ADDSCACCPERCMNT, ADDSCACCPERCQTR, ADDSCACCPERCR12, MNTHOSC, QTROSC, R12OSC, CUSTSCOREMNT, CUSTSCOREQTR, CUSTSCORER12, BEFFRMNT_EXCLDS, AFTFRMNT_EXCLDS, BEFFRQTR_EXCLDS, AFTFRQTR_EXCLDS, BEFFRR12_EXCLDS, AFTFRR12_EXCLDS, MNTHOSC_EXCLDS, QTROSC_EXCLDS, R12OSC_EXCLDS, CUSTSCOREMNT_EXCLDS, CUSTSCOREQTR_EXCLDS, CUSTSCORER12_EXCLDS, CUR_MNT_P_LINES, CUR_QTR_P_LINES, R12_P_LINES, CUR_MNT_P_FR, CUR_QTR_P_FR, R12_P_FR, PBFRMNT, PBFRQTR, PBFRR12';
//columns for custscoresbyday
$columns2 = 'SALESPLAN, RECORDDATE, SCOREMONTH, SCOREQUARTER, SCOREROLL12, SLOPE30DAY, SLOPE90DAY, SLOPE12MON, LINESMONTH, LINESQUARTER, LINESROLL12, SLOPELINES30DAY, SLOPELINES90DAY, SLOPELINES12MON, BOMONTH, BOQUARTER, BOROLL12, SLOPEBO30DAY, SLOPEBO90DAY, SLOPEBO12MON,
BEMONTH,BEQUARTER,BEROLL12,SLOPEBE30DAY,SLOPEBE90DAY,SLOPEBE12MON,DMONTH,DQUARTER,DROLL12,SLOPED30DAY,SLOPED90DAY,SLOPED12MON,XDMONTH,XDQUARTER,XDROLL12,SLOPEXD30DAY,SLOPEXD90DAY,SLOPEXD12MON,XEMONTH,XEQUARTER,
XEROLL12,SLOPEXE30DAY,SLOPEXE90DAY,SLOPEXE12MON,XSMONTH,XSQUARTER,XSROLL12,SLOPEXS30DAY,SLOPEXS90DAY,SLOPEXS12MON,BEFFRMNT,AFTFRMNT,BEFFRQTR,AFTFRQTR,BEFFRR12,AFTFRR12,SLOPEBEFFRMNT,SLOPEAFTFRMNT,SLOPEBEFFRQTR,SLOPEAFTFRQTR,
SLOPEAFTFRR12,SLOPEBEFFRR12,SHIPACCMONTH,SHIPACCQUARTER,SHIPACCROLL12,SLOPESHIPACCMONTH,SLOPESHIPACCQUARTER,SLOPESHIPACCROLL12,DMGACCMONTH,DMGACCQUARTER,DMGACCROLL12,SLOPEDMGACCMONTH,SLOPEDMGACCQUARTER,SLOPEDMGACCROLL12,ADDSCACCMONTH,ADDSCACCQUARTER,ADDSCACCROLL12,SLOPEADDSCACCMONTH,SLOPEADDSCACCQUARTER,
SLOPEADDSCACCROLL12,OSCMONTH,OSCQUARTER,OSCROLL12,SLOPEOSCMONTH,SLOPEOSCQUARTER,SLOPEOSCROLL12,SCOREMONTH_EXCLDS,SCOREQUARTER_EXCLDS,SCOREROLL12_EXCLDS,SLOPE30DAY_EXCLDS,SLOPE90DAY_EXCLDS,SLOPE12MON_EXCLDS,
BEFFRMNT_EXCLDS,AFTFRMNT_EXCLDS,BEFFRQTR_EXCLDS,AFTFRQTR_EXCLDS,BEFFRR12_EXCLDS,AFTFRR12_EXCLDS,SLOPEBEFFRMNT_EXCLDS,SLOPEAFTFRMNT_EXCLDS,SLOPEBEFFRQTR_EXCLDS,SLOPEAFTFRQTR_EXCLDS,SLOPEAFTFRR12_EXCLDS,SLOPEBEFFRR12_EXCLDS,
OSCMONTH_EXCLDS,OSCQUARTER_EXCLDS,OSCROLL12_EXCLDS,SLOPEOSCMONTH_EXCLDS,SLOPEOSCQUARTER_EXCLDS,SLOPEOSCROLL12_EXCLDS,PBFRMNT,PBFRQTR,PBFRR12,SLOPEPBFRMNT,SLOPEPBFRQTR,SLOPEPBFRR12';

$maxrange = 999;
$counter = 0;
$rowcount = count($masterdisplayarray);

do {
    if ($maxrange > $rowcount) {  //prevent undefined offset
        $maxrange = $rowcount - 1;
    }
    $data = array();
    $values = array();
    $data2 = array();
    $values2= array();
    while ($counter <= $maxrange) {
        $SALESPLAN = $masterdisplayarray[$counter]['SALESPLAN'];
        $TOTMONTHLINES = intval($masterdisplayarray[$counter]['TOTMONTHLINES']);
        $TOTMONTHCOGS = $masterdisplayarray[$counter]['TOTMONTHCOGS'];
        $TOTMONTHSALES = $masterdisplayarray[$counter]['TOTMONTHSALES'];
        $TOTQTRLINES = intval($masterdisplayarray[$counter]['TOTQTRLINES']);
        $TOTQTRCOGS = $masterdisplayarray[$counter]['TOTQTRCOGS'];
        $TOTQTRSALES = $masterdisplayarray[$counter]['TOTQTRSALES'];
        $TOTR12LINES = intval($masterdisplayarray[$counter]['TOTR12LINES']);
        $TOTR12COGS = $masterdisplayarray[$counter]['TOTR12COGS'];
        $TOTR12SALES = $masterdisplayarray[$counter]['TOTR12SALES'];
        $TOTMNTBO = intval($masterdisplayarray[$counter]['TOTMNTBO']);
        $TOTQTRBO = intval($masterdisplayarray[$counter]['TOTQTRBO']);
        $TOTR12BO = intval($masterdisplayarray[$counter]['TOTR12BO']);
        $TOTMNTBE = intval($masterdisplayarray[$counter]['TOTMNTBE']);
        $TOTQTRBE = intval($masterdisplayarray[$counter]['TOTQTRBE']);
        $TOTR12BE = intval($masterdisplayarray[$counter]['TOTR12BE']);
        $TOTMNTD = intval($masterdisplayarray[$counter]['TOTMNTD']);
        $TOTQTRD = intval($masterdisplayarray[$counter]['TOTQTRD']);
        $TOTR12D = intval($masterdisplayarray[$counter]['TOTR12D']);
        $TOTMNTXD = intval($masterdisplayarray[$counter]['TOTMNTXD']);
        $TOTQTRXD = intval($masterdisplayarray[$counter]['TOTQTRXD']);
        $TOTR12XD = intval($masterdisplayarray[$counter]['TOTR12XD']);
        $TOTMNTXE = intval($masterdisplayarray[$counter]['TOTMNTXE']);
        $TOTQTRXE = intval($masterdisplayarray[$counter]['TOTQTRXE']);
        $TOTR12XE = intval($masterdisplayarray[$counter]['TOTR12XE']);
        $TOTMNTXS = intval($masterdisplayarray[$counter]['TOTMNTXS']);
        $TOTQTRXS = intval($masterdisplayarray[$counter]['TOTQTRXS']);
        $TOTR12XS = intval($masterdisplayarray[$counter]['TOTR12XS']);
        $BEFFRMNT = $masterdisplayarray[$counter]['BEFFRMNT'];
        $AFTFRMNT = $masterdisplayarray[$counter]['AFTFRMNT'];
        $BEFFRQTR = $masterdisplayarray[$counter]['BEFFRQTR'];
        $AFTFRQTR = $masterdisplayarray[$counter]['AFTFRQTR'];
        $BEFFRR12 = $masterdisplayarray[$counter]['BEFFRR12'];
        $AFTFRR12 = $masterdisplayarray[$counter]['AFTFRR12'];
        $SHIPACCPERCMNT = $masterdisplayarray[$counter]['SHIPACCPERCMNT'];
        $SHIPACCPERCQTR = $masterdisplayarray[$counter]['SHIPACCPERCQTR'];
        $SHIPACCPERCR12 = $masterdisplayarray[$counter]['SHIPACCPERCR12'];
        $DAMAGEACCPERCMNT = $masterdisplayarray[$counter]['DAMAGEACCPERCMNT'];
        $DAMAGEACCPERCQTR = $masterdisplayarray[$counter]['DAMAGEACCPERCQTR'];
        $DAMAGEACCPERCR12 = $masterdisplayarray[$counter]['DAMAGEACCPERCR12'];
        $ADDSCACCPERCMNT = $masterdisplayarray[$counter]['ADDSCACCPERCMNT'];
        $ADDSCACCPERCQTR = $masterdisplayarray[$counter]['ADDSCACCPERCQTR'];
        $ADDSCACCPERCR12 = $masterdisplayarray[$counter]['ADDSCACCPERCR12'];
        $MNTHOSC = $masterdisplayarray[$counter]['MNTHOSC'];
        $QTROSC = $masterdisplayarray[$counter]['QTROSC'];
        $R12OSC = $masterdisplayarray[$counter]['R12OSC'];
        $CUSTSCOREMNT = $masterdisplayarray[$counter]['CUSTSCOREMNT'];
        $CUSTSCOREQTR = $masterdisplayarray[$counter]['CUSTSCOREQTR'];
        $CUSTSCORER12 = $masterdisplayarray[$counter]['CUSTSCORER12'];
        $BEFFRMNT_EXCLDS = $masterdisplayarray[$counter]['BEFFRMNT_EXCLDS'];
        $AFTFRMNT_EXCLDS = $masterdisplayarray[$counter]['AFTFRMNT_EXCLDS'];
        $BEFFRQTR_EXCLDS = $masterdisplayarray[$counter]['BEFFRQTR_EXCLDS'];
        $AFTFRQTR_EXCLDS = $masterdisplayarray[$counter]['AFTFRQTR_EXCLDS'];
        $BEFFRR12_EXCLDS = $masterdisplayarray[$counter]['BEFFRR12_EXCLDS'];
        $AFTFRR12_EXCLDS = $masterdisplayarray[$counter]['AFTFRR12_EXCLDS'];
        $MNTHOSC_EXCLDS = $masterdisplayarray[$counter]['MNTHOSC_EXCLDS'];
        $QTROSC_EXCLDS = $masterdisplayarray[$counter]['QTROSC_EXCLDS'];
        $R12OSC_EXCLDS = $masterdisplayarray[$counter]['R12OSC_EXCLDS'];
        $CUSTSCOREMNT_EXCLDS = $masterdisplayarray[$counter]['CUSTSCOREMNT_EXCLDS'];
        $CUSTSCOREQTR_EXCLDS = $masterdisplayarray[$counter]['CUSTSCOREQTR_EXCLDS'];
        $CUSTSCORER12_EXCLDS = $masterdisplayarray[$counter]['CUSTSCORER12_EXCLDS'];
        $CUR_MNT_P_LINES = intval($masterdisplayarray[$counter]['CUR_MNT_P_LINES']);
        $CUR_QTR_P_LINES = intval($masterdisplayarray[$counter]['CUR_QTR_P_LINES']);
        $R12_P_LINES = intval($masterdisplayarray[$counter]['R12_P_LINES']);
        $CUR_MNT_P_FR = intval($masterdisplayarray[$counter]['CUR_MNT_P_FR']);
        $CUR_QTR_P_FR = intval($masterdisplayarray[$counter]['CUR_QTR_P_FR']);
        $R12_P_FR = intval($masterdisplayarray[$counter]['R12_P_FR']);
        $PBFRMNT = $masterdisplayarray[$counter]['PBFRMNT'];
        $PBFRQTR = $masterdisplayarray[$counter]['PBFRQTR'];
        $PBFRR12 = $masterdisplayarray[$counter]['PBFRR12'];
        $slope30day = $masterdisplayarray[$counter]['slope30day'];
        $slope90day = $masterdisplayarray[$counter]['slope90day'];
        $slope12mon = $masterdisplayarray[$counter]['slope12mon'];
        $slopelines30day = $masterdisplayarray[$counter]['slopelines30day'];
        $slopelines90day = $masterdisplayarray[$counter]['slopelines90day'];
        $slopelines12mon = $masterdisplayarray[$counter]['slopelines12mon'];
        $slopeBO30day = $masterdisplayarray[$counter]['slopeBO30day'];
        $slopeBO90day = $masterdisplayarray[$counter]['slopeBO90day'];
        $slopeBO12mon = $masterdisplayarray[$counter]['slopeBO12mon'];
        $slopeBE30day = $masterdisplayarray[$counter]['slopeBE30day'];
        $slopeBE90day = $masterdisplayarray[$counter]['slopeBE90day'];
        $slopeBE12mon = $masterdisplayarray[$counter]['slopeBE12mon'];
        $slopeD30day = $masterdisplayarray[$counter]['slopeD30day'];
        $slopeD90day = $masterdisplayarray[$counter]['slopeD90day'];
        $slopeD12mon = $masterdisplayarray[$counter]['slopeD12mon'];
        $slopeXD30day = $masterdisplayarray[$counter]['slopeXD30day'];
        $slopeXD90day = $masterdisplayarray[$counter]['slopeXD90day'];
        $slopeXD12mon = $masterdisplayarray[$counter]['slopeXD12mon'];
        $slopeXE30day = $masterdisplayarray[$counter]['slopeXE30day'];
        $slopeXE90day = $masterdisplayarray[$counter]['slopeXE90day'];
        $slopeXE12mon = $masterdisplayarray[$counter]['slopeXE12mon'];
        $slopeXS30day = $masterdisplayarray[$counter]['slopeXS30day'];
        $slopeXS90day = $masterdisplayarray[$counter]['slopeXS90day'];
        $slopeXS12mon = $masterdisplayarray[$counter]['slopeXS12mon'];
        $slopeBFFR30day = $masterdisplayarray[$counter]['slopeBFFR30day'];
        $slopeAFTFR30day = $masterdisplayarray[$counter]['slopeAFTFR30day'];
        $slopeBFFR90day = $masterdisplayarray[$counter]['slopeBFFR90day'];
        $slopeAFTFR90day = $masterdisplayarray[$counter]['slopeAFTFR90day'];
        $slopeBFFR12mon = $masterdisplayarray[$counter]['slopeBFFR12mon'];
        $slopeAFTFR12mon = $masterdisplayarray[$counter]['slopeAFTFR12mon'];
        $slopeSHIPACC30day = $masterdisplayarray[$counter]['slopeSHIPACC30day'];
        $slopeSHIPACC90day = $masterdisplayarray[$counter]['slopeSHIPACC90day'];
        $slopeSHIPACC12mon = $masterdisplayarray[$counter]['slopeSHIPACC12mon'];
        $slopeDMGACC30day = $masterdisplayarray[$counter]['slopeDMGACC30day'];
        $slopeDMGACC90day = $masterdisplayarray[$counter]['slopeDMGACC90day'];
        $slopeDMGACC12mon = $masterdisplayarray[$counter]['slopeDMGACC12mon'];
        $slopeADDSC30day = $masterdisplayarray[$counter]['slopeADDSC30day'];
        $slopeADDSC90day = $masterdisplayarray[$counter]['slopeADDSC90day'];
        $slopeADDSC12mon = $masterdisplayarray[$counter]['slopeADDSC12mon'];
        $slopeOSC30day = $masterdisplayarray[$counter]['slopeOSC30day'];
        $slopeOSC90day = $masterdisplayarray[$counter]['slopeOSC90day'];
        $slopeOSC12mon = $masterdisplayarray[$counter]['slopeOSC12mon'];
        $slope30day_exclds = $masterdisplayarray[$counter]['slope30day_exclds'];
        $slope90day_exclds = $masterdisplayarray[$counter]['slope90day_exclds'];
        $slope12mon_exclds = $masterdisplayarray[$counter]['slope12mon_exclds'];
        $slopeBFFR30day_exclds = $masterdisplayarray[$counter]['slopeBFFR30day_exclds'];
        $slopeAFTFR30day_exclds = $masterdisplayarray[$counter]['slopeAFTFR30day_exclds'];
        $slopeBFFR90day_exclds = $masterdisplayarray[$counter]['slopeBFFR90day_exclds'];
        $slopeAFTFR90day_exclds = $masterdisplayarray[$counter]['slopeAFTFR90day_exclds'];
        $slopeBFFR12mon_exclds = $masterdisplayarray[$counter]['slopeBFFR12mon_exclds'];
        $slopeAFTFR12mon_exclds = $masterdisplayarray[$counter]['slopeAFTFR12mon_exclds'];
        $SLOPEPBFRMNT = $masterdisplayarray[$counter]['SLOPEPBFRMNT'];
        $SLOPEPBFRQTR = $masterdisplayarray[$counter]['SLOPEPBFRQTR'];
        $SLOPEPBFRR12 = $masterdisplayarray[$counter]['SLOPEPBFRR12'];
        $slopeOSC30day_exclds = $masterdisplayarray[$counter]['slopeOSC30day_exclds'];
        $slopeOSC90day_exclds = $masterdisplayarray[$counter]['slopeOSC90day_exclds'];
        $slopeOSC12mon_exclds = $masterdisplayarray[$counter]['slopeOSC12mon_exclds'];

        //data for customerscoresbybilltomerge
        $data[] = "('$SALESPLAN',$TOTMONTHLINES,'$TOTMONTHCOGS','$TOTMONTHSALES',$TOTQTRLINES ,'$TOTQTRCOGS','$TOTQTRSALES',$TOTR12LINES,'$TOTR12COGS','$TOTR12SALES',$TOTMNTBO,$TOTQTRBO,$TOTR12BO,$TOTMNTBE,$TOTQTRBE,$TOTR12BE,$TOTMNTD,$TOTQTRD,$TOTR12D,$TOTMNTXD,$TOTQTRXD,$TOTR12XD,$TOTMNTXE,$TOTQTRXE,$TOTR12XE,$TOTMNTXS,$TOTQTRXS,$TOTR12XS,'$BEFFRMNT','$AFTFRMNT','$BEFFRQTR','$AFTFRQTR','$BEFFRR12','$AFTFRR12','$SHIPACCPERCMNT','$SHIPACCPERCQTR','$SHIPACCPERCR12','$DAMAGEACCPERCMNT','$DAMAGEACCPERCQTR','$DAMAGEACCPERCR12','$ADDSCACCPERCMNT','$ADDSCACCPERCQTR','$ADDSCACCPERCR12','$MNTHOSC','$QTROSC','$R12OSC','$CUSTSCOREMNT','$CUSTSCOREQTR','$CUSTSCORER12','$BEFFRMNT_EXCLDS','$AFTFRMNT_EXCLDS','$BEFFRQTR_EXCLDS','$AFTFRQTR_EXCLDS','$BEFFRR12_EXCLDS','$AFTFRR12_EXCLDS','$MNTHOSC_EXCLDS','$QTROSC_EXCLDS','$R12OSC_EXCLDS','$CUSTSCOREMNT_EXCLDS','$CUSTSCOREQTR_EXCLDS','$CUSTSCORER12_EXCLDS',$CUR_MNT_P_LINES,$CUR_QTR_P_LINES,$R12_P_LINES,$CUR_MNT_P_FR,$CUR_QTR_P_FR,$R12_P_FR,'$PBFRMNT','$PBFRQTR','$PBFRR12')";







//data for custscoresbyday
        $data2[] = "('$SALESPLAN','$RECORDDATE','$CUSTSCOREMNT','$CUSTSCOREQTR','$CUSTSCORER12','$slope30day','$slope90day','$slope12mon',$TOTMONTHLINES,
   $TOTQTRLINES,$TOTR12LINES,'$slopelines30day','$slopelines90day','$slopelines12mon',$TOTMNTBO,$TOTQTRBO,$TOTR12BO,'$slopeBO30day',
   '$slopeBO90day','$slopeBO12mon',$TOTMNTBE,$TOTQTRBE,$TOTR12BE,'$slopeBE30day','$slopeBE90day','$slopeBE12mon',$TOTMNTD,$TOTQTRD,$TOTR12D,
   '$slopeD30day','$slopeD90day','$slopeD12mon',$TOTMNTXD,$TOTQTRXD,$TOTR12XD,'$slopeXD30day','$slopeXD90day','$slopeXD12mon',$TOTMNTXE,
   $TOTQTRXE,$TOTR12XE,'$slopeXE30day','$slopeXE90day','$slopeXE12mon',$TOTMNTXS,$TOTQTRXS,$TOTR12XS,'$slopeXS30day','$slopeXS90day',
   '$slopeXS12mon','$BEFFRMNT','$AFTFRMNT','$BEFFRQTR','$AFTFRQTR','$BEFFRR12','$AFTFRR12','$slopeBFFR30day','$slopeAFTFR30day','$slopeBFFR90day','$slopeAFTFR90day',
   '$slopeBFFR12mon','$slopeAFTFR12mon','$SHIPACCPERCMNT','$SHIPACCPERCQTR','$SHIPACCPERCR12','$slopeSHIPACC30day','$slopeSHIPACC90day','$slopeSHIPACC12mon',
    '$DAMAGEACCPERCMNT','$DAMAGEACCPERCQTR','$DAMAGEACCPERCR12','$slopeDMGACC30day','$slopeDMGACC90day','$slopeDMGACC12mon','$ADDSCACCPERCMNT','$ADDSCACCPERCQTR',
    '$ADDSCACCPERCR12','$slopeADDSC30day','$slopeADDSC90day','$slopeADDSC12mon','$MNTHOSC','$QTROSC','$R12OSC','$slopeOSC30day','$slopeOSC90day','$slopeOSC12mon','$CUSTSCOREMNT_EXCLDS',
   '$CUSTSCOREQTR_EXCLDS','$CUSTSCORER12_EXCLDS','$slope30day_exclds','$slope90day_exclds','$slope12mon_exclds','$BEFFRMNT_EXCLDS','$AFTFRMNT_EXCLDS','$BEFFRQTR_EXCLDS','$AFTFRQTR_EXCLDS',
    '$BEFFRR12_EXCLDS','$AFTFRR12_EXCLDS','$slopeBFFR30day_exclds','$slopeAFTFR30day_exclds','$slopeBFFR90day_exclds','$slopeAFTFR90day_exclds','$slopeBFFR12mon_exclds','$slopeAFTFR12mon_exclds',
   '$MNTHOSC_EXCLDS','$QTROSC_EXCLDS','$R12OSC_EXCLDS','$slopeOSC30day_exclds','$slopeOSC90day_exclds','$slopeOSC12mon_exclds','$PBFRMNT',
    '$PBFRQTR','$PBFRR12','$SLOPEPBFRMNT','$SLOPEPBFRQTR','$SLOPEPBFRR12')";

        $counter +=1;
    }


    $values = implode(',', $data);
    if (empty($values)) {
        break;
    }
    
    $values2 = implode(',', $data2);
    if (empty($values2)) {
        break;
    }
    
    
    $sql = "INSERT IGNORE INTO slotting.customerscores_salesplan_merge ($columns) VALUES $values";
    $query = $conn1->prepare($sql);
    $query->execute();
    
    $sql2 = "INSERT IGNORE INTO slotting.custscoresbyday_salesplan ($columns2) VALUES $values2";
    $query2 = $conn1->prepare($sql2);
    $query2->execute();
    $maxrange +=1000;
} while ($counter <= $rowcount);


$sqldelete2 = "TRUNCATE TABLE customerscores_salesplan";
$querydelete2 = $conn1->prepare($sqldelete2);
$querydelete2->execute();


$sqlmerge = "INSERT INTO customerscores_salesplan () SELECT * FROM customerscores_salesplan_merge;";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();

$sqldelete4 = "TRUNCATE TABLE scorecard_display_salesplan";
$querydelete4 = $conn1->prepare($sqldelete4);
$querydelete4->execute();

$sqlmerge2 = "INSERT INTO scorecard_display_salesplan (SALESPLAN,RECORDDATE,SCOREMONTH,SCOREQUARTER,SCOREROLL12,SLOPE30DAY,SLOPE90DAY,SLOPE12MON,LINESMONTH,LINESQUARTER,LINESROLL12,SLOPELINES30DAY,SLOPELINES90DAY,SLOPELINES12MON,BOMONTH,BOQUARTER,BOROLL12,SLOPEBO30DAY,SLOPEBO90DAY,SLOPEBO12MON,BEMONTH,BEQUARTER,BEROLL12,SLOPEBE30DAY,SLOPEBE90DAY,SLOPEBE12MON,DMONTH,DQUARTER,DROLL12,SLOPED30DAY,SLOPED90DAY,SLOPED12MON,XDMONTH,XDQUARTER,XDROLL12,SLOPEXD30DAY,SLOPEXD90DAY,SLOPEXD12MON,XEMONTH,XEQUARTER,XEROLL12,SLOPEXE30DAY,SLOPEXE90DAY,SLOPEXE12MON,XSMONTH,XSQUARTER,XSROLL12,SLOPEXS30DAY,SLOPEXS90DAY,SLOPEXS12MON,BEFFRMNT,AFTFRMNT,BEFFRQTR,AFTFRQTR,BEFFRR12,AFTFRR12,SLOPEBEFFRMNT,SLOPEAFTFRMNT,SLOPEBEFFRQTR,SLOPEAFTFRQTR,SLOPEAFTFRR12,SLOPEBEFFRR12,SHIPACCMONTH,SHIPACCQUARTER,SHIPACCROLL12,SLOPESHIPACCMONTH,SLOPESHIPACCQUARTER,SLOPESHIPACCROLL12,DMGACCMONTH,DMGACCQUARTER,DMGACCROLL12,SLOPEDMGACCMONTH,SLOPEDMGACCQUARTER,SLOPEDMGACCROLL12,ADDSCACCMONTH,ADDSCACCQUARTER,ADDSCACCROLL12,SLOPEADDSCACCMONTH,SLOPEADDSCACCQUARTER,SLOPEADDSCACCROLL12,OSCMONTH,OSCQUARTER,OSCROLL12, SLOPEOSCMONTH, SLOPEOSCQUARTER, SLOPEOSCROLL12, SCOREMONTH_EXCLDS, SCOREQUARTER_EXCLDS, SCOREROLL12_EXCLDS, SLOPE30DAY_EXCLDS, SLOPE90DAY_EXCLDS, SLOPE12MON_EXCLDS, BEFFRMNT_EXCLDS, AFTFRMNT_EXCLDS, BEFFRQTR_EXCLDS, AFTFRQTR_EXCLDS, BEFFRR12_EXCLDS, AFTFRR12_EXCLDS, SLOPEBEFFRMNT_EXCLDS, SLOPEAFTFRMNT_EXCLDS, SLOPEBEFFRQTR_EXCLDS, SLOPEAFTFRQTR_EXCLDS, SLOPEAFTFRR12_EXCLDS, SLOPEBEFFRR12_EXCLDS, OSCMONTH_EXCLDS, OSCQUARTER_EXCLDS, OSCROLL12_EXCLDS, SLOPEOSCMONTH_EXCLDS, SLOPEOSCQUARTER_EXCLDS, SLOPEOSCROLL12_EXCLDS, PBFRMNT,PBFRQTR,  PBFRR12, SLOPEPBFRMNT, SLOPEPBFRQTR, SLOPEPBFRR12, TOTMONTHCOGS, TOTMONTHSALES, TOTQTRCOGS, TOTQTRSALES, TOTR12COGS, TOTR12SALES, CUR_MNT_P_LINES, CUR_QTR_P_LINES, R12_P_LINES)

SELECT 
    SLOPE . *, TOTMONTHCOGS, TOTMONTHSALES, TOTQTRCOGS, TOTQTRSALES, TOTR12COGS, TOTR12SALES, CUR_MNT_P_LINES, CUR_QTR_P_LINES, R12_P_LINES 
FROM
    slotting.customerscores_salesplan as SCORES
        inner JOIN
    (SELECT 
        t1 . *
    FROM
        slotting.custscoresbyday_salesplan t1
    WHERE
        t1.RECORDDATE = (SELECT 
                MAX(t2.RECORDDATE)
            FROM
                slotting.custscoresbyday_salesplan t2
            WHERE
                t2.SALESPLAN = t1.SALESPLAN
            HAVING count(t2.SALESPLAN) >= 1)) as SLOPE ON (SCORES.SALESPLAN = SLOPE.SALESPLAN)";
$querymerge2 = $conn1->prepare($sqlmerge2);
$querymerge2->execute();

//Add average scores to summary table.
$sqlmerge3 = "INSERT IGNORE INTO scoreavg_salesplan
SELECT RECORDDATE, avg(scoremonth), avg(scorequarter), avg(scoreroll12) FROM slotting.custscoresbyday_salesplan WHERE RECORDDATE  >= DATE_ADD(CURDATE(), INTERVAL -5 DAY) GROUP BY RECORDDATE ;";
$querymerge3 = $conn1->prepare($sqlmerge3);
$querymerge3->execute();