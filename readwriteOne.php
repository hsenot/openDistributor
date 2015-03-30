<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");
    require_once("inc/database.inc.php");
    require_once("inc/security.inc.php");
    require_once("inc/json.pdo.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_pfi = "";
        if (isset($_REQUEST['pfi'])){$p_pfi = $_REQUEST['pfi'];}

        // Opening up DB connection
        $pgconn = pgConnection();

        // Retrieving details for an address PFI
        $sql="SELECT case when bunit_id1=0 then '' else bunit_id1||coalesce(bunit_suf1,'')||'' end unit,hse_num1||coalesce(hse_suf1,'') housenumber,road_name streetname,road_type streettype,locality,postcode FROM address WHERE pfi='".$p_pfi."' LIMIT 1";
        //echo $sql;
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        
        // Returns an associative array of parameters (name=>value)
        $p_arr = json_decode(rs2json($recordSet),true);
        //echo var_dump($p_arr);

        $rec = $p_arr['rows'][0];
        //echo var_dump($rec);

        // Build a proper web service request based on the parameters
        $full_url = 'http://'.$_SERVER['HTTP_HOST']."/openDistributor/get.php"."?". http_build_query($rec,'', '&');
        //echo $full_url;
        // Download the web service response
        $json = json_decode(file_get_contents($full_url));

        // Record the distributor information
        $sql = "update address_distributor set distributor='".$json->distributor."' WHERE pfi='".$p_pfi."'";
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        

        // Some output
        echo 'Executed query:'.$sql;
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
