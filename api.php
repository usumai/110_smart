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
    $sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
    $sql  = " SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) ";


    
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

function getActivities() {
/*    
   $sql = "SELECT * 
            FROM smartdb.sm13_stk LEFT JOIN 
                (SELECT stkm_id,
                SUM(CASE WHEN genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig,
                SUM(CASE WHEN genesis_cat='nonoriginal' THEN 1 ELSE 0 END) AS rc_extras,
                SUM(CASE WHEN res_reason_code<>'' AND genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig_complete,
                COUNT(*) AS rc_totalsent, stk_include
                FROM smartdb.sm14_ass 
                WHERE delete_date IS NULL
                GROUP BY stkm_id ) AS vt1
            ON smartdb.sm13_stk.stkm_id= vt1.stkm_id;";
*/            

    $sql = "SELECT 
                stk.*, 
                vt1.rc_orig,
                vt1.rc_extras,
                vt1.rc_orig_complete,
                vt1.rc_totalsent
            FROM smartdb.sm13_stk as stk LEFT JOIN 
                (SELECT stkm_id,
                    SUM(CASE WHEN genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig,
                    SUM(CASE WHEN genesis_cat='nonoriginal' THEN 1 ELSE 0 END) AS rc_extras,
                    SUM(CASE WHEN res_reason_code<>'' AND genesis_cat='original' THEN 1 ELSE 0 END) AS rc_orig_complete,
                    COUNT(*) AS rc_totalsent
                FROM smartdb.sm14_ass 
                WHERE delete_date IS NULL
                GROUP BY stkm_id ) AS vt1
            ON stk.stkm_id= vt1.stkm_id;";
            
    echo json_encode(qget($sql));
}
?>