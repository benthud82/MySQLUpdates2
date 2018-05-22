<?php

//Slotting MBO 2016 - Pick to Belt recommendations.
//Updates table ptb_recs
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
if (isset($var_whse)) {
    $USAarray = array($var_whse);
} else {
    $USAarray = array(2, 3, 6, 7, 9);
//    $USAarray = array(7);
}

include_once '../globalincludes/usa_asys.php';
include_once '../globalincludes/nahsi_mysql.php';
set_time_limit(99999);
$today = date("Y-m-d H:i:s");


foreach ($USAarray as $whse) {

    $sqldelete = "DELETE FROM slotting.ptb_recs WHERE PTB_WHSE = $whse";
    $querydelete = $conn1->prepare($sqldelete);
    $querydelete->execute();
    include '../globaldata/ptb_location_count.php'; //pull tier count by whse for C01 and C02
    $ptb_location_count = $array_tiercount[0]['TIER_COUNT']; //assign max number of location available for PTB and bulk pallet locations.  Recommended items cannot exceed this number.

    include '../globaldata/ptb_rec_data.php'; //pull the case pick data by whse

    $columns = 'PTB_WHSE, PTB_ITEM, PTB_PKGU, PTB_LOC, PTB_CUBE, PTB_AVGQ, PTB_AVGP, PTB_PVEL, PTB_CLAS, PTB_ADBS, PTB_DSLS, PTB_CURT, PTB_RANK, PTB_DATE';

    if (count($array_ptb_incl_data) > 0) {  //if DC has items on the inclusion table, add with rank of 0
        $values = array();
        $data = array();
        foreach ($array_ptb_incl_data as $key => $value) {
            $PTB_WHSE = $array_ptb_incl_data[$key]['VCWHSE'];
            $PTB_ITEM = $array_ptb_incl_data[$key]['VCITEM'];
            $PTB_PKGU = $array_ptb_incl_data[$key]['VCPKGU'];
            $PTB_LOC = $array_ptb_incl_data[$key]['VCLOC#'];
            $PTB_CUBE = $array_ptb_incl_data[$key]['VCCUBE'];
            $PTB_AVGQ = $array_ptb_incl_data[$key]['SHIP_QTY_MN'];
            $PTB_AVGP = $array_ptb_incl_data[$key]['PICK_QTY_MN'];
            $PTB_PVEL = $array_ptb_incl_data[$key]['PICK_VEL'];
            $PTB_CLAS = $array_ptb_incl_data[$key]['VCCLAS'];
            $PTB_ADBS = $array_ptb_incl_data[$key]['VCADBS'];
            $PTB_DSLS = $array_ptb_incl_data[$key]['VCDSLS'];
            $PTB_CURT = $array_ptb_incl_data[$key]['CURR_TIER'];
            $PTB_RANK = 0;
            $PTB_DATE = $today;

            $data[] = "($PTB_WHSE, $PTB_ITEM, $PTB_PKGU, '$PTB_LOC', $PTB_CUBE, $PTB_AVGQ, $PTB_AVGP, $PTB_PVEL, '$PTB_CLAS', $PTB_ADBS, $PTB_DSLS, '$PTB_CURT', $PTB_RANK, '$PTB_DATE')";
        }

        $values = implode(',', $data);
        $sql = "INSERT IGNORE INTO slotting.ptb_recs ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
    }

    $values = array();

    $maxrange = 4999;
    $counter = 0;
    $rowcount = count($array_ptb_recs);

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }
        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table
            $PTB_WHSE = $array_ptb_recs[$counter]['VCWHSE'];
            $PTB_ITEM = $array_ptb_recs[$counter]['VCITEM'];
            $PTB_PKGU = $array_ptb_recs[$counter]['VCPKGU'];
            $PTB_LOC = $array_ptb_recs[$counter]['VCLOC#'];
            $PTB_CUBE = $array_ptb_recs[$counter]['VCCUBE'];
            $PTB_AVGQ = $array_ptb_recs[$counter]['SHIP_QTY_MN'];
            $PTB_AVGP = $array_ptb_recs[$counter]['PICK_QTY_MN'];
            $PTB_PVEL = $array_ptb_recs[$counter]['PICK_VEL'];
            $PTB_CLAS = $array_ptb_recs[$counter]['VCCLAS'];
            $PTB_ADBS = $array_ptb_recs[$counter]['VCADBS'];
            $PTB_DSLS = $array_ptb_recs[$counter]['VCDSLS'];
            $PTB_CURT = $array_ptb_recs[$counter]['CURR_TIER'];
            $PTB_RANK = $counter + 1;
            $PTB_DATE = $today;

            $data[] = "($PTB_WHSE, $PTB_ITEM, $PTB_PKGU, '$PTB_LOC', $PTB_CUBE, $PTB_AVGQ, $PTB_AVGP, $PTB_PVEL, '$PTB_CLAS', $PTB_ADBS, $PTB_DSLS, '$PTB_CURT', $PTB_RANK, '$PTB_DATE')";
            $counter +=1;
        } //end of $array_ptb_recs foreach loop

        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.ptb_recs ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=5000;
    } while ($counter <= $rowcount);

    //update percentage tracking table "ptb_tracking"
    include '../globaldata/ptb_rec_tracking.php';
    $correct_count = $array_ptb_perc[0]['count_corr'];
    $perc_correct = $correct_count / $ptb_location_count;

    $sql2 = "INSERT INTO slotting.ptb_tracking (PTB_TRACK_WHSE,PTB_TRACK_DATE, PTB_TRACK_COUNT, PTB_TRACK_CORRECT, PTB_TRACK_PERC) VALUES ($whse, '$PTB_DATE', $ptb_location_count, $correct_count, $perc_correct) ON DUPLICATE KEY UPDATE PTB_TRACK_COUNT = $ptb_location_count, PTB_TRACK_CORRECT = $correct_count, PTB_TRACK_PERC = $perc_correct";
    $query2 = $conn1->prepare($sql2);
    $query2->execute();
} //end of whse array foreach loop
//write new records to ptb_recs_history table
$sqlmerge = "INSERT IGNORE INTO ptb_recs_history (PTB_WHSE, PTB_ITEM, PTB_PKGU, PTB_LOC, PTB_CUBE, PTB_AVGQ, PTB_AVGP, PTB_PVEL, PTB_CLAS, PTB_ADBS, PTB_DSLS, PTB_CURT, PTB_RANK, PTB_DATE)
SELECT * FROM ptb_recs;";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();
