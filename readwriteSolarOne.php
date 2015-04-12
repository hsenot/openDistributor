<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");
    require_once("inc/database.inc.php");
    require_once("inc/security.inc.php");
    require_once("inc/json.pdo.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_id = 12345;
        if (isset($_REQUEST['id'])){$p_id = $_REQUEST['id'];}

        // Opening up DB connection
        $pgconn = pgConnection();

        // Retrieving details for an address PFI
        $sql="SELECT ST_X(ST_Centroid(the_geom)) as x,ST_Y(ST_Centroid(the_geom)) as y FROM australia_grid WHERE gid=".$p_id." LIMIT 1";
        //echo $sql;
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        
        // Returns an associative array of parameters (name=>value)
        $rec = json_decode(rs2json($recordSet),true);
        //echo var_dump($rec["rows"][0]);

        // Build a proper web service request based on the parameters
        $full_url = 'http://'.$_SERVER['HTTP_HOST']."/openDistributor/getSolar.php"."?". http_build_query($rec["rows"][0],'', '&');
        //echo $full_url;
        // Download the web service response
        $json = json_decode(file_get_contents($full_url));

        // Record the distributor information
        $sql = "insert into australia_grid_solar (gid,ghi,the_geom) select gid,".$json->ghi.",the_geom from australia_grid WHERE gid=".$p_id;
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        

        // Some output
        echo 'Executed query:'.$sql;
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
