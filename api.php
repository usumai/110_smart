<?php 
include "01_dbcon.php"; 
include "php/common/common.php";
include "php/service/fileupload.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$request = getApiAction();
   
if($request != null) {
    $act = $request->action;
}elseif (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}


if ($act=="create_ga_stocktake") {  
    execWithErrorHandler(function() use ($con, $request){   
        $stocktakeId = createGaStocktake($con, $request->data);
        $result = ["stocktakeId" => $stocktakeId];
        echo json_encode(new ResponseMessage("OK", $result));
    });
}elseif($act=="create_ga_assets") {
    execWithErrorHandler(function() use ($con, $request){ 
        createGaAssets($con, $request->data->stocktakeId, $request->data->assets);
        echo json_encode(new ResponseMessage("OK",null));
    });       
}elseif ($act=="create_is_audit") {      
    execWithErrorHandler(function() use ($con, $request){
        $stocktakeId = createIsAudit($con, $request->data);
        $result = ["stocktakeId" => $stocktakeId];
        echo json_encode(new ResponseMessage("OK", $result));
    });
}elseif($act=="create_is_impairments") {
    execWithErrorHandler(function() use ($con, $request){ 
        createIsImpairments($con, $request->data->stocktakeId, $request->data->impairments);
        echo json_encode(new ResponseMessage("OK",null));
    });
}elseif($act=="get_is_impairments") {
    execWithErrorHandler(function() { 
        $result = getIsImpairments();
        echo json_encode(new ResponseMessage("OK",$result));
    });       
}elseif($act=="get_sm19_cat") {
    execWithErrorHandler(function() { 
        $result = getSM19Cats();
        echo json_encode(new ResponseMessage("OK",$result));
    });      
}elseif($act=="save_user_profile") {
    execWithErrorHandler(function() use ($con, $request){ 
        $result = saveUserProfile($con, $request->data);
        echo json_encode(new ResponseMessage("OK",$result));
    });  
}elseif($act=="delete_user_profile") {
    execWithErrorHandler(function() use ($con, $request){ 
        $result = deleteUserProfile($con, $request->data);
        echo json_encode(new ResponseMessage("OK",$result));
    });     
}elseif($act=="get_user_profiles") {
    execWithErrorHandler(function(){ 
        echo json_encode(new ResponseMessage("OK",getUserProfiles()));
    });           
    
}elseif ($act=="get_system") {
    $sql        = "SELECT *, (SELECT stk_type FROM smartdb.sm13_stk WHERE stk_include=1 group by stk_type) AS act_type FROM smartdb.sm10_set;";
    echo json_encode(qget($sql));    
}elseif ($act=="get_activities") {
    getActivities();
}elseif ($act=="save_activity_toggle_include") {
	$stkm_id        = $_POST["stkm_id"];
    $stk_include    = $_POST["stk_include"];
    $sql            = "UPDATE smartdb.sm14_ass SET stk_include = $stk_include WHERE stkm_id = $stkm_id ";
    $sql1            = "UPDATE smartdb.sm13_stk SET stk_include = $stk_include WHERE stkm_id = $stkm_id ";
    mysqli_multi_query($con,$sql1);
    echo mysqli_multi_query($con,$sql);

}elseif ($act=="save_activity_toggle_delete") {
	$stkm_id        = $_POST["stkm_id"];
    $delete_status  = $_POST["delete_status"];
    $delete_status  = $delete_status==1 ? " smm_delete_date=NOW() " :  " smm_delete_date=NULL ";
    $sql            = " UPDATE smartdb.sm13_stk SET $delete_status WHERE stkm_id = $stkm_id ";
    echo mysqli_multi_query($con,$sql);
}elseif ($act=="get_stk_assets") {
    $sql = "    SELECT ass_id, res_create_date, res_asset_id, res_class, res_loc_location, res_loc_room, res_assetdesc1, res_assetdesc2, res_inventno, res_serialno, res_plateno, res_val_nbv, res_reason_code, CASE WHEN res_reason_code<>'' THEN 1 ELSE 0 END AS ass_status FROM smartdb.sm14_ass WHERE stk_include=1 AND delete_date IS NULL AND genesis_cat <> 'ga_template'";
    echo json_encode(qget($sql));
    
}elseif ($act=="get_stk_assets_to_export") {
	$stkm_id = $_POST["stkm_id"];
    $sql = "    SELECT * FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND genesis_cat <> 'ga_template'";
    echo json_encode(qget($sql));
    
}elseif ($act=="get_stk_progress") {
    $sql = "    SELECT COUNT(*) as count_total, SUM(CASE WHEN res_reason_code<>'' THEN 1 ELSE 0 END) AS count_complete   FROM smartdb.sm14_ass WHERE stk_include = 1 AND delete_date IS NULL AND genesis_cat <> 'template'";
    echo json_encode(qget($sql));

}elseif ($act=="get_stk_asset") {
	$ass_id = $_POST["ass_id"];
    $sql    = " SELECT *
                , DATE_FORMAT(res_date_lastinv, '%Y-%m-%d') AS res_date_lastinv_clean
                , DATE_FORMAT(res_date_cap, '%Y-%m-%d') AS res_date_cap_clean
                , DATE_FORMAT(res_date_pl_ret, '%Y-%m-%d') AS res_date_pl_ret_clean
                , DATE_FORMAT(res_date_deact, '%Y-%m-%d') AS res_date_deact_clean
                FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
    echo json_encode(qget($sql));

}elseif ($act=="get_stk_templates") {
    $sql = "    SELECT * FROM smartdb.sm14_ass WHERE delete_date IS NULL AND genesis_cat = 'ga_template'";
    echo json_encode(qget($sql));

}elseif ($act=="save_stk_ass_rc") {
	$ass_id         = $_POST["ass_id"];
	$res_reason_code= $_POST["res_reason_code"];
    $stmt   = $con->prepare("UPDATE smartdb.sm14_ass SET res_reason_code=? WHERE ass_id=?;");
    $stmt   ->bind_param("ss", $res_reason_code, $ass_id);
    $stmt   ->execute();

}elseif ($act=='get_is_records') {
    $sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND date(smm_delete_date) IS NULL";
    $sqlimp  = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND (isType <> 'b2r') AND ((isBackup IS NULL) OR (isBackup=0))";

    //Placeholder until data_source is added
    $sqlb2r  = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND (isType = 'b2r') AND ((isBackup IS NULL) OR (isBackup=0)) AND data_source='skeleton'";
    // $sqlb2r  = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND isType ='b2r' AND data_source='skeleton' ";

    $sql = $sqlimp." UNION ALL ".$sqlb2r;
    // echo $sql;
    
    echo json_encode(qget($sql));

}elseif ($act=='get_is_settings') {
    $sql = "SELECT findingID, color AS fCol, resAbbr AS fAbr FROM smartdb.sm19_result_cats;";
    echo json_encode(qget($sql));

}elseif ($act=="save_stk_delete_no_ass") {
    $ass_id     = $_POST["ass_id"];
    $direction  = $_POST["direction"];
    if ($direction=='delete'){
        $stmt   = $con->prepare("UPDATE smartdb.sm14_ass SET delete_date=NOW() WHERE ass_id=?;");
    }else{
        $stmt   = $con->prepare("UPDATE smartdb.sm14_ass SET delete_date=NULL WHERE ass_id=?;");
    }
    $stmt   ->bind_param("s", $ass_id);
    $stmt   ->execute();

}elseif ($act=='get_b2r_contents') {
    $stkm_id    = $_POST["stkm_id"];
    $BIN_CODE   = $_POST["BIN_CODE"];
    // $sql        = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id = $stkm_id AND BIN_CODE = '$BIN_CODE'";
    $sql        = " SELECT DSTRCT_CODE, WHOUSE_ID, BIN_CODE, STOCK_CODE, ITEM_NAME, SUM(SOH) as SOH ";
    $sql       .= " FROM smartdb.sm18_impairment   ";
    $sql       .= " WHERE isType = 'b2r' AND stkm_id = '$stkm_id' AND BIN_CODE = '$BIN_CODE' AND data_source <> 'skeleton' AND res_parent_storageID IS NULL  ";
    $sql       .= " GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, STOCK_CODE, ITEM_NAME ";
    echo json_encode(qget($sql));

}elseif ($act=='get_b2r_extras') {
    $stkm_id    = $_POST["stkm_id"];
    $BIN_CODE   = $_POST["BIN_CODE"];
    $sql        = " SELECT * FROM smartdb.sm18_impairment ";
    $sql       .= " WHERE isType = 'b2r' AND stkm_id = '$stkm_id' AND BIN_CODE = '$BIN_CODE' AND res_parent_storageID IS NOT NULL ";
    echo json_encode(qget($sql));

}elseif ($act=='get_b2r_skeleton') {
    $stkm_id    = $_POST["stkm_id"];
    $BIN_CODE   = $_POST["BIN_CODE"];
    $sql        = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id = $stkm_id AND BIN_CODE = '$BIN_CODE'";
    echo json_encode(qget($sql));

}elseif ($act=='get_b2r_bin') {
    $auto_storageID    = $_POST["auto_storageID"];
    $sql        = " SELECT * FROM smartdb.sm18_impairment  WHERE auto_storageID = $auto_storageID";
    echo json_encode(qget($sql));


}elseif ($act=='save_final_b2r_extra_result') {
    $auto_storageID     = $_POST["auto_storageID"];
    $finalResult        = $_POST["finalResult"];
    $finalResultPath    = $_POST["finalResultPath"];
    if($finalResult=="clear"){// Clear results
        $msg = "Clear results";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET finalResult=NULL, finalResultPath=NULL
                                    WHERE auto_storageID=? ");
        $stmt   ->bind_param("s",  $auto_storageID);
    }else{//set a value
        $msg = "Result set";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET finalResult=?, finalResultPath=?
                                    WHERE auto_storageID=? ");
        $stmt   ->bind_param("sss", $finalResult, $finalResultPath, $auto_storageID);
    }
    $stmt   ->execute();
    echo $msg;
    

}elseif ($act=='save_delete_b2r_extra') {
    $auto_storageID     = $_POST["auto_storageID"];
    $stmt   = $con->prepare("   DELETE FROM smartdb.sm18_impairment WHERE auto_storageID=? ");
    $stmt   ->bind_param("s",  $auto_storageID);
    $stmt   ->execute();
    echo "Deleted record";

}elseif ($act=='get_result_cats') {
    $sql        = " SELECT * from smartdb.sm19_result_cats ";
    echo json_encode(qget($sql));

}elseif ($act=='save_b2r_result') {
    $BIN_CODE       = $_POST["BIN_CODE"];
    $stkm_id        = $_POST["stkm_id"];
    $findingID      = $_POST["findingID"];
    $current_user   = "user_function_not_made";
    $fingerprint    = time();

    $msg = "Not set";
    if($findingID==0){//NSTR
        $msg = "Clear results";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NULL, res_update_user=NULL, findingID=NULL,fingerprint=NULL
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("ss", $BIN_CODE, $stkm_id);
    }else if($findingID==14){//NSTR
        $msg = "NSTR";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NOW(), res_update_user=?, findingID=?,fingerprint=?
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("sssss", $current_user, $findingID, $fingerprint, $BIN_CODE, $stkm_id);
    }else if($findingID==15){//There are extra stockcodes in the bin
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NULL, res_update_user=NULL, findingID=?,fingerprint=?
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("ssss", $findingID, $fingerprint, $BIN_CODE, $stkm_id);
    }
    $stmt   ->execute();

    echo $msg;


}elseif ($act=='save_b2r_extra') {
    $BIN_CODE       = $_POST["BIN_CODE"];
    $stkm_id        = $_POST["stkm_id"];
    $current_user   = "user_function_not_made";
    $fingerprint    = time();

    $stmt   = $con->prepare("   INSERT INTO smartdb.sm18_impairment (res_create_date, res_update_user, fingerprint, 
                                res_parent_storageID, stkm_id, BIN_CODE, DSTRCT_CODE, WHOUSE_ID, isType, data_source)
                                SELECT NOW(), ?, ?, storageID, stkm_id, BIN_CODE, DSTRCT_CODE, WHOUSE_ID, isType, 'extra'
                                FROM smartdb.sm18_impairment
                                WHERE BIN_CODE=? AND stkm_id=? AND data_source='skeleton'");
    $stmt   ->bind_param("ssss",  $current_user, $fingerprint, $BIN_CODE, $stkm_id);
    $stmt   ->execute();
       

    
}elseif ($act=="save_textinput") {
	$full_table_name    = $_POST["full_table_name"];
	$column_name        = $_POST["column_name"];
    $internal_val       = $_POST["internal_val"];
	$primary_key_name   = $_POST["primary_key_name"];
    $primary_key        = $_POST["primary_key"];
    $sql    = "UPDATE $full_table_name SET $column_name='$internal_val' WHERE $primary_key_name=$primary_key;";
    echo mysqli_multi_query($con,$sql);

}elseif ($act=="get_stk_rcs") {
    $sql = " SELECT * FROM smartdb.sm15_rc ";
    echo json_encode(qget($sql));

}elseif ($act=="update_rcs") {
    function get_file(){
        foreach(glob('config/*.*') as $filename) {//find the filename in the config folder
            if (strpos($filename, 'reason_codes') !== false) {
                return $filename;
            }
        }
    }
    $sql            = "TRUNCATE TABLE smartdb.sm15_rc;";
    $res_truncate   = mysqli_multi_query($con,$sql);
    $filename       = get_file('reason_codes');
    $file_contents  = file_get_contents($filename);
    $string         = "[" . trim($file_contents) . "]";
    $json           = json_decode($string, true);
    $header         = $json[0];
    
    $stmt   = $con->prepare("INSERT INTO smartdb.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_example, rc_origin, rc_action, rc_states) VALUES (?,?,?,?,?,?,?);");
    foreach ($header['reason_codes'] as $key => $val) {    
        $stmt   ->bind_param("sssssss", $val['res_reason_code'], $val['rc_desc'], $val['rc_long_desc'], $val['rc_example'], $val['rc_origin'], $val['rc_action'], $val['rc_states']);
        $stmt   ->execute();
    }

}elseif ($act=="get_images") {
    $ass_id   = $_POST["ass_id"];   

    $sql = "SELECT genesis_cat, res_asset_id, res_fingerprint FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $genesis_cat        = $row["genesis_cat"];
            $res_asset_id       = $row["res_asset_id"];
            $res_fingerprint    = $row["res_fingerprint"];
    }}
    $a          = scandir("images/");
    $img_list   = "";
    $images     = "";
    if ($genesis_cat=="nonoriginal") {
         $photo_name_test	= $res_fingerprint;
    }else{
         $photo_name_test	= $res_asset_id;
    }
    $filearr = [];
    foreach ($a as $key => $photo_name) {
         $photo_name_parts = explode("_",$photo_name);
        if ($photo_name_parts[0]==$photo_name_test)  {
            array_push($filearr,$photo_name);
            //   $img_list .= "<button type='button' class='btn thumb_photo' value='".$photo_name."' data-toggle='modal' data-target='#modal_show_pic'><img src='images/".$photo_name."?".time()."' width='200px'></button>"; 
        }
    }
    echo json_encode($filearr);
    
}elseif ($act=="get_stk_assets_export") {
    $stkm_id    = $_POST["stkm_id"];  
    $sql        = " SELECT ass_id, stkm_id, 0 AS stk_include, rr_id, ledger_id, create_date, create_user, delete_date, sto_asset_id, sto_assetdesc1,  sto_assetdesc2, sto_assettext, sto_class, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity,sto_val_nbv,sto_val_acq,sto_val_orig,sto_val_scrap,sto_valuation_method,sto_ccc,sto_ccc_name,sto_ccc_parent,sto_ccc_parent_name,sto_wbs,sto_fund,sto_responsible_ccc,sto_mfr,sto_inventory,sto_inventno,sto_serialno,sto_site_no,sto_grpcustod,sto_plateno,sto_revodep,sto_date_lastinv,sto_date_cap,sto_date_pl_ret,sto_date_deact,sto_loc_latitude,sto_loc_longitude,genesis_cat,res_create_date,res_create_user,res_fingerprint,res_reason_code,res_rc_desc,res_comment,res_asset_id,res_assetdesc1,res_assetdesc2,res_assettext,res_class,res_class_ga_cat,res_loc_location,res_loc_room,res_loc_state,res_quantity,res_val_nbv,res_val_acq,res_val_orig,res_val_scrap,res_valuation_method,res_ccc,res_ccc_name,res_ccc_parent,res_ccc_parent_name,res_wbs,res_fund,res_responsible_ccc,res_mfr,res_inventory,res_inventno,res_serialno,res_site_no,res_grpcustod,res_plateno,res_revodep,res_date_lastinv,res_date_cap,res_date_pl_ret,res_date_deact,res_loc_latitude,res_loc_longitude
    FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id ";
    // echo $sql;
    echo json_encode(qget($sql));

}elseif ($act=='save_create_template') {
    $ass_id        = $_POST["ass_id"];

    $fingerprint        = time();
    $sql = " INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, ledger_id, stk_include, rr_id, sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_name, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method, sto_ccc, sto_ccc_name, sto_ccc_parent, sto_ccc_parent_name, sto_wbs, sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_revodep, sto_date_lastinv,  sto_date_cap, sto_date_pl_ret, sto_date_deact, sto_loc_latitude, sto_loc_longitude, genesis_cat, res_create_date, res_create_user, res_fingerprint, res_reason_code, res_rc_desc, res_comment, res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_name, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method, res_ccc, res_ccc_name, res_ccc_parent, res_ccc_parent_name, res_wbs, res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_revodep, res_date_lastinv,  res_date_cap, res_date_pl_ret, res_date_deact, res_loc_latitude, res_loc_longitude)
    SELECT NOW(), create_user, delete_date, delete_user, stkm_id, ledger_id, stk_include, rr_id, sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_name, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method, sto_ccc, sto_ccc_name, sto_ccc_parent, sto_ccc_parent_name, sto_wbs, sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_revodep, sto_date_lastinv,  sto_date_cap, sto_date_pl_ret, sto_date_deact, sto_loc_latitude, sto_loc_longitude, 'ga_template', res_create_date, res_create_user, '$fingerprint', res_reason_code, res_rc_desc, res_comment, res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_name, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method, res_ccc, res_ccc_name, res_ccc_parent, res_ccc_parent_name, res_wbs, res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_revodep, res_date_lastinv,  res_date_cap, res_date_pl_ret, res_date_deact, res_loc_latitude, res_loc_longitude 
    FROM smartdb.sm14_ass
    WHERE ass_id =$ass_id ;";
    // echo "<br><br><br>$sql";
    mysqli_multi_query($con,$sql);
    
    $sql = "SELECT ass_id FROM smartdb.sm14_ass ORDER BY ass_id DESC LIMIT 1;";
    echo json_encode(qget($sql));
    
}elseif ($act=="get_rr_stats") {
    $sql        = " SELECT COUNT(*) AS rr_rowcount FROM smartdb.sm12_rwr ";
    echo json_encode(qget($sql));

}elseif ($act=="get_search_results") {
    $search_term    = $_POST["search_term"];
    $sql            = " SELECT ass_id, res_asset_id, res_assetdesc1, sto_assetdesc2, stkm_id,  res_reason_code FROM smartdb.sm14_ass WHERE res_assetdesc1 LIKE '%$search_term%' AND delete_date IS NULL AND stk_include=1 LIMIT 10 ";
    echo json_encode(qget($sql));

}elseif ($act=="get_rwr_search_results") {
    $search_term    = $_POST["search_term"];
    $sql            = " SELECT COUNT(*) AS rr_search_count
                        FROM smartdb.sm12_rwr 
                        WHERE AssetDesc1 LIKE '%$search_term%' 
                        OR Class LIKE '%$search_term%' 
                        OR accNo LIKE '%$search_term%' 
                        OR InventNo LIKE '%$search_term%'  ";
    echo json_encode(qget($sql));

}elseif ($act=='save_check_version'){
    // Steps for relesing a new version:
    // 1. Update the version info above with version number one more than current
    // 2. Update the 08_version.json as per above details
    // 3. Delete json and xls files from directory to stop any leaks
    // 4. Push local to master using toolshelf
    $test_internet = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
    if ($test_internet){
         $URL = 'https://raw.githubusercontent.com/usumai/110_smart/master/08_version.json';
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_URL, $URL);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
         $data = curl_exec($ch);
         curl_close($ch);
         $json = json_decode($data, true);
         $latest_version_no       = $json["latest_version_no"];
         $version_publish_date    = $json["version_publish_date"];

         $sql_save = "UPDATE smartdb.sm10_set SET date_last_update_check=NOW(), versionRemote=$latest_version_no; ";
         mysqli_multi_query($con,$sql_save);
         $test_results = 1 ;//"Check performed and updated";

    }else{
         $test_results = 2 ;//"Internet is required to check the version";
    }

    // Compare remote to local and advise if update button should be displayed
    $sql = "SELECT '$test_results' AS test_results ";
    echo json_encode(qget($sql));

    // $result = $con->query($sql);
    // if ($result->num_rows > 0) {
    //      while($row = $result->fetch_assoc()) {
    //      $versionLocal	= $row["versionLocal"];
    //      $versionRemote	= $row["versionRemote"];
    // }}
    // $data  = [];
    // $data["versionLocal"]    = $versionLocal;
    // $data["versionRemote"]   = $versionRemote;
    // $data["test_results"]    = $test_results;
    // $data = json_encode($data);
    // echo $data;
    
    
}

function qget($sql){
    // Submits a basic sql and returns an array result
    global $con;
    $res = [];
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res[] = $row;
    }}
    return $res;
}

function getIsDistrictList() {
    $sql="SELECT 
                DSTRCT_CODE, 
                WHOUSE_ID 
            FROM smartdb.sm18_impairment
            WHERE stkm_id IN (
                SELECT 
                    stkm_id 
                FROM 
                    smartdb.sm13_stk 
                WHERE 
                    stk_include=1 AND 
                    date(smm_delete_date) IS NULL
                ) AND 
                isBackup=0 AND 
                LEFT(isType,3)='imp' AND 
                date(delete_date) IS NULL 
            GROUP BY DSTRCT_CODE, WHOUSE_ID";
    return qget($sql);
}

function getIsImpairments() {
    $sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
    $sql  = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) ";
    return qget($sql);
}

function getSM19Cats() {
    $sql = "SELECT 
			findingID, 
			color, 
			resAbbr
		FROM smartdb.sm19_result_cats;";               

	return qget($sql);
}
function getUserProfiles(){
     $sql = "SELECT * FROM smartdb.sm11_pro WHERE date(delete_date) IS NULL;";
    return qget($sql);
}
function deleteUserProfile($connection, $record){
    $sql = "DELETE FROM smartdb.sm11_pro WHERE profile_id = $record->profile_id";
    $connection->query($sql);
    return $record->profile_id;
}
function saveUserProfile($connection, $record){

    $insertSql = "INSERT INTO smartdb.sm11_pro (
                    create_date, 
                    profile_name, 
                    profile_phone_number)
                    VALUE (NOW(), ?, ?)";

    $updateSql = "UPDATE smartdb.sm11_pro 
                  SET 
                    update_date=NOW(),
                    profile_name=?,
                    profile_phone_number=?
                    WHERE profile_id=?";

    $prodId=0;
    if ($record->profile_id==0){
        $stmt = $connection->prepare($insertSql);

        $stmt->bind_param("ss", 
            $record->profile_name, 
            $record->profile_phone_number);        

        $stmt->execute();
        $prodId = $stmt->insert_id;  
    }else{
        $stmt = $connection->prepare($updateSql);

        $stmt->bind_param("sss", 
            $record->profile_name, 
            $record->profile_phone_number,
            $record->profile_id);        
                
        $stmt->execute();
        $prodId = $record->profile_id;      
    }

    $connection->query("UPDATE smartdb.sm10_set SET active_profile_id=$prodId");
    return $prodId;
}


function getActivities() {
    $sql = "
SELECT 
    act.*, 
    asset.*
FROM 
    smartdb.sm13_stk as act 
    LEFT JOIN 
    (
        (
            SELECT stkm_id,
                'Stocktake' as isCat,
	            SUM(CASE WHEN genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig,
	            SUM(CASE WHEN genesis_cat='nonoriginal' THEN 1 ELSE 0 END) AS rc_extras,
	            SUM(CASE WHEN res_reason_code <>'' AND genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig_complete,
	            COUNT(*) AS rc_totalsent
	        FROM smartdb.sm14_ass 
	        WHERE ((date(delete_date) IS NULL) or (date(delete_date)='0000-00-00'))
	        GROUP BY stkm_id
        )
        UNION all
        (
            SELECT stkm_id,
                'B2R' as isCat,
	            SUM(CASE WHEN data_source <> 'extra' THEN 1 ELSE 0 END) AS rc_orig,
	            SUM(CASE WHEN data_source = 'extra' THEN 1 ELSE 0 END) AS rc_extras,
	            SUM(CASE WHEN ((findingID <> '') and (data_source <> 'extra')) THEN 1 ELSE 0 END) AS rc_orig_complete,
	            COUNT(*) AS rc_totalsent
	        FROM smartdb.sm18_impairment 
            WHERE ((date(delete_date) IS NULL) or (date(delete_date)='0000-00-00'))
                  AND isType='b2r'
	        GROUP BY stkm_id        
        )        
        UNION all
        (
            SELECT stkm_id, 
                'Impairment' as isCat,
	            SUM(CASE WHEN data_source <> 'extra' THEN 1 ELSE 0 END) AS rc_orig,
	            SUM(CASE WHEN data_source ='extra' THEN 1 ELSE 0 END) AS rc_extras,
	            SUM(CASE WHEN ((findingID <> '') and (data_source <> 'extra')) THEN 1 ELSE 0 END) AS rc_orig_complete,
	            COUNT(*) AS rc_totalsent
	        FROM smartdb.sm18_impairment 
            WHERE ((date(delete_date) IS NULL) or (date(delete_date)='0000-00-00'))
                  AND isType<>'b2r'
	        GROUP BY stkm_id        
        )
    ) as asset
    ON act.stkm_id=asset.stkm_id;";
            
    echo json_encode(qget($sql));
}
?>