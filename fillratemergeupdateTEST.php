
<?php

//determine the number of returns per invoice by ship to.  This does not provide detail.  Need to create another file to show indivudal detail.

set_time_limit(99999);
include '../globalincludes/nahsi_mysql.php';
include '../globalincludes/usa_asys.php';
include '../globalfunctions/custdbfunctions.php';
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
//Find the first day of current month fiscal
$current_month_start_fiscal = _currentmonthfiscal();  //call current month function to find start for for current month for sql
//Find the first day of current quarter fiscal
$current_quarter_start_fiscal = _currentquarterfiscal();  //call current quarter function to find start for for current quarter for sql
//Find first day for rolling 12 month 1yyddd
$rolling_12_start_fiscal = _rolling12startfiscal();  //call rolling start function to find start date for rolling 12 month sql

//Find the first day of current month 1yyddd
$current_month_start_yyddd = _currentmonthyyddd();  //call current month function to find start for for current month for sql
//Find the first day of current quarter 1yyddd
$current_quarter_start_yyddd = _currentquarteryyddd();  //call current quarter function to find start for for current quarter for sql
//Find first day for rolling 12 month 1yyddd
$rolling_12_start_yyddd = _rolling12startyyddd();  //call rolling start function to find start date for rolling 12 month sql

$sqldelete = "TRUNCATE TABLE fillratemerge";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$largecust = $aseriesconn->prepare("SELECT PBAN8 FROM HSIPCORDTA.NOTWPS, HSIPCORDTA.IM0018 WHERE PBAN8 = BILL_TO and PBSHAN = CUSTOMER GROUP BY PBAN8 HAVING (sum(case when PBSHJD >= $current_month_start_yyddd then PBBXVS else 0 end) >= 10000 or sum(case when PBSHJD >= $rolling_12_start_yyddd then PBBXVS else 0 end) >= 120000)");
$largecust->execute();
$largecustarray = $largecust->fetchAll(pdo::FETCH_NUM);

//Pull in fill rate issues by type for current month, quarter and rolling 12

$fillrateissues = $aseriesconn->prepare("SELECT 
    BILL_TO,
    CUSTOMER,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_BO,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_BO,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_BO,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_BE,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_BE,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_BE,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_D,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_D,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_D,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_XD,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_XD,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_XD,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_XE,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_XE,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_XE,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and substr(TR_DATE,1,6) >= '" . $current_month_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_MNT_XS,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and substr(TR_DATE,1,6) >= '" . $current_quarter_start_fiscal . "'
        then
            1
        else 0
    end) as CUR_QTR_XS,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and substr(TR_DATE,1,6) >= '" . $rolling_12_start_fiscal . "'
        then
            1
        else 0
    end) as R12_XS
FROM
    A.HSIPCORDTA.IM0011
GROUP BY BILL_TO , CUSTOMER");
$fillrateissues->execute();
//$fillrateissuesarray = $fillrateissues->fetchAll(pdo::FETCH_NUM);


foreach ($fillrateissues as $key => $value) {
    $arraykeyindex = _searchForKey($fillrateissues[$key][0], $largecustarray, 0);
    if (isset($arraykeyindex)) {
        $BILLTO = $fillrateissues[$key][0];
        $SHIPTO = $fillrateissues[$key][1];
        $CUR_MNT_BO = $fillrateissues[$key][2];
        $CUR_QTR_BO = $fillrateissues[$key][3];
        $R12_BO = $fillrateissues[$key][4];
        $CUR_MNT_BE = $fillrateissues[$key][5];
        $CUR_QTR_BE = $fillrateissues[$key][6];
        $R12_BE = $fillrateissues[$key][7];
        $CUR_MNT_D = $fillrateissues[$key][8];
        $CUR_QTR_D = $fillrateissues[$key][9];
        $R12_D = $fillrateissues[$key][10];
        $CUR_MNT_XD = $fillrateissues[$key][11];
        $CUR_QTR_XD = $fillrateissues[$key][12];
        $R12_XD = $fillrateissues[$key][13];
        $CUR_MNT_XE = $fillrateissues[$key][14];
        $CUR_QTR_XE = $fillrateissues[$key][15];
        $R12_XE = $fillrateissues[$key][16];
        $CUR_MNT_XS = $fillrateissues[$key][17];
        $CUR_QTR_XS = $fillrateissues[$key][18];
        $R12_XS = $fillrateissues[$key][19];




        $sql = "INSERT INTO fillratemerge (BILLTO, SHIPTO, CUR_MNT_BO, CUR_QTR_BO, R12_BO, CUR_MNT_BE, CUR_QTR_BE, R12_BE, CUR_MNT_D, CUR_QTR_D, R12_D, CUR_MNT_XD, CUR_QTR_XD, R12_XD, CUR_MNT_XE, CUR_QTR_XE, R12_XE, CUR_MNT_XS, CUR_QTR_XS, R12_XS) VALUES (:BILLTO, :SHIPTO, :CUR_MNT_BO, :CUR_QTR_BO, :R12_BO, :CUR_MNT_BE, :CUR_QTR_BE, :R12_BE, :CUR_MNT_D, :CUR_QTR_D, :R12_D, :CUR_MNT_XD, :CUR_QTR_XD, :R12_XD, :CUR_MNT_XE, :CUR_QTR_XE, :R12_XE, :CUR_MNT_XS, :CUR_QTR_XS, :R12_XS)";
        $query = $conn1->prepare($sql);
        $query->execute(array(':BILLTO' => $BILLTO, ':SHIPTO' => $SHIPTO, ':CUR_MNT_BO' => $CUR_MNT_BO, ':CUR_QTR_BO' => $CUR_QTR_BO, ':R12_BO' => $R12_BO, ':CUR_MNT_BE' => $CUR_MNT_BE, ':CUR_QTR_BE' => $CUR_QTR_BE, ':R12_BE' => $R12_BE, ':CUR_MNT_D' => $CUR_MNT_D, ':CUR_QTR_D' => $CUR_QTR_D, ':R12_D' => $R12_D, ':CUR_MNT_XD' => $CUR_MNT_XD, ':CUR_QTR_XD' => $CUR_QTR_XD, ':R12_XD' => $R12_XD, ':CUR_MNT_XE' => $CUR_MNT_XE, ':CUR_QTR_XE' => $CUR_QTR_XE, ':R12_XE' => $R12_XE, ':CUR_MNT_XS' => $CUR_MNT_XS, ':CUR_QTR_XS' => $CUR_QTR_XS, ':R12_XS' => $R12_XS));
    }
}

$sqldelete1 = "TRUNCATE TABLE fillratebyshipto";
$querydelete1 = $conn1->prepare($sqldelete1);
$querydelete1->execute();


$sqlmerge = "INSERT INTO fillratebyshipto (BILLTO, SHIPTO, CUR_MNT_BO, CUR_QTR_BO, R12_BO, CUR_MNT_BE, CUR_QTR_BE, R12_BE, CUR_MNT_D, CUR_QTR_D, R12_D, CUR_MNT_XD, CUR_QTR_XD, R12_XD, CUR_MNT_XE, CUR_QTR_XE, R12_XE, CUR_MNT_XS, CUR_QTR_XS, R12_XS)
SELECT * FROM fillratemerge;";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();









