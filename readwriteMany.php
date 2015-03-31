<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");
    require_once("inc/database.inc.php");
    require_once("inc/security.inc.php");
    require_once("inc/json.pdo.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $p_postcode = "";
        if (isset($_REQUEST['postcode'])){$p_postcode = $_REQUEST['postcode'];}

        // Some output
        echo 'Postcode:'.$p_postcode.'...started!';

        // Opening up DB connection
        $pgconn = pgConnection();

        // Retrieving details for an address PFI
        $sql="SELECT pfi FROM address_distributor WHERE postcode='".$p_postcode."' AND distributor=''";
        //echo $sql;
        $recordSet = $pgconn->prepare($sql);
        $recordSet->execute();        
        // Returns an associative array of parameters (name=>value)
        $p_arr = json_decode(rs2json($recordSet),true);
        //echo var_dump($p_arr);

        $recs = $p_arr['rows'];
        //echo var_dump($recs);

        foreach ($recs as $rec)
        {
            echo 'PFI:'.$rec['pfi'].'...';
            // Build a proper web service request based on the parameters
            $full_url = 'http://'.$_SERVER['HTTP_HOST']."/openDistributor/readwriteOne.php?pfi=".$rec['pfi'];
            //echo $full_url;
            // Download the web service response
            $json = json_decode(file_get_contents($full_url));
            echo 'done!';
        }

        // Some output
        echo 'Postcode:'.$p_postcode.'...done!';
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
