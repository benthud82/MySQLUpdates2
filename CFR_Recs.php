<?php

//Slotting MBO 2016 - Pick to Belt recommendations.
//Updates table cfr_recs
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

    $sqldelete = "DELETE FROM slotting.cfr_recs WHERE CFR_WHSE = $whse";
    $querydelete = $conn1->prepare($sqldelete);
    $querydelete->execute();
    include '../globaldata/cfr_location_count.php'; //pull tier count by whse for L02
    $cfr_location_count = $array_tiercount[0]['TIER_COUNT']; //assign max number of location available for cfr locations.  Recommended items cannot exceed this number.

    include '../globaldata/cfr_rec_data.php'; //pull the case pick data by whse

    $columns = 'CFR_WHSE, CFR_ITEM, CFR_PKGU, CFR_LOC, CFR_CUBE, CFR_AVGQ, CFR_AVGP, CFR_CVEL, CFR_CLAS, CFR_ADBS, CFR_DSLS, CFR_CURT, CFR_NEWMAX, CFR_NEWGRD5, CFR_RANK, CFR_DATE';


    if (count($array_cfr_incl_data) > 0) {  //if DC has items on the inclusion table, add with rank of 0
        $values = array();
        $data = array();
        foreach ($array_cfr_incl_data as $key => $value) {
            $CFR_WHSE = $array_cfr_incl_data[$key]['VCWHSE'];
            $CFR_ITEM = $array_cfr_incl_data[$key]['VCITEM'];
            $CFR_PKGU = $array_cfr_incl_data[$key]['VCPKGU'];
            $CFR_LOC = $array_cfr_incl_data[$key]['VCLOC#'];
            $CFR_CUBE = $array_cfr_incl_data[$key]['VCCUBE'];
            $CFR_AVGQ = $array_cfr_incl_data[$key]['SHIP_QTY_MN'];
            $CFR_AVGP = $array_cfr_incl_data[$key]['PICK_QTY_MN'];
            $CFR_CVEL = $array_cfr_incl_data[$key]['CUBE_VEL'];
            $CFR_CLAS = $array_cfr_incl_data[$key]['VCCLAS'];
            $CFR_ADBS = $array_cfr_incl_data[$key]['VCADBS'];
            $CFR_DSLS = $array_cfr_incl_data[$key]['VCDSLS'];
            $CFR_CURT = $array_cfr_incl_data[$key]['CURR_TIER'];
            $CFR_NEWMAX = $array_cfr_incl_data[$key]['VCNDMD'];
            $CFR_NEWGRD5 = $array_cfr_incl_data[$key]['VCNGD5'];
            $CFR_RANK = 0;
            $CFR_DATE = $today;

            $data[] = "($CFR_WHSE, $CFR_ITEM, $CFR_PKGU, '$CFR_LOC', $CFR_CUBE, $CFR_AVGQ, $CFR_AVGP, $CFR_CVEL, '$CFR_CLAS', $CFR_ADBS, $CFR_DSLS, '$CFR_CURT', $CFR_NEWMAX, '$CFR_NEWGRD5', $CFR_RANK, '$CFR_DATE')";
        }

        $values = implode(',', $data);
        $sql = "INSERT IGNORE INTO slotting.cfr_recs ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
    }




    $values = array();

    $maxrange = 4999;
    $counter = 0;
    $rowcount = count($array_cfr_recs);

    do {
        if ($maxrange > $rowcount) {  //prevent undefined offset
            $maxrange = $rowcount - 1;
        }
        $data = array();
        $values = array();
        while ($counter <= $maxrange) { //split into 5,000 lines segments to insert into merge table
            $CFR_WHSE = $array_cfr_recs[$counter]['VCWHSE'];
            $CFR_ITEM = $array_cfr_recs[$counter]['VCITEM'];
            $CFR_PKGU = $array_cfr_recs[$counter]['VCPKGU'];
            $CFR_LOC = $array_cfr_recs[$counter]['VCLOC#'];
            $CFR_CUBE = $array_cfr_recs[$counter]['VCCUBE'];
            $CFR_AVGQ = $array_cfr_recs[$counter]['SHIP_QTY_MN'];
            $CFR_AVGP = $array_cfr_recs[$counter]['PICK_QTY_MN'];
            $CFR_CVEL = $array_cfr_recs[$counter]['CUBE_VEL'];
            $CFR_CLAS = $array_cfr_recs[$counter]['VCCLAS'];
            $CFR_ADBS = $array_cfr_recs[$counter]['VCADBS'];
            $CFR_DSLS = $array_cfr_recs[$counter]['VCDSLS'];
            $CFR_CURT = $array_cfr_recs[$counter]['CURR_TIER'];
            $CFR_NEWMAX = $array_cfr_recs[$counter]['VCNDMD'];
            $CFR_NEWGRD5 = $array_cfr_recs[$counter]['VCNGD5'];
            $CFR_RANK = $counter + 1;
            $CFR_DATE = $today;

            $data[] = "($CFR_WHSE, $CFR_ITEM, $CFR_PKGU, '$CFR_LOC', $CFR_CUBE, $CFR_AVGQ, $CFR_AVGP, $CFR_CVEL, '$CFR_CLAS', $CFR_ADBS, $CFR_DSLS, '$CFR_CURT', $CFR_NEWMAX, '$CFR_NEWGRD5', $CFR_RANK, '$CFR_DATE')";
            $counter +=1;
        } //end of $array_cfr_recs foreach loop

        $values = implode(',', $data);

        if (empty($values)) {
            break;
        }
        $sql = "INSERT IGNORE INTO slotting.cfr_recs ($columns) VALUES $values";
        $query = $conn1->prepare($sql);
        $query->execute();
        $maxrange +=5000;
    } while ($counter <= $rowcount);

    //update percentage tracking table "cfr_tracking"
    include '../globaldata/cfr_rec_tracking.php';
    $correct_count = $array_cfr_perc[0]['count_corr'];
    $perc_correct = $correct_count / $cfr_location_count;

    $sql2 = "INSERT INTO slotting.cfr_tracking (CFR_TRACK_WHSE,CFR_TRACK_DATE, CFR_TRACK_COUNT, CFR_TRACK_CORRECT, CFR_TRACK_PERC) VALUES ($whse, '$CFR_DATE', $cfr_location_count, $correct_count, $perc_correct) ON DUPLICATE KEY UPDATE CFR_TRACK_COUNT = $cfr_location_count, CFR_TRACK_CORRECT = $correct_count, CFR_TRACK_PERC = $perc_correct";
    $query2 = $conn1->prepare($sql2);
    $query2->execute();
} //end of whse array foreach loop
//write new records to cfr_recs_history table
$sqlmerge = "INSERT IGNORE INTO cfr_recs_history (CFR_WHSE, CFR_ITEM, CFR_PKGU, CFR_LOC, CFR_CUBE, CFR_AVGQ, CFR_AVGP, CFR_CVEL, CFR_CLAS, CFR_ADBS, CFR_DSLS, CFR_CURT, CFR_NEWMAX, CFR_NEWGRD5, CFR_RANK, CFR_DATE)
SELECT * FROM cfr_recs;";
$querymerge = $conn1->prepare($sqlmerge);
$querymerge->execute();
