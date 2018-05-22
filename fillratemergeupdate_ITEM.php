
<?php

//determine the number of returns per invoice by ship to.  This does not provide detail.  Need to create another file to show indivudal detail.

set_time_limit(99999);
//include '../globalincludes/nahsi_mysql.php';
include '../globalincludes/ustxgpslotting_mysql.php';
include '../globalincludes/usa_asys.php';
include '../globalfunctions/custdbfunctions.php';
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');

//Find the first day of current month 1yyddd
$roll_month_start_yyyymmdd = _rollmonthyyyymmdd();  //call current month function to find start for for current month for sql
//Find the first day of current quarter 1yyddd
$roll_quarter_start_yyyymmdd = _rollqtryyyymmdd();  //call current quarter function to find start for for current quarter for sql
//Find first day for rolling 12 month 1yyddd
$rolling_12_start_yyyymmdd = _roll12yyyymmdd();  //call rolling start function to find start date for rolling 12 month sql

$current_month_start_yyddd = _rollmonthyyddd();
$rolling_12_start_yyddd = _rolling12startyyddd();

$sqldelete = "TRUNCATE TABLE slotting.frissue_billto_shipto";
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$largecust = $aseriesconn->prepare("SELECT PBAN8 FROM HSIPCORDTA.NOTWPS, HSIPCORDTA.IM0018 WHERE PBAN8 = BILL_TO and PBSHAN = CUSTOMER GROUP BY PBAN8 HAVING (sum(case when PBSHJD >= $current_month_start_yyddd then PBBXVS else 0 end) >= 5000 or sum(case when PBSHJD >= $rolling_12_start_yyddd then PBBXVS else 0 end) >= 60000)");
$largecust->execute();
$largecustarray = $largecust->fetchAll(pdo::FETCH_NUM);

//Pull in fill rate issues by type for current month, quarter and rolling 12

$fillrateissues = $aseriesconn->prepare("SELECT 
    BILL_TO,
    CUSTOMER,
    ITEM,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_BO,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_BO,
    sum(case
        when
            IP_FIL_TYP = 'BO'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_BO,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_BE,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_BE,
    sum(case
        when
            IP_FIL_TYP = 'BE'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_BE,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_D,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_D,
    sum(case
        when
            IP_FIL_TYP = 'D'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_D,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_XD,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_XD,
    sum(case
        when
            IP_FIL_TYP = 'XD'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_XD,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_XE,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_XE,
    sum(case
        when
            IP_FIL_TYP = 'XE'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_XE,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_XS,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_XS,
    sum(case
        when
            IP_FIL_TYP = 'XS'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_XS,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_MNT_P_LINES,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
        then
            1
        else 0
    end) as CUR_QTR_P_LINES,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
        then
            1
        else 0
    end) as R12_P_LINES,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $roll_month_start_yyyymmdd . "'
                and IP_FIL_TYP <> ' ' and IP_FIL_TYP <> 'BE'
        then
            1
        else 0
    end) as CUR_MNT_P_FR,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $roll_quarter_start_yyyymmdd . "'
               and IP_FIL_TYP <> ' ' and IP_FIL_TYP <> 'BE'
        then
            1
        else 0
    end) as CUR_QTR_P_FR,
    sum(case
        when
            IM_BRN_TYP = 'P'
                and TR_DATE >= '" . $rolling_12_start_yyyymmdd . "'
                and IP_FIL_TYP <> ' ' and IP_FIL_TYP <> 'BE'
        then
            1
        else 0
    end) as R12_P_FR
FROM
    A.HSIPCORDTA.IM0011
WHERE BILL_TO in (SELECT PBAN8 FROM HSIPCORDTA.NOTWPS, HSIPCORDTA.IM0018 WHERE PBAN8 = BILL_TO and PBSHAN = CUSTOMER GROUP BY PBAN8 HAVING (sum(case when PBSHJD >= $current_month_start_yyddd then PBBXVS else 0 end) >= 5000 or sum(case when PBSHJD >= $rolling_12_start_yyddd then PBBXVS else 0 end) >= 60000))
GROUP BY BILL_TO , CUSTOMER, ITEM");
$fillrateissues->execute();
$fillrateissuesarray = $fillrateissues->fetchAll(pdo::FETCH_NUM);


foreach ($fillrateissuesarray as $key => $value) {
    $arraykeyindex = _searchForKey($fillrateissuesarray[$key][0], $largecustarray, 0);
    if (isset($arraykeyindex)) {
        $BILLTO = $fillrateissuesarray[$key][0];
        $SHIPTO = $fillrateissuesarray[$key][1];
        $CUR_MNT_BO = $fillrateissuesarray[$key][2];
        $CUR_QTR_BO = $fillrateissuesarray[$key][3];
        $R12_BO = $fillrateissuesarray[$key][4];
        $CUR_MNT_BE = $fillrateissuesarray[$key][5];
        $CUR_QTR_BE = $fillrateissuesarray[$key][6];
        $R12_BE = $fillrateissuesarray[$key][7];
        $CUR_MNT_D = $fillrateissuesarray[$key][8];
        $CUR_QTR_D = $fillrateissuesarray[$key][9];
        $R12_D = $fillrateissuesarray[$key][10];
        $CUR_MNT_XD = $fillrateissuesarray[$key][11];
        $CUR_QTR_XD = $fillrateissuesarray[$key][12];
        $R12_XD = $fillrateissuesarray[$key][13];
        $CUR_MNT_XE = $fillrateissuesarray[$key][14];
        $CUR_QTR_XE = $fillrateissuesarray[$key][15];
        $R12_XE = $fillrateissuesarray[$key][16];
        $CUR_MNT_XS = $fillrateissuesarray[$key][17];
        $CUR_QTR_XS = $fillrateissuesarray[$key][18];
        $R12_XS = $fillrateissuesarray[$key][19];
        $CUR_MNT_P_LINES = $fillrateissuesarray[$key][20];
        $CUR_QTR_P_LINES = $fillrateissuesarray[$key][21];
        $R12_P_LINES = $fillrateissuesarray[$key][22];
        $CUR_MNT_P_FR = $fillrateissuesarray[$key][23];
        $CUR_QTR_P_FR = $fillrateissuesarray[$key][24];
        $R12_P_FR = $fillrateissuesarray[$key][25];




        $sql = "INSERT INTO fillratemerge (BILLTO, SHIPTO, CUR_MNT_BO, CUR_QTR_BO, R12_BO, CUR_MNT_BE, CUR_QTR_BE, R12_BE, CUR_MNT_D, CUR_QTR_D, R12_D, CUR_MNT_XD, CUR_QTR_XD, R12_XD, CUR_MNT_XE, CUR_QTR_XE, R12_XE, CUR_MNT_XS, CUR_QTR_XS, R12_XS, CUR_MNT_P_LINES, CUR_QTR_P_LINES, R12_P_LINES, CUR_MNT_P_FR, CUR_QTR_P_FR, R12_P_FR) VALUES (:BILLTO, :SHIPTO, :CUR_MNT_BO, :CUR_QTR_BO, :R12_BO, :CUR_MNT_BE, :CUR_QTR_BE, :R12_BE, :CUR_MNT_D, :CUR_QTR_D, :R12_D, :CUR_MNT_XD, :CUR_QTR_XD, :R12_XD, :CUR_MNT_XE, :CUR_QTR_XE, :R12_XE, :CUR_MNT_XS, :CUR_QTR_XS, :R12_XS, :CUR_MNT_P_LINES, :CUR_QTR_P_LINES, :R12_P_LINES, :CUR_MNT_P_FR, :CUR_QTR_P_FR, :R12_P_FR)";
        $query = $conn1->prepare($sql);
        $query->execute(array(':BILLTO' => $BILLTO, ':SHIPTO' => $SHIPTO, ':CUR_MNT_BO' => $CUR_MNT_BO, ':CUR_QTR_BO' => $CUR_QTR_BO, ':R12_BO' => $R12_BO, ':CUR_MNT_BE' => $CUR_MNT_BE, ':CUR_QTR_BE' => $CUR_QTR_BE, ':R12_BE' => $R12_BE, ':CUR_MNT_D' => $CUR_MNT_D, ':CUR_QTR_D' => $CUR_QTR_D, ':R12_D' => $R12_D, ':CUR_MNT_XD' => $CUR_MNT_XD, ':CUR_QTR_XD' => $CUR_QTR_XD, ':R12_XD' => $R12_XD, ':CUR_MNT_XE' => $CUR_MNT_XE, ':CUR_QTR_XE' => $CUR_QTR_XE, ':R12_XE' => $R12_XE, ':CUR_MNT_XS' => $CUR_MNT_XS, ':CUR_QTR_XS' => $CUR_QTR_XS, ':R12_XS' => $R12_XS, ':CUR_MNT_P_LINES' => $CUR_MNT_P_LINES, ':CUR_QTR_P_LINES' => $CUR_QTR_P_LINES, ':R12_P_LINES' => $R12_P_LINES, ':CUR_MNT_P_FR' => $CUR_MNT_P_FR, ':CUR_QTR_P_FR' => $CUR_QTR_P_FR, ':R12_P_FR' => $R12_P_FR));
        unset($fillrateissuesarray[$key]);
    }
    unset($fillrateissuesarray[$key]);
}

$sqldelete1 = "TRUNCATE TABLE fillratebyshipto";
$querydelete1 = $conn1->prepare($sqldelete1);
$querydelete1->execute();


$sqlmerge = "INSERT INTO fillratebyshipto (BILLTO, SHIPTO, CUR_MNT_BO, CUR_QTR_BO, R12_BO, CUR_MNT_BE, CUR_QTR_BE, R12_BE, CUR_MNT_D, CUR_QTR_D, R12_D, CUR_MNT_XD, CUR_QTR_XD, R12_XD, CUR_MNT_XE, CUR_QTR_XE, R12_XE, CUR_MNT_XS, CUR_QTR_XS, R12_XS, CUR_MNT_P_LINES, CUR_QTR_P_LINES, R12_P_LINES, CUR_MNT_P_FR, CUR_QTR_P_FR, R12_P_FR)
SELECT * FROM fillratemerge;";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();