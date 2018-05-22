<?php

if (isset($var_whse)) {
    $whsefilter = ' and LOWHSE = ' . $var_whse;
} else {
    $whsefilter = '';
}

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
include_once '../globalincludes/nahsi_mysql.php';
include_once '../globalincludes/usa_asys.php';
include_once '../globalincludes/newcanada_asys.php';
include_once '../globalfunctions/slottingfunctions.php';

//********GLOBAL VARIABLES***********
$DAYS_TO_STOCK = 10;  //Should this be changed for "C" movers to 1-2 ship occurences?
$DAMPEN_PERCENT = 1;
$MOVES_PER_HOUR = 10;
$HOURLY_RATE = 19;
$DAYS_IN_YEAR = 253;
$row = array();
//********END OF GLOBAL VARIABLES*********

if (isset($var_whse)) {
    $sqldelete = "DELETE FROM slotting.slottingcost WHERE Whse = $var_whse";
} else {
    $sqldelete = "TRUNCATE TABLE slotting.slottingcost";
}
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$whsearray = array(2, 3, 6, 7, 9, 11, 12, 16, 22);
//$whsearray = array(22);

foreach ($whsearray as $whse) {

    if ($whse == 11 || $whse == 12 || $whse == 16) {
        $useconn = $aseriesconn_can;
        $useschema = 'ARCPCORDTA';
    } else {
        $useconn = $aseriesconn;
        $useschema = 'HSIPCORDTA';
    }


    $result1 = $useconn->prepare("SELECT VCWHSE,
                                         VCITEM, 
                                         VCLOC#,
                                         VCPKGU,
                                         AVGD_BTW_SLE as VCADBS,
                                         VCDSLS,
                                         LOMINC, 
                                         VCFTIR, 
                                         VCTTIR,
                                         VCGRD5,
                                         LMDEEP,
                                         VCFIXA,
                                         VCMAXC, 
                                         SHIP_QTY_MN, 
                                         PICK_QTY_MN,
                                         SLOT_QTY,
                                         VCCLAS, 
                                         VCCTRF, 
                                         VCNDMD, 
                                         VCNGD5,
                                         VCNDEP,
                                         VCNTRF,
                                         LOMINC / SHIP_QTY_MN as MINDAYS,
                                         VCMAXC / SHIP_QTY_MN as MAXDAYS
                                  FROM $useschema.NPFLOC, 
                                       $useschema.NPFLSM, 
                                       $useschema.NPTSLD, 
                                       $useschema.NPFMVC 
                                  WHERE LOLOC# = LMLOC# 
                                       AND LOITEM = LMITEM 
                                       AND LOWHSE = LMWHSE 
                                       AND WAREHOUSE = LOWHSE 
                                       AND LOLOC# = CUR_LOCATION 
                                       AND ITEM_NUMBER = LOITEM 
                                       and LMITEM = VCITEM 
                                       and LOLOC# = VCLOC# 
                                       and VCWHSE = LOWHSE 
                                       and VCWHSE = $whse 
                                       and LOPRIM = 'P'
                                       and LMITEM between '1000000' and '9999999'
                                       and VCLOC# not like 'Q%'
                                       and AVGD_BTW_SLE > 0
                                       and SHIP_QTY_MN > 0 $whsefilter");
    $result1->execute();
    $mindaysarray = $result1->fetchAll(pdo::FETCH_ASSOC);


    $columns = 'Whse, Item, Location, Pkgu, ADBS, DSLS, Curr_Min, Curr_Tier, To_Tier, Curr_Grid5, Curr_Depth, Fixt_MC, Curr_Max, Avg_Ship_Qty, Avg_Pick_Qty, Slot_Qty, Item_MC, Curr_TF, New_Dmd, New_Grid5, New_Dep, New_TF, Min_Days, Max_Days, Loc_Ship_Days, Imp_Dly_Moves, Accpt_Dly_Moves, Addtl_Dly_Moves, Yearly_Est_Cost';


    $values = array();

    $maxrange = 3999;
    $counter = 0;
    $rowcount = count($mindaysarray);

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table
            $LOWHSE = $mindaysarray[$counter]['VCWHSE'];
            $LOITEM = $mindaysarray[$counter]['VCITEM'];
            $LOLOC = $mindaysarray[$counter]['VCLOC#'];
            $VCPKGU = $mindaysarray[$counter]['VCPKGU'];
            $VCADBS = $mindaysarray[$counter]['VCADBS'];
            $VCDSLS = $mindaysarray[$counter]['VCDSLS'];
            $LOMINC = $mindaysarray[$counter]['LOMINC'];
            $LMTIER = $mindaysarray[$counter]['VCFTIR'];
            $VCTTIR = $mindaysarray[$counter]['VCTTIR'];
            $LMGRD5 = $mindaysarray[$counter]['VCGRD5'];
            $LMDEEP = $mindaysarray[$counter]['LMDEEP'];
            $LMFIXA = $mindaysarray[$counter]['VCFIXA'];
            $LOMAXC = $mindaysarray[$counter]['VCMAXC'];
            $SHIP_QTY_MN = $mindaysarray[$counter]['SHIP_QTY_MN'];
            $PICK_QTY_MN = $mindaysarray[$counter]['PICK_QTY_MN'];
            $SLOT_QTY = $mindaysarray[$counter]['SLOT_QTY'];
            $VCCLAS = $mindaysarray[$counter]['VCCLAS'];
            $VCCTRF = $mindaysarray[$counter]['VCCTRF'];
            $VCNDMD = $mindaysarray[$counter]['VCNDMD'];
            $VCNGD5 = $mindaysarray[$counter]['VCNGD5'];
            $VCNDEP = $mindaysarray[$counter]['VCNDEP'];
            $VCNTRF = $mindaysarray[$counter]['VCNTRF'];
            $MINDAYS = str_replace(',', '', number_format($mindaysarray[$counter]['MINDAYS'], 2));
            $MAXDAYS = str_replace(',', '', number_format($mindaysarray[$counter]['MAXDAYS'], 2));

            $replen_cost_return_array = _slotting_replen_cost($LOMAXC, $VCCTRF, $LOMINC, $SHIP_QTY_MN, $VCADBS);
            $Loc_Ship_Days = str_replace(',', '', $replen_cost_return_array['LOC_DMD_DAYS']);
            $Imp_Dly_Moves = $replen_cost_return_array['IMP_MOVES_DAILY'];
            $Accpt_Dly_Moves = $replen_cost_return_array['ACCEPTABLE_MOVES_DAILY'];
            $Addtl_Dly_Moves = $replen_cost_return_array['ADDITIONAL_DAILY_MOVES'];
            $Yearly_Est_Cost = str_replace(',', '', $replen_cost_return_array['YEARLY_REPLEN_COST']);

            $data[] = "($LOWHSE, $LOITEM, '$LOLOC', $VCPKGU, $VCADBS, $VCDSLS, $LOMINC, '$LMTIER', '$VCTTIR', '$LMGRD5', $LMDEEP, '$LMFIXA', $LOMAXC, $SHIP_QTY_MN, $PICK_QTY_MN, $SLOT_QTY, '$VCCLAS', $VCCTRF, $VCNDMD, '$VCNGD5', $VCNDEP, $VCNTRF, $MINDAYS, $MAXDAYS, $Loc_Ship_Days, $Imp_Dly_Moves, $Accpt_Dly_Moves, $Addtl_Dly_Moves, $Yearly_Est_Cost)";
            $counter +=1;
        }


        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.slottingcost ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=4000;
    } while ($counter <= $rowcount);
}
//update history table

$sql_hist = "INSERT IGNORE INTO slottingcost_hist(costhist_whse, costhist_tier, costhist_date, costhist_bay, costhist_cost, costhist_count)
                 SELECT Whse, Curr_Tier, CURDATE(), substring(Location,1,5) as BAY, avg(ABS(Yearly_Est_Cost)), count(Item) FROM slotting.slottingcost GROUP BY Whse, Curr_Tier,CURDATE(),substring(Location,1,5);";
$query_hist = $conn1->prepare($sql_hist);
$query_hist->execute();

