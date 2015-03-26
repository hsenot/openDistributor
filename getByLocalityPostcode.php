<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_locality = "PRESTON";
        if (isset($_REQUEST['locality'])){$p_locality = $_REQUEST['locality'];}
        $p_postcode = "3072";
        if (isset($_REQUEST['postcode'])){$p_postcode = $_REQUEST['postcode'];}

        // Array of parameters
        $p_arr = array('suburb'=>$p_locality,'postcode'=>$p_postcode,'serviceType'=>'EDA','_'=>date('U'));

        // Build a proper web service request based on the parameters
        $full_url = "http://www.agl.com.au/svc/LookupServiceArea/GetDistributorResults"."?". http_build_query($p_arr,'', '&');

        // Download the web service response
        $file_in_a_string = file_get_contents($full_url);

        // Extraction of the distributor information based on the pattern
        // <div class="msg_distributor">Your distribution business is: <strong><a href="#contacts">XXXXXXXXX</a></strong></div>
        if (strpos($file_in_a_string,'you are not in one of our serviced areas') !== false)
        {
            $distributor = 'ERROR';
        }
        else
        {
            $dom = new DOMDocument;
            $dom->loadHTML($file_in_a_string);
            foreach($dom->getElementsByTagName('tr') as $node)
            {
                foreach($node->getElementsByTagName('td') as $cell)
                {
                    if (in_array($cell->nodeValue,['Citipower','Powercor','SP Ausnet','Jemena','United Energy']))
                    {
                        $array[] = $cell->nodeValue;
                    };
                }
            }
            //print_r($array);
        }

        // Output preparation
        unset($p_arr['serviceType']);
        unset($p_arr['_']);
        $p_arr['distributors']=join($array,",");
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
