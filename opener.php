<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");
    require_once("inc/database.inc.php");
    require_once("inc/security.inc.php");

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

        // Build a proper web service request based on the parameters
        $full_url = "http://tools.energyandresources.vic.gov.au/energyapi/energytest1.php?housenumber=".$p_housenumber."&unit=".$p_unit."&streetname=".$p_streetname."&streettype=".$p_streettype."&locality=".$p_locality."&postcode=".$p_postcode;

        // Download the web service response
        $file_in_a_string = file_get_contents($full_url);

        // Extraction of the distributor information based on the pattern
        // <div class="msg_distributor">Your distribution business is: <strong><a href="#contacts">XXXXXXXXX</a></strong></div>
        $distributor = substr($file_in_a_string,strpos($file_in_a_string,"contacts")+10,strpos($file_in_a_string,"</a>")-strpos($file_in_a_string,"contacts")-10);

        // Record the distributor information
        // Opening up DB connection
        $pgconn = pgConnection();

        // Inserting the observation
        $sql = "update vmadd_address set distributor='".$distributor."' WHERE ....;";
        echo $sql;

        // TODO: finalise data structure and SQL statement before uncommenting below
        //$recordSet = $pgconn->prepare($sql);
        //$recordSet->execute();
    }
    catch (Exception $e) {
        trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
    }
?>
