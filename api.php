<?php 
include "01_dbcon.php"; 
include "05_db_designer.php";
include "php/service/ActivityImport.php";
include "php/service/ActivityExport.php";
header("Cache-Control: no-store, max-age=0");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_exception_handler('errorHandler');
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
    	 
        $stocktakeId = createStocktakeActivity($con, $request->data);
        $result = ["stocktakeId" => $stocktakeId];
        echo json_encode(new ResponseMessage("OK", $result));
    });
}elseif($act=="create_ga_assets") {
    execWithErrorHandler(function() use ($con, $request){ 
    
    	$result = ["processed" => 0,
					"taskId"=>$request->data->taskId]; 
    
    	if($request->data->records){
    	
        	createGaAssets($con, $request->data->stocktakeId, $request->data->records);
        	    		
        	$result = [ "processed" => count($request->data->records), 
        				"taskId"=>$request->data->taskId];
        }
        echo json_encode(new ResponseMessage("OK",$result));
    });
}elseif($act=="import_ga_asset_images"){
    execWithErrorHandler(function() use ($con, $request){
        importGaImages($request->data->activity_id);
        echo json_encode(new ResponseMessage("OK",0));
    });
}elseif ($act=="backup_export_json"){
    
    execWithErrorHandler(function() use ($con, $request){
        backupExportJson();
        echo json_encode(new ResponseMessage("OK",0));
    });  
}elseif ($act=="clear_ga_rr"){
    execWithErrorHandler(function() use ($con, $request){ 
    	$result = ["processed" => 0]; 
   		clearGaRawRemainder($con);
        echo json_encode(new ResponseMessage("OK",$result));
    });       
}elseif ($act=="create_ga_abbrs"){
    execWithErrorHandler(function() use ($con, $request){ 
    	$result = ["processed" => 0,
					"taskId"=>$request->data->taskId];  
    	if($request->data->records){
    	
    		createGaAbbrs($con,$request->data->records);
    		
        	$result = [ "processed" => count($request->data->records), 
        				"taskId"=>$request->data->taskId];
        }
        echo json_encode(new ResponseMessage("OK",$result));
    });       
}elseif ($act=="create_ga_raw_remainders"){
    execWithErrorHandler(function() use ($con, $request){ 
    	$result = ["processed" => 0,
					"taskId"=>$request->data->taskId]; 
    	if($request->data->records){
    		
    		createGaRawRemainders($con,$request->data->records);
    		
        	$result = [ "processed" => count($request->data->records), 
        				"taskId"=>$request->data->taskId];
        }
        echo json_encode(new ResponseMessage("OK",$result));
    });        
}elseif ($act=="update_settings"){
    execWithErrorHandler(function() use ($con, $request){ 
    	$result = ["processed" => 0]; 
    	if($request->data->settings){
    		updateSettings($con,$request->data->settings);
        	$result = ["processed" => 1];
        }
        echo json_encode(new ResponseMessage("OK",$result));
    });              
}elseif($act=="count_is_NotYetComplete"){
    execWithErrorHandler(function() use ($con, $request){
        echo json_encode(new ResponseMessage("OK", countIsNotYetCompleteItems($request->data->activityId)));
    });
    
}elseif ($act=="create_is_audit") {      
    execWithErrorHandler(function() use ($con, $request){
        $stocktakeId = createStocktakeActivity($con, $request->data);
        $result = ["stocktakeId" => $stocktakeId];
        echo json_encode(new ResponseMessage("OK", $result));
    });
}elseif($act=="create_is_impairments") {
    execWithErrorHandler(function() use ($con, $request){ 
    	$result = ["processed" => 0,
    				"taskId"=>$request->data->taskId];
    	if($request->data->records){
    	
        	createIsImpairments($con, $request->data->stocktakeId, $request->data->records);
            		
        	$result = [ "processed" => count($request->data->records), 
        				"taskId"=>$request->data->taskId];

        }
        
        echo json_encode(new ResponseMessage("OK",$result));
    });
}elseif ($act=='reset_data') {
    execWithErrorHandler(function() use ($con, $request) {
        $result = resetData( $request &&  $request->data ?  $request->data->excludedRawRemainder : false);
        echo json_encode(new ResponseMessage("OK",$result));
    });   
}elseif($act=="update_software") {
    execWithErrorHandler(function() { 
        $result = updateSoftware();
        echo json_encode(new ResponseMessage("OK",$result));
    });         
}elseif($act=="get_is_impairments") {
    execWithErrorHandler(function() { 
        $result = getIsImpairments();
        echo json_encode(new ResponseMessage("OK",$result));
    });      
}elseif($act=="get_milis_finding_ids") {
    execWithErrorHandler(function() use ($isImpAbbrsWithMilisEnabled) {     	
        $result = getFindingIDs("imp%", $isImpAbbrsWithMilisEnabled);
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
    $delete_status  = $delete_status==1 ? " delete_date=NOW(), stk_include=0 " :  " delete_date=NULL ";
    $sql            = " UPDATE smartdb.sm13_stk SET $delete_status WHERE stkm_id = $stkm_id ";
    echo mysqli_multi_query($con,$sql);
}elseif ($act=="get_ga_assets") {
    $sql = "
    SELECT 
    	ass.ass_id, 
    	ass.res_create_date, 
    	ass.res_asset_id, 
    	ass.res_class, 
    	ass.res_loc_location, 
    	ass.res_loc_room, 
    	ass.res_assetdesc1, 
    	ass.res_assetdesc2,
    	ass.res_inventno, 
    	ass.res_serialno, 
    	ass.res_grpcustod, 
    	ass.res_val_nbv, 
    	ass.res_reason_code, 
    	(
    		CASE WHEN ass.res_reason_code<>'' 
    			THEN 1 
    			ELSE 0 
    		END
    	) AS ass_status    	 
    FROM 
    	smartdb.sm14_ass ass 
    	INNER JOIN
    	smartdb.sm13_stk act
    	ON ass.stkm_id=act.stkm_id   	
    WHERE 
    	act.stk_include=1 
    	AND ((act.delete_date IS NULL) OR (date(act.delete_date)='0000-00-00')) 
    	AND (ass.genesis_cat <> 'ga_template')";
    
    
    echo json_encode(qget($sql));
    
}elseif ($act=="get_stk_assets_to_export") {
	$stkm_id = $_POST["stkm_id"];
    $sql = "    SELECT * FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND genesis_cat <> 'ga_template'";
    echo json_encode(qget($sql));
    
}elseif ($act=="export_is") {
	$stkm_id = $_POST["stkm_id"];
	$activity= exportIsActivity($stkm_id);
    echo json_encode($activity);	
}elseif ($act=="export_ga_data") {
    $stkm_id    = $_POST["stkm_id"];  
 	$activity= exportGaActivity($stkm_id);
    echo json_encode($activity);     
}elseif ($act=="export_ga_asset_images") {
    execWithErrorHandler(function() use ($con, $request) {
        $result= exportGaImages($request->data->activityId, $request->data->fileName);
        echo json_encode(new ResponseMessage("OK",$result));
    });  
}elseif ($act=="get_stk_progress") {
    $sql = "    SELECT COUNT(*) as count_total, SUM(CASE WHEN a.res_reason_code<>'' THEN 1 ELSE 0 END) AS count_complete   FROM smartdb.sm14_ass a left join smartdb.sm13_stk s on a.stkm_id=s.stkm_id WHERE s.stk_include = 1 AND a.delete_date IS NULL AND a.genesis_cat <> 'template'";
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
    $stmt   = $con->prepare("UPDATE smartdb.sm14_ass SET res_reason_code=?, res_create_date=NOW() WHERE ass_id=?;");
    $stmt   ->bind_param("ss", $res_reason_code, $ass_id);
    $stmt   ->execute();

}elseif ($act=='get_is_records') {
    execWithErrorHandler(function() { 
        $result = getIsRecords();
        echo json_encode(new ResponseMessage("OK",$result));
    });     
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
    $sql        = "

SELECT 
    DSTRCT_CODE, 
    isType,
    WHOUSE_ID, 
    BIN_CODE, 
    STOCK_CODE, 
    ITEM_NAME, 
    SUM(SOH) as SOH,
    (CASE WHEN checkFlag=1 THEN 1 ELSE 0 END) as SIGHTED
FROM 
    smartdb.sm18_impairment   
WHERE 
    isType in ('b2r','b2r_exc') AND 
    stkm_id = $stkm_id AND 
    BIN_CODE = '$BIN_CODE' AND 
    (data_source NOT IN ('skeleton', 'extra'))
GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, STOCK_CODE, ITEM_NAME
ORDER BY 
    STOCK_CODE ASC, ITEM_NAME ASC";

    echo json_encode(qget($sql));

}elseif ($act=='get_b2r_extras') {
    $stkm_id    = $_POST["stkm_id"];
    $BIN_CODE   = $_POST["BIN_CODE"];
    $sql        = " SELECT * FROM smartdb.sm18_impairment ";
    $sql       .= " WHERE isType = 'b2r' AND stkm_id = $stkm_id AND BIN_CODE = '$BIN_CODE' AND data_source='extra' ";
    echo json_encode(qget($sql));

}elseif ($act=='get_b2r_skeleton') {
    echo json_encode(getB2RBinRecord($_POST["stkm_id"],$_POST["BIN_CODE"]));
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
                                    SET finalResult=NULL, 
                                    	finalResultPath=NULL
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
    
    //update parent bin status
    updateB2RBinStatusByChildId($con, $auto_storageID, null);    
    
    echo $msg;
    

}elseif ($act=='save_delete_b2r_extra') {
    $auto_storageID     = $_POST["auto_storageID"];
    
  	$rec= qget("select 
					stkm_id, 
					BIN_CODE 
				FROM smartdb.sm18_impairment 
				WHERE auto_storageID= $auto_storageID");
	  
    
    $stmt   = $con->prepare("   DELETE FROM smartdb.sm18_impairment WHERE auto_storageID=? ");
    $stmt   ->bind_param("s",  $auto_storageID);
    $stmt   ->execute();
    
    //update parent bin status
    if(count($rec)>0){
		updateB2RBinStatus($con,$rec[0]["stkm_id"],$rec[0]["BIN_CODE"],null);
	}		
   
    echo "Deleted record";

}elseif ($act=='get_result_cats') {
    $sql        = " SELECT * from smartdb.sm19_result_cats ";
    if($_POST["isType"]){
    	$byType=$_POST["isType"];
    	
    	$sql .= "WHERE isType='$byType'";
    }
    
    echo json_encode(qget($sql));

}elseif ($act=='save_b2r_result') {
    $BIN_CODE       = $_POST["BIN_CODE"];
    $stkm_id        = $_POST["stkm_id"];
    $findingID      = $_POST["findingID"];
    $current_user   = "user_function_not_made";


    $msg = "Not set";
    if($findingID==0){//NSTR
        $msg = "Clear results";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NULL, res_update_user=NULL, findingID=NULL
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("ss", $BIN_CODE, $stkm_id);
    }else if($findingID==14){//NSTR
        $msg = "NSTR";
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NOW(), res_update_user=?, findingID=?
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("ssss", $current_user, $findingID, $BIN_CODE, $stkm_id);
    }else if($findingID==15){//There are extra stockcodes in the bin
        $stmt   = $con->prepare("   UPDATE smartdb.sm18_impairment 
                                    SET res_create_date=NULL, res_update_user=NULL, findingID=?
                                    WHERE BIN_CODE=? AND isType='b2r' AND stkm_id=?");
        $stmt   ->bind_param("sss", $findingID, $BIN_CODE, $stkm_id);
    }
    $stmt   ->execute();

    echo $msg;


}elseif ($act=='save_b2r_extra') {
    $BIN_CODE       = $_POST["BIN_CODE"];
    $stkm_id        = $_POST["stkm_id"];
    $current_user   = "user_function_not_made";

    $stmt   = $con->prepare("
    INSERT INTO smartdb.sm18_impairment (
        stkm_id, 
        isID,
        BIN_CODE, 
        DSTRCT_CODE, 
        WHOUSE_ID, 
        isType, 
        res_parent_storageID,
        data_source,
        res_update_user,
        res_create_date
    ) SELECT
        stkm_id, 
        isID, 
        BIN_CODE, 
        DSTRCT_CODE, 
        WHOUSE_ID, 
        isType, 
        storageID, 
        'extra',
        ?, 
        NOW()
    FROM smartdb.sm18_impairment
    WHERE 
        BIN_CODE=? AND 
        stkm_id=? AND 
        data_source='skeleton'");
    $stmt   ->bind_param("sss",  $current_user, $BIN_CODE, $stkm_id);
    $stmt   ->execute();
    if(! $con->error){
        
        $result = ["auto_storageID" => $con->insert_id, "stkm_id" => $stkm_id, "BIN_CODE" => $BIN_CODE];
        echo json_encode($result);
    }  
    //update parent bin status
    updateB2RBinStatus($con, $stkm_id ,  $BIN_CODE, null);
    
    
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
    
    if ($res_asset_id=="firstfound") {
         $assetImageName	= $res_fingerprint;
    }else{
         $assetImageName	= $res_asset_id;
    }
    
    $assetImages = [];
    
    foreach ($a as $key => $existImageName) {
        
        $name_parts = explode("_",$existImageName);
        
        if ($name_parts[0] == $assetImageName) {
            array_push($assetImages,  $existImageName);
        }
    }
    
    echo json_encode($assetImages);
    
}elseif ($act=='ga_create_template') {
    $ass_id = $_POST["ass_id"];

    $sql = "
    INSERT INTO smartdb.sm14_ass (
        stkm_id,
        stk_include,
        rr_id,
        sto_asset_id,
        sto_assetdesc1,
        sto_assetdesc2,
        sto_assettext,
        sto_class,
        sto_class_name,
        sto_class_ga_cat,
        sto_loc_location,
        sto_loc_room,
        sto_loc_state,
        sto_quantity,
        sto_val_nbv,
        sto_val_acq,
        sto_val_orig,
        sto_val_scrap,
        sto_valuation_method,
        sto_ccc,
        sto_ccc_name,
        sto_ccc_grandparent,
        sto_ccc_grandparentname,
        sto_wbs,
        sto_fund,
        sto_responsible_ccc,
        sto_mfr,
        sto_inventory,
        sto_inventno,
        sto_serialno,
        sto_site_no,
        sto_grpcustod,
        sto_plateno,
        sto_revodep,
        sto_date_lastinv,
        sto_date_cap,
        sto_date_pl_ret,
        sto_date_deact,
        sto_loc_latitude,
        sto_loc_longitude,
        genesis_cat,
        res_create_date,
        res_create_user,
        res_reason_code,
        res_rc_desc,
        res_comment,
        res_asset_id,
        res_assetdesc1,
        res_assetdesc2,
        res_assettext,
        res_class,
        res_class_name,
        res_class_ga_cat,
        res_loc_location,
        res_loc_room,
        res_loc_state,
        res_quantity,
        res_val_nbv,
        res_val_acq,
        res_val_orig,
        res_val_scrap,
        res_valuation_method,
        res_ccc,
        res_ccc_name,
        res_ccc_grandparent,
        res_ccc_grandparent_name,
        res_wbs,
        res_fund,
        res_responsible_ccc,
        res_mfr,
        res_inventory,
        res_site_no,
        res_grpcustod,
        res_plateno,
        res_revodep,
        res_date_lastinv,
        res_date_cap,
        res_date_pl_ret,
        res_date_deact,
        res_loc_latitude,
        res_loc_longitude
    )
    SELECT 
        stkm_id,
        stk_include,
        rr_id,
        sto_asset_id,
        sto_assetdesc1,
        sto_assetdesc2,
        sto_assettext,
        sto_class,
        sto_class_name,
        sto_class_ga_cat,
        sto_loc_location,
        sto_loc_room,
        sto_loc_state,
        sto_quantity,
        sto_val_nbv,
        sto_val_acq,
        sto_val_orig,
        sto_val_scrap,
        sto_valuation_method,
        sto_ccc,
        sto_ccc_name,
        sto_ccc_grandparent,
        sto_ccc_grandparentname,
        sto_wbs,
        sto_fund,
        sto_responsible_ccc,
        sto_mfr,
        sto_inventory,
        sto_inventno,
        sto_serialno,
        sto_site_no,
        sto_grpcustod,
        sto_plateno,
        sto_revodep,
        sto_date_lastinv,
        sto_date_cap,
        sto_date_pl_ret,
        sto_date_deact,
        sto_loc_latitude,
        sto_loc_longitude,
        'ga_template',
        res_create_date,
        res_create_user,
        res_reason_code,
        res_rc_desc,
        res_comment,
        'firstfound',
        res_assetdesc1,
        res_assetdesc2,
        res_assettext,
        res_class,
        res_class_name,
        res_class_ga_cat,
        res_loc_location,
        res_loc_room,
        res_loc_state,
        res_quantity,
        res_val_nbv,
        res_val_acq,
        res_val_orig,
        res_val_scrap,
        res_valuation_method,
        res_ccc,
        res_ccc_name,
        res_ccc_grandparent,
        res_ccc_grandparent_name,
        res_wbs,
        res_fund,
        res_responsible_ccc,
        res_mfr,
        res_inventory,
        res_site_no,
        res_grpcustod,
        res_plateno,
        res_revodep,
        res_date_lastinv,
        res_date_cap,
        res_date_pl_ret,
        res_date_deact,
        res_loc_latitude,
        res_loc_longitude 
    FROM smartdb.sm14_ass
    WHERE ass_id =$ass_id ;";

    $con->query($sql);
    if(! $con->error){
        
        $result = ["ass_id" => $con->insert_id]; 
        echo json_encode($result);
    }    
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

}elseif ($act=='check_available_software_version'){
    execWithErrorHandler(function() use ($con, $request){
        checkAvailableSoftwareVersion();       
        $result = ["test_results" => 1];
        echo json_encode(new ResponseMessage("OK", $result));
    });    
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
                    date(delete_date) IS NULL
                ) AND 
                isBackup=0 AND 
                LEFT(isType,3)='imp' AND 
                date(delete_date) IS NULL 
            GROUP BY DSTRCT_CODE, WHOUSE_ID";
    return qget($sql);
}

function getIsImpairments() {
    $sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND ((delete_date IS NULL) OR (date(delete_date)='0000-00-00'))";
    $sql  = "
    		SELECT * 
    		FROM smartdb.sm18_impairment  
    		WHERE stkm_id IN ($sqlInclude ) ";
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

function updateB2RBinStatusByChildId($connection, $childStorageId, $findingCode){
	$rec= qget("select 
					stkm_id, 
					BIN_CODE 
				FROM smartdb.sm18_impairment 
				WHERE auto_storageID=$childStorageId");
	if(count($rec)>0){
		updateB2RBinStatus($connection,$rec[0]["stkm_id"],$rec[0]["BIN_CODE"],$findingCode);
	}			
}

function updateB2RBinStatus ($connection, $stkId, $binId, $findingCode) {
	$refs=array("NSTR"=>14,"TBA"=>15, "INV"=>16);
	$status=null;
	if($findingCode) {
		$status=$refs[$findingCode];
	}else{
		$bin=getB2RBinRecord($stkId, $binId);
		if(count($bin)>0){
			$status=$bin[0]["findingID"];
			if ($bin[0]["extra_total"]>0) {
				if($bin[0]["extra_total"]==$bin[0]["extra_complete"]) {
					$status=$refs["INV"];				
				}elseif ($bin[0]["extra_incomplete"] > 0){
					$status=$refs["TBA"];
				}
			}		
		}
	}
	$sql=" update smartdb.sm18_impairment
		   SET	findingID=? 
		   where 
				bin_code= ? AND 
				stkm_id= ? AND 
				data_source = 'skeleton'";
	$stmt = $connection->prepare($sql);

    $stmt->bind_param("sss", $status, $binId, $stkId);        
    $stmt->execute();
}
function countIsNotYetCompleteItems($stkm_id) {
    $sql = "
    SELECT count(*) as NotYetCompleteItems
    FROM smartdb.sm18_impairment rec 
    WHERE (rec.stkm_id = $stkm_id) AND (
        ((rec.isType='b2r') AND (rec.data_source='skeleton') AND (rec.findingID=15)) OR 
        ((rec.findingID in ( 
                SELECT findingID 
                FROM smartdb.sm19_result_cats 
                WHERE 
                    isType like 'imp%' AND resAbbr in ('USWD','USND')
          ) AND rec.checked_to_milis=0)) OR 
        (rec.findingID=13)
     )
    ";
    return qget($sql);
}
function getB2RBinRecord($stkm_id, $BIN_CODE) {
  
    $sql = "
		select 
			r1.*, 
			r2.extra_complete,
			r2.extra_incomplete,
			r2.extra_total
		from 
		(
			select *
			from smartdb.sm18_impairment
			where 
				bin_code= '$BIN_CODE' AND 
				stkm_id= $stkm_id AND 
				data_source = 'skeleton'
		) as r1
		left join
		(
			select 
				stkm_id,
				bin_code,
				sum(CASE WHEN finalResult IS NOT NULL THEN 1 ELSE 0 END) as extra_complete,
				sum(CASE WHEN finalResult IS NULL THEN 1 ELSE 0 END) as extra_incomplete,
				count(*) as extra_total
			from smartdb.sm18_impairment
			where 
				bin_code='$BIN_CODE' AND 
				stkm_id= $stkm_id AND 
				data_source = 'extra'
		) as r2 
		on (r1.stkm_id=r2.stkm_id AND r1.bin_code=r2.bin_code)";
   
    return qget($sql);
}

function getIsRecords (){
	global $isImpAbbrsWithMilisEnabled;
	$impMilisFindingIDs=getFindingIDsString("imp%",$isImpAbbrsWithMilisEnabled);

    $sqlInclude = "
    	SELECT stkm_id 
    	FROM smartdb.sm13_stk 
    	WHERE stk_include=1 
    		AND ((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))";
 
    $sqlimp  = "
    	SELECT 
    		i0.*, 
    		(
	    		CASE WHEN (
	    				(i0.findingID=11) 
	    				AND (
	    						(   
	    						SELECT count(*) 
	    						FROM smartdb.sm18_impairment i1 
	    						WHERE 
	    							i1.res_parent_storageID=i0.storageID
	    							AND i1.findingID in ($impMilisFindingIDs)
	    							AND i1.checked_to_milis=0
	    						) = 0
	    				)
	    			)
	    			THEN 1
	    			ELSE 0
	    		END	    		
    		) AS isComplete 
    	FROM smartdb.sm18_impairment i0 
    	WHERE stkm_id IN ($sqlInclude ) 
    		AND ( LEFT(i0.isType,3) = 'imp') 
    		AND ((i0.isBackup IS NULL) OR (i0.isBackup=0))
  			AND (i0.data_source <> 'extra')";
    		
    $sqlb2r  = "
    	SELECT *, 1 AS isComplete 
    	FROM smartdb.sm18_impairment
    	WHERE stkm_id IN ($sqlInclude ) 
    		AND (isType = 'b2r') 
    		AND ((isBackup IS NULL) OR (isBackup=0)) 
    		AND data_source='skeleton'";
  
    $sql = $sqlimp." UNION ALL ".$sqlb2r;

    
    return qget($sql);
}

function getActivities() {
	$abbrs=["SER","USWD","USND","NIC","SPLT"];
	$milisAbbrs=["USWD","USND"];
	$b2rAbbrs=["INV","NSTR"];
	$impMilisFindingIDs=getFindingIDsString("imp%",$milisAbbrs);
	$impCompletedFindingIDs=getFindingIDsString("imp%",$abbrs);	
	$b2rCompletedFindingIDs=getFindingIDsString("b2r",$b2rAbbrs);	
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
	            SUM(CASE WHEN ( (data_source='skeleton') AND 
	            				((isBackup is NULL) OR (isBackup=0))
	            			) 
	            		THEN 1 
	            		ELSE 0 
	            		END
	            ) AS rc_orig,
	            SUM(CASE WHEN (	(data_source = 'extra') AND 
	            				((isBackup is NULL) OR (isBackup=0))
	            			) 
	            		 THEN 1 
	            		 ELSE 0 
	            	END
	            ) AS rc_extras,
	            SUM(CASE WHEN (	(findingID in ($b2rCompletedFindingIDs)) AND 
	            				(data_source = 'skeleton') AND
	            				((isBackup is NULL) OR (isBackup=0))
	            			) 
	            		THEN 1 
	            		ELSE 0 
	            		END
	            ) AS rc_orig_complete,
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
	            SUM(
	            	CASE WHEN ((data_source <> 'extra') AND 
	            			   ((isBackup is NULL) OR (isBackup=0))
	            			   ) 
	            		THEN 1 
	            		ELSE 0 
	            		END
	            ) AS rc_orig,
	            SUM(CASE WHEN ( (data_source ='extra') AND 
	            				((isBackup is NULL) OR (isBackup=0))
	            			) 
	            		THEN 1 
	            		ELSE 0 
	            		END
	            ) AS rc_extras,
	            SUM(CASE WHEN (findingID in ($impCompletedFindingIDs)) AND 
	            				(data_source <> 'extra') AND 
	            				((isBackup is NULL) OR (isBackup=0))           			
	            		THEN (
	            			CASE WHEN (
	            						(findingID=11) 
	            						AND ((select sum(i.SOH) 
	            							 from  smartdb.sm18_impairment i 
	            							 where i.res_parent_storageID=b.storageID) >= b.SOH)
	            						AND (
					    						(SELECT count(*) 
					    						FROM smartdb.sm18_impairment i1 
					    						WHERE 
					    							i1.res_parent_storageID=b.storageID
					    							AND i1.findingID in ($impMilisFindingIDs)
					    							AND i1.checked_to_milis=0
					    						) > 0
	    								)	 
	            				 )
	            				 	THEN 0
	            			     WHEN ((findingID in ($impMilisFindingIDs)) AND (checked_to_milis<>1))
	            				 	THEN 0 
	            				 ELSE 1 
	            			END
	            		) 
	            		ELSE 0 
	            	END
	            ) AS rc_orig_complete,
	            COUNT(*) AS rc_totalsent
	        FROM smartdb.sm18_impairment b
            WHERE ((date(delete_date) IS NULL) or (date(delete_date)='0000-00-00'))
                  AND isType like 'imp%'
	        GROUP BY stkm_id        
        )
    ) as asset
    ON act.stkm_id=asset.stkm_id;";
    echo json_encode(new ResponseMessage("OK", qget($sql)));
}

function resetData($excludedRR){
    global $con, $dbname, $hostname, $username, $password;

    
    if($excludedRR){
        //Delete all tables except for RR
        $con->query("DROP TABLE $dbname.sm10_set, $dbname.sm11_pro, $dbname.sm13_stk, $dbname.sm14_ass, $dbname.sm15_rc, $dbname.sm16_file, $dbname.sm17_history, $dbname.sm18_impairment, $dbname.sm19_result_cats, $dbname.sm20_quarantine;");
        if($con->error){
            throw new Exception($con->error);
        }
        fnInitiateDatabase(true, true);
    }else{
	    $con->query("DROP DATABASE $dbname;"); 
	    if($con->error){
	 	    throw new Exception($con->error);
	    }
	 
	    $con->query("CREATE DATABASE $dbname;");
	    if($con->error){
	 	    throw new Exception($con->error);
	    }

        $con=new mysqli($hostname, $username, $password, $dbname);
        if ($con->connect_error) {
            throw new Exception($con->connect_error);
        }         	
        fnInitiateDatabase(true, false);
    }
    
    return $excludedRR;
}

function checkAvailableSoftwareVersion(){
    $versionInfo=getSoftwareVersion();
    $softwareLocalVersion=$versionInfo['localVersion'];
    $softwareLocalRevision=$versionInfo['localRevision'];
    $softwareRemoteVersion=$versionInfo['remoteVersion'];
    $softwareRemoteRevision=$versionInfo['remoteRevision'];
    
    $sql_save = "UPDATE smartdb.sm10_set 
                 SET date_last_update_check=NOW(), versionLocal=$softwareLocalVersion,
                     versionLocalRevision='$softwareLocalRevision',
                     versionRemote=$softwareRemoteVersion,
                     versionRemoteRevision='$softwareRemoteRevision'
                WHERE smartm_id=1; ";
    qget($sql_save);
}

function updateSoftware() {
	$networkStatus=getNetworkStatus();  

    $errCode=0;
    $errMsg="";

    if ($networkStatus == NET_NO_INTERNET){
		throw new Exception("Device is not connected to internet", $networkStatus);
	}	

    if ($networkStatus == NET_NO_SERVICE){
		throw new Exception("Software update repository is not available", $networkStatus);
	}
	
	$servername = "";
	$username   = "root";
	$password   = "";
	$output=[];

	
	splitLines($output, shell_exec(GIT_CMD .' init 2>&1')); 
	splitLines($output, shell_exec(GIT_CMD .' clean  -d  -f .'));
	splitLines($output, shell_exec(GIT_CMD .' reset --hard'));
	if($networkStatus == NET_HTTP_PROXY){
  		splitLines($output, shell_exec(GIT_CMD .' config http.proxy http://' . HTTP_PROXY));
	}else{
	    splitLines($output, shell_exec(GIT_CMD .' config --unset http.proxy'));
	}

	splitLines($output, shell_exec(GIT_CMD .' pull https://github.com/usumai/110_smart.git'));

	$revision=shell_exec(GIT_CMD .' rev-parse --short HEAD');

	$result = ["info" => $output, "revision" => $revision];

	qget("DROP DATABASE smartdb");
	
	return $result;
}

function splitLines(&$entries, $outputText){
	foreach(explode("\n",$outputText) as $line){
		$entries[]=$line;
	}
}
?>