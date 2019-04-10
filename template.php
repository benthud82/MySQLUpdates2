<?php

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
//put in connection includes (as400 printvis)


$result1 = $aseriesconn->prepare("");
$result1->execute();
$mindaysarray = $result1->fetchAll(pdo::FETCH_ASSOC);

//create table on local
$columns = 'dsl2whs, dsl2item, dsl2pkgu, dsl2csls';

//***KEEP**
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
        
        //STOPKEEP
        $dsl2whs = $mindaysarray[$counter][''];
        $dsl2item = $mindaysarray[$counter][''];

        $data[] = "($LMWHSE, $LMITEM, $LMPKGU, '$LMCSLS')";
        $counter +=1;
    }


    $values = implode(',', $data);

    if (empty($values)) {
        break;
    }
    $sql = "INSERT IGNORE INTO slotting.dsl2locs ($columns) VALUES $values";
    $query = $conn1->prepare($sql);
    $query->execute();
    $maxrange +=4000;
} while ($counter <= $rowcount);

