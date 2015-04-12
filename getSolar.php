<?php
    date_default_timezone_set('Australia/Melbourne');

    # Includes
    require_once("inc/error.inc.php");

    # Performs the query and returns XML or JSON
    try {
        $gs = 0.001;
        $p_x = 145.906;
        if (isset($_REQUEST['x'])){$p_x = floatval($_REQUEST['x']);}
        $p_y = -38.244;
        if (isset($_REQUEST['y'])){$p_y = floatval($_REQUEST['y']);}

        $p_arr = array('REQUEST'=>'GetFeatureInfo','EXCEPTIONS'=>'application/vnd.ogc.se_xml','BBOX'=>($p_x-$gs).','.($p_y-$gs).','.($p_x+$gs).','.($p_y+$gs),'SERVICE'=>'WMS','INFO_FORMAT'=>'application/json','QUERY_LAYERS'=>'_3tier:GHI_Global_3km_3TIER','FEATURE_COUNT'=>50,'Layers'=>'_3tier:GHI_Global_3km_3TIER','WIDTH'=>512,'HEIGHT'=>512,'format'=>'image/jpeg','srs'=>'EPSG:4326','version'=>'1.1.1','x'=>256,'y'=>256);

// http://irena.masdar.ac.ae:8080/geoserver/_3tier/wms?REQUEST=GetFeatureInfo&EXCEPTIONS=application%2Fvnd.ogc.se_xml&BBOX=138.625172%2C-44.885483%2C150.329672%2C-33.180983
        //&SERVICE=WMS&INFO_FORMAT=application/json&QUERY_LAYERS=_3tier%3AWind_Speed_Global_5km_80m_3TIER&FEATURE_COUNT=50&Layers=_3tier%3AWind_Speed_Global_5km_80m_3TIER&WIDTH=512&HEIGHT=512&format=image%2Fjpeg&styles=&srs=EPSG%3A4326&version=1.1.1&x=215&y=221

        // Build a proper web service request based on the parameters
        $full_url = "http://irena.masdar.ac.ae:8080/geoserver/_3tier/wms"."?". http_build_query($p_arr,'', '&');
        //echo $full_url;

        // Download the web service response
        $json_res = json_decode(file_get_contents($full_url),true);
        $output = json_encode(array("ghi"=>$json_res['features'][0]['properties']['GRAY_INDEX']));

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
