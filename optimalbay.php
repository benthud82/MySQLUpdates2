<?php

if (isset($whssel)) {
    $whsefilter = ' and LOWHSE = ' . $whssel;
} else {
    $whsefilter = '';
}

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
include_once '../globalincludes/nahsi_mysql.php';
//include_once '../globalincludes/ustxgpslotting_mysql.php';
include_once '../globalincludes/usa_asys.php';
include_once '../globalincludes/newcanada_asys.php';
include_once '../globalfunctions/slottingfunctions.php';


if (isset($whssel)) {
    $sqldelete = "DELETE FROM slotting.optimalbay WHERE Whse = $whssel";
} else {
    $sqldelete = "TRUNCATE TABLE slotting.optimalbay";
}
$querydelete = $conn1->prepare($sqldelete);
$querydelete->execute();

$whsearray = array(2, 3, 6, 7, 9, 11, 12, 16);
//$whsearray = array(7);

foreach ($whsearray as $whse) {

    if ($whse == 11 || $whse == 12 || $whse == 16) {
        $useconn = $aseriesconn_can;
        $useschema = 'ARCPCORDTA';
    } else {
        $useconn = $aseriesconn;
        $useschema = 'HSIPCORDTA';
    }



//cube by bay per whse
    $baycube = $useconn->prepare("SELECT substring(LMLOC#,4,2) as BAY, sum(LMVOL9) as BAYVOL
                                  FROM $useschema.NPFLSM 
                                  WHERE LMWHSE = $whse and LMTIER = 'L04' and substring(LMLOC#,4,2) not in ('99','ZZ', 'OT')
                                  GROUP BY substring(LMLOC#,4,2) 
                                  ORDER BY substring(LMLOC#,4,2)");
    $baycube->execute();
    $baycubearray = $baycube->fetchAll(pdo::FETCH_ASSOC);

//    if ($whse == 7) { //endcap opportunity
//        $baycubearray[0]['BAYVOL'] += 38016;
//        $baycubearray[0]['BAYVOL'] += 1175040;
//        $baycubearray[1]['BAYVOL'] += 532224;
//        $baycubearray[2]['BAYVOL'] += 533376;
//        $baycubearray[3]['BAYVOL'] += 517248;
//        $baycubearray[4]['BAYVOL'] += 612864;
//    } 




//Result set for PPC sorted by highest PPC for items currently in L04
    $ppc = $useconn->prepare("SELECT VCWHSE, 
                                     VCITEM,
                                     VCPKGU,
                                     VCLOC#,
                                     VCADBS,
                                     VCCSLS,
                                     VCCUBE,
                                     VCFTIR,
                                     VCTTIR,
                                     VCNGD5,
                                     VCNDEP,
                                     PICK_QTY_MN,
                                     case
                                        when AVGD_BTW_SLE >= 365 then 0
                                        when DAYS_FRM_SLE >= 180 then 0
                                        when PICK_QTY_MN > SHIP_QTY_MN then SHIP_QTY_MN / AVGD_BTW_SLE
                                        when AVGD_BTW_SLE = 0 and DAYS_FRM_SLE = 0 then PICK_QTY_MN
                                        when AVGD_BTW_SLE = 0 then (PICK_QTY_MN / DAYS_FRM_SLE)
                                        else (PICK_QTY_MN / AVGD_BTW_SLE)
                                    end as DAILYPICKS,
                                    cast(substring(VCNGD5,1,2) as INTEGER) * cast(substring(VCNGD5,4,2) as INTEGER) * VCNDEP as NEWGRIDVOL,
                                    (case when AVGD_BTW_SLE >= 365 then 0
                                               when DAYS_FRM_SLE >= 180 then 0
                                               when PICK_QTY_MN > SHIP_QTY_MN then SHIP_QTY_MN / AVGD_BTW_SLE 
                                               when AVGD_BTW_SLE = 0 and DAYS_FRM_SLE = 0 then PICK_QTY_MN  
                                               when AVGD_BTW_SLE = 0 then (PICK_QTY_MN / DAYS_FRM_SLE)    
                                               else (PICK_QTY_MN / AVGD_BTW_SLE)                          
                                        end) / (cast(substring(VCNGD5, 1, 2) as INTEGER) * cast(substring(VCNGD5, 4, 2) as INTEGER) * VCNDEP) * 1000 as PPC_CALC 
                                FROM $useschema.NPFMVC, $useschema.NPTSLD 
                                WHERE WAREHOUSE = VCWHSE and 
                                      VCITEM = ITEM_NUMBER and  
                                      VCPKGU = PACKAGE_UNIT and 
                                      substring(PACKAGE_TYPE,1,1) = VCCSLS
                                      and VCWHSE = $whse
                                      and VCFTIR in('L04','L02','L06','L16')
                                      and VCADBS > 0
                                      and VCNDEP > 0
                                      and cast(substring(VCNGD5,4,2) as INTEGER)  > 0
                                      and cast(substring(VCNGD5,1,2) as INTEGER)  > 0
                                ORDER BY (case when AVGD_BTW_SLE >= 365 then 0
                                               when DAYS_FRM_SLE >= 180 then 0
                                               when PICK_QTY_MN > SHIP_QTY_MN then SHIP_QTY_MN / AVGD_BTW_SLE 
                                               when AVGD_BTW_SLE = 0 and DAYS_FRM_SLE = 0 then PICK_QTY_MN  
                                               when AVGD_BTW_SLE = 0 then (PICK_QTY_MN / DAYS_FRM_SLE)    
                                               else (PICK_QTY_MN / AVGD_BTW_SLE)                          
                                        end) / (cast(substring(VCNGD5, 1, 2) as INTEGER) * cast(substring(VCNGD5, 4, 2) as INTEGER) * VCNDEP) DESC");
    $ppc->execute();
    $ppcarray = $ppc->fetchAll(pdo::FETCH_ASSOC);

    $columns = 'OPT_WHSE, OPT_ITEM, OPT_PKGU, OPT_LOC, OPT_ADBS, OPT_CSLS, OPT_CUBE, OPT_CURTIER, OPT_TOTIER, OPT_NEWGRID, OPT_NDEP, OPT_AVGPICK, OPT_DAILYPICKS, OPT_NEWGRIDVOL, OPT_PPCCALC, OPT_OPTBAY, OPT_CURRBAY, OPT_CURRDAILYFT, OPT_SHLDDAILYFT, OPT_ADDTLFTPERPICK, OPT_ADDTLFTPERDAY, OPT_WALKCOST';


    $values = array();

    $maxrange = 3999;
    $counter = 0;
    $rowcount = count($ppcarray);
    $newgrid_runningvol = 0;
    $baykey = 0;
    $maxbaykey = count($baycubearray) - 1;
    $baytotalvolume = intval($baycubearray[$baykey]['BAYVOL']);

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }

        $data = array();
        $values = array();
        while ($counter <= $maxrange) {
            $OPT_TOTIER = $ppcarray[$counter]['VCTTIR'];
            if ($OPT_TOTIER === 'L02') {
                $OPT_WHSE = intval($ppcarray[$counter]['VCWHSE']);
                $OPT_ITEM = intval($ppcarray[$counter]['VCITEM']);
                $OPT_PKGU = intval($ppcarray[$counter]['VCPKGU']);
                $OPT_LOC = $ppcarray[$counter]['VCLOC#'];
                $OPT_ADBS = intval($ppcarray[$counter]['VCADBS']);
                $OPT_CSLS = $ppcarray[$counter]['VCCSLS'];
                $OPT_CUBE = intval($ppcarray[$counter]['VCCUBE']);
                $OPT_CURTIER = $ppcarray[$counter]['VCFTIR'];
                $OPT_NEWGRID = $ppcarray[$counter]['VCNGD5'];
                $OPT_NDEP = intval($ppcarray[$counter]['VCNDEP']);
                $OPT_AVGPICK = intval($ppcarray[$counter]['PICK_QTY_MN']);
                $OPT_DAILYPICKS = $ppcarray[$counter]['DAILYPICKS'];
                $OPT_NEWGRIDVOL = intval($ppcarray[$counter]['NEWGRIDVOL']);
                $OPT_PPCCALC = $ppcarray[$counter]['PPC_CALC'];
                $OPT_CURRBAY = intval(substr($OPT_LOC, 3, 2));
                $OPT_OPTBAY = intval(0);
                $walkcostarray = _walkcost($OPT_CURRBAY, $OPT_OPTBAY, $OPT_DAILYPICKS);
                $OPT_CURRDAILYFT = intval($walkcostarray['CURR_FT_PER_DAY']);
                $OPT_SHLDDAILYFT = intval($walkcostarray['SHOULD_FT_PER_DAY']);
                $OPT_ADDTLFTPERPICK = intval($walkcostarray['ADDTL_FT_PER_PICK']);
                $OPT_ADDTLFTPERDAY = intval($walkcostarray['ADDTL_FT_PER_DAY']);
                $OPT_WALKCOST = $walkcostarray['ADDTL_COST_PER_YEAR'];
                $data[] = "($OPT_WHSE, $OPT_ITEM, $OPT_PKGU, '$OPT_LOC', $OPT_ADBS, '$OPT_CSLS', $OPT_CUBE, '$OPT_CURTIER', '$OPT_TOTIER', '$OPT_NEWGRID', $OPT_NDEP, $OPT_AVGPICK, $OPT_DAILYPICKS, $OPT_NEWGRIDVOL, $OPT_PPCCALC, $OPT_OPTBAY, $OPT_CURRBAY, $OPT_CURRDAILYFT, $OPT_SHLDDAILYFT, $OPT_ADDTLFTPERPICK, $OPT_ADDTLFTPERDAY, $OPT_WALKCOST)";
                $counter +=1;
            } else {
                $OPT_WHSE = intval($ppcarray[$counter]['VCWHSE']);
                $OPT_ITEM = intval($ppcarray[$counter]['VCITEM']);
                $OPT_PKGU = intval($ppcarray[$counter]['VCPKGU']);
                $OPT_LOC = $ppcarray[$counter]['VCLOC#'];
                $OPT_ADBS = intval($ppcarray[$counter]['VCADBS']);
                $OPT_CSLS = $ppcarray[$counter]['VCCSLS'];
                $OPT_CUBE = intval($ppcarray[$counter]['VCCUBE']);
                $OPT_CURTIER = $ppcarray[$counter]['VCFTIR'];
                $OPT_NEWGRID = $ppcarray[$counter]['VCNGD5'];
                $OPT_NDEP = intval($ppcarray[$counter]['VCNDEP']);
                $OPT_AVGPICK = intval($ppcarray[$counter]['PICK_QTY_MN']);
                $OPT_DAILYPICKS = $ppcarray[$counter]['DAILYPICKS'];
                $OPT_NEWGRIDVOL = intval($ppcarray[$counter]['NEWGRIDVOL']);
                $OPT_PPCCALC = $ppcarray[$counter]['PPC_CALC'];
                $OPT_CURRBAY = intval(substr($OPT_LOC, 3, 2));

                $newgrid_runningvol += $OPT_NEWGRIDVOL; //add newgrid vol to running total of newgrid vol

                if ($newgrid_runningvol <= $baytotalvolume) {  //can next item volume fit into current available room?
                    $OPT_OPTBAY = intval($baycubearray[$baykey]['BAY']);
                    $walkcostarray = _walkcost($OPT_CURRBAY, $OPT_OPTBAY, $OPT_DAILYPICKS);
                    $OPT_CURRDAILYFT = intval($walkcostarray['CURR_FT_PER_DAY']);
                    $OPT_SHLDDAILYFT = intval($walkcostarray['SHOULD_FT_PER_DAY']);
                    $OPT_ADDTLFTPERPICK = intval($walkcostarray['ADDTL_FT_PER_PICK']);
                    $OPT_ADDTLFTPERDAY = intval($walkcostarray['ADDTL_FT_PER_DAY']);
                    $OPT_WALKCOST = $walkcostarray['ADDTL_COST_PER_YEAR'];
                    $data[] = "($OPT_WHSE, $OPT_ITEM, $OPT_PKGU, '$OPT_LOC', $OPT_ADBS, '$OPT_CSLS', $OPT_CUBE, '$OPT_CURTIER', '$OPT_TOTIER', '$OPT_NEWGRID', $OPT_NDEP, $OPT_AVGPICK, $OPT_DAILYPICKS, $OPT_NEWGRIDVOL, $OPT_PPCCALC, $OPT_OPTBAY, $OPT_CURRBAY, $OPT_CURRDAILYFT, $OPT_SHLDDAILYFT, $OPT_ADDTLFTPERPICK, $OPT_ADDTLFTPERDAY, $OPT_WALKCOST)";
                    $counter +=1;
                } else { //item cannot fit.  Increase bay key and reset
                    if ($baykey < $maxbaykey) {
                        $baykey += 1; //add one to baykey to proceed to next bay
                    }
                    $newgrid_runningvol = $OPT_NEWGRIDVOL; //reset running total for new grid vol
                    $baytotalvolume = intval($baycubearray[$baykey]['BAYVOL']); //reset available bay volume for next bay
                    $OPT_OPTBAY = intval($baycubearray[$baykey]['BAY']);
                    $walkcostarray = _walkcost($OPT_CURRBAY, $OPT_OPTBAY, $OPT_DAILYPICKS);
                    $OPT_CURRDAILYFT = intval($walkcostarray['CURR_FT_PER_DAY']);
                    $OPT_SHLDDAILYFT = intval($walkcostarray['SHOULD_FT_PER_DAY']);
                    $OPT_ADDTLFTPERPICK = intval($walkcostarray['ADDTL_FT_PER_PICK']);
                    $OPT_ADDTLFTPERDAY = intval($walkcostarray['ADDTL_FT_PER_DAY']);
                    $OPT_WALKCOST = $walkcostarray['ADDTL_COST_PER_YEAR'];
                    $data[] = "($OPT_WHSE, $OPT_ITEM, $OPT_PKGU, '$OPT_LOC', $OPT_ADBS, '$OPT_CSLS', $OPT_CUBE, '$OPT_CURTIER', '$OPT_TOTIER', '$OPT_NEWGRID', $OPT_NDEP, $OPT_AVGPICK, $OPT_DAILYPICKS, $OPT_NEWGRIDVOL, $OPT_PPCCALC, $OPT_OPTBAY, $OPT_CURRBAY, $OPT_CURRDAILYFT, $OPT_SHLDDAILYFT, $OPT_ADDTLFTPERPICK, $OPT_ADDTLFTPERDAY, $OPT_WALKCOST)";
                    $counter +=1;
                }
            }
        }

        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.optimalbay ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=4000;
    } while ($counter <= $rowcount);
}

//update history table

$sql_hist = "INSERT IGNORE INTO slotting.optimalbay_hist(optbayhist_whse, optbayhist_tier, optbayhist_date, optbayhist_bay, optbayhist_pick, optbayhist_cost, optbayhist_count)
                 SELECT OPT_WHSE, OPT_CURTIER, CURDATE(), substring(OPT_LOC,1,5) as BAY, sum(OPT_DAILYPICKS), avg(ABS(OPT_WALKCOST)), count(OPT_ITEM) FROM slotting.optimalbay GROUP BY OPT_WHSE, OPT_CURTIER, CURDATE(), substring(OPT_LOC,1,5);";
$query_hist = $conn1->prepare($sql_hist);
$query_hist->execute();

