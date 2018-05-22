<script src="../jquery-1.4.4.min.js" type="text/javascript"></script>

<script>
    //Set the interval function to refresh a set time period
//    setInterval(function () {
//        //set header data to refresh every 120 seconds (120,000 ms)
//        //Call ajax request to refresh totedata
//        refreshprintedcasedata();
//    }, 45000);



    function runagain() {
        var firstrun = 0;
        refreshprintedcasedata(firstrun);
    }


    function refreshprintedcasedata(firstrun) {
        var firstrun = firstrun;
        $.ajax({
            data: {firstrun: firstrun},
            url: 'totedata.php',
            type: 'POST',
            dataType: 'html',
            success: function (ajaxresult) {
                runagain();
            }

        });
    }


    $(document).ready(function () {
        var firstrun = 1;
        refreshprintedcasedata(firstrun);
    });
</script>