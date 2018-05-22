<?php

//estimate the PO delivery date by item
//Start at 5 key and traverse down to 2 key
//Run openpo_update first to ensure most up to date data

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');
ini_set('max_allowed_packet', 999999999);
include_once '../globalincludes/nahsi_mysql.php';  //conn1
//include '../../globalincludes/usa_asys.php';  //conn1
//include '../../globalincludes/ustxgpslotting_mysql.php';  //conn1
include_once 'globalfunctions.php';
//$whse = 7;

date_default_timezone_set('America/New_York');
$today = date("Y-m-d H:i:s");

//Global variables
$minocc = 2;
$date_format = 'Y-m-d';


//Pull history to see if any items have been urfed and putaway.  Determine accuracy of estimate.  
//Would the estimated date have been late?  Pull last 8 days of putaways and compare actual URF to estimated URF.

$estimateerror = $conn1->prepare("INSERT ignore INTO estimate_error
                                   (URFVEND_EST,
                                    URFVNAD_EST,
                                    URFCARR_EST,
                                    URFTODC_EST,
                                    URFPONM_EST,
                                    URFRECN_EST,
                                    URFDCIN_EST,
                                    URFTMST_EST,
                                    PUTITEM_EST,
                                    PUTTMST_EST,
                                    PODATE_EST,
                                    AVGURFDATE_EST,
                                    MAXURFDATE_EST,
                                    GROUPUSEDWCS_EST,
                                    AVGEDIDATE_EST,
                                    MAXEDIDATE_EST,
                                    GROUPUSEDEDI_EST,
                                    UPDATEDATETIME_EST) 
                                   SELECT 
                                       URFVEND,
                                       URFVNAD,
                                       URFCARR,
                                       URFTODC,
                                       URFPONM,
                                       URFRECN,
                                       URFDCIN,
                                       URFTMST,
                                       PUTITEM,
                                       PUTTMST,
                                       PODATE,
                                       AVGURFDATE,
                                       MAXURFDATE,
                                       GROUPUSEDWCS,
                                       AVGEDIDATE,
                                       MAXEDIDATE,
                                       GROUPUSEDEDI,
                                       UPDATEDATETIME
                                   FROM
                                       urfdate,
                                       putdate,
                                       urfdate_est_hist
                                   where
                                        PUTTMST BETWEEN NOW() - INTERVAL 5 DAY AND NOW()
                                                   and OPENPONUM = URFPONM
                                           and OPENITEM = PUTITEM
                                           and URFPONM = PUTPONM
                                           and URFRECN = PUTRECN
                                           and URFDCIN = PUTDCIN
                                           and exists( select 
                                               *
                                           from
                                               urfdate_est_hist
                                           where
                                               OPENPONUM = URFPONM
                                                   and OPENITEM = PUTITEM)");

$estimateerror->execute();

