<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_unit = "";
        if (isset($_REQUEST['unit'])){$p_unit = $_REQUEST['unit'];}
        $p_housenumber = "";
        if (isset($_REQUEST['housenumber'])){$p_housenumber = $_REQUEST['housenumber'];}
        $p_streetname = "";
        if (isset($_REQUEST['streetname'])){$p_streetname = $_REQUEST['streetname'];}
        $p_streettype = "";
        if (isset($_REQUEST['streettype'])){$p_streettype = $_REQUEST['streettype'];}
        $p_locality = "";
        if (isset($_REQUEST['locality'])){$p_locality = $_REQUEST['locality'];}
        $p_postcode = "";
        if (isset($_REQUEST['postcode'])){$p_postcode = $_REQUEST['postcode'];}

        // Array of parameters
        $p_arr = array('unit'=>$p_unit,'housenumber'=>$p_housenumber,'streetname'=>$p_streetname,'streettype'=>$p_streettype,'locality'=>$p_locality,'postcode'=>$p_postcode);

        // Build a proper web service request based on the parameters
        $full_url = "http://tools.energyandresources.vic.gov.au/energyapi/energytest1.php"."?". http_build_query($p_arr,'', '&');

        // Download the web service response
        $file_in_a_string = file_get_contents($full_url);

        // Extraction of the distributor information based on the pattern
        // <div class="msg_distributor">Your distribution business is: <strong><a href="#contacts">XXXXXXXXX</a></strong></div>
        if (strpos($file_in_a_string,'Error') !== false)
        {
            $distributor = 'ERROR';
        }
        else
        {
            $distributor = substr($file_in_a_string,strpos($file_in_a_string,"contacts")+10,strpos($file_in_a_string,"</a>")-strpos($file_in_a_string,"contacts")-10);
        }

        // Output preparation
        $p_arr['distributor']=$distributor;
        $output = json_encode($p_arr);

        // no-cache (important for mobile safari)
        header('cache-control: no-cache');

        // json/jsonp support
        if (isset($_REQUEST['callback'])) {
            // Result content type
            header('content-type: application/javascript');  
            $output = $_REQUEST['callback'] . '(' . $output . ');';
        } 
        else
        {
            // Result content type
            header('content-type: application/json');            
        }
        echo $output;
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
