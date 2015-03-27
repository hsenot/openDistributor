<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");
    require_once("inc/database.inc.php");
    require_once("inc/security.inc.php");
    require_once("inc/json.pdo.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_locality = "";
        if (isset($_REQUEST['locality'])){$p_locality = $_REQUEST['locality'];}
        $p_postcode = "";
        if (isset($_REQUEST['postcode'])){$p_postcode = $_REQUEST['postcode'];}

        $rec = array('locality'=>$p_locality,'postcode'=>$p_postcode);
        //echo var_dump($rec);

        // Build a proper web service request based on the parameters
        $full_url = 'http://'.$_SERVER['HTTP_HOST']."/openDistributor/getByLocalityPostcode.php"."?". http_build_query($rec,'', '&');
        //echo $full_url;
        // Download the web service response
        $json = json_decode(file_get_contents($full_url));

        // Record the distributor information
        $sql = "update locality_postcode set distributors='".$json->distributors."' WHERE locality='".$p_locality."' AND postcode='".$p_postcode."'";

        // Opening up DB connection
        $pgconn = pgConnection();
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        

        // Some output
        echo 'Executed query:'.$sql;
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
