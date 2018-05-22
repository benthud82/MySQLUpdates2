<?php

//sql statement for daily pick.  This is used for all off system slotting daily pick calculations and sorts

$sql_dailypick = 'case
                    when  A.AVGD_BTW_SLE >= 365 then 0
                    when  A.DAYS_FRM_SLE >= 180 then 0
                    when  A.PICK_QTY_MN >  A.SHIP_QTY_MN then ( A.SHIP_QTY_MN / (case when X.CPCCPKU > 0 then X.CPCCPKU else 1 end)) /  A.AVGD_BTW_SLE
                    when  A.AVGD_BTW_SLE = 0 and  A.DAYS_FRM_SLE = 0 then  A.PICK_QTY_MN
                    when  A.AVGD_BTW_SLE = 0 then ( A.PICK_QTY_MN /  A.DAYS_FRM_SLE)
                    else ( A.PICK_QTY_MN /  A.AVGD_BTW_SLE)
                  end';

$sql_dailyunit = 'case
                when  A.AVGD_BTW_SLE >= 365 then 0
                when  A.DAYS_FRM_SLE >= 180 then 0
                when  A.PICK_QTY_MN >  A.SHIP_QTY_MN then  A.SHIP_QTY_MN /  A.AVGD_BTW_SLE
                when
                     A.AVGD_BTW_SLE = 0
                        and  A.DAYS_FRM_SLE = 0
                then
                     A.SHIP_QTY_MN
                when  A.AVGD_BTW_SLE = 0 then ( A.SHIP_QTY_MN /  A.DAYS_FRM_SLE)
                else ( A.SHIP_QTY_MN /  A.AVGD_BTW_SLE)
            end';