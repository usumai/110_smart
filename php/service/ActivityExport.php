<?php 

function backupExportJson(){

    if ($_FILES['file']['error'] == UPLOAD_ERR_OK               
        && is_uploaded_file($_FILES['file']['tmp_name'])) { 
            
            $fileName=  $_FILES['file']['name'];
            $filePath = 'backup/' . $fileName;
/*
            $counter = 0;
            while (file_exists($filePath)) {

                $filePath = 'backup/' . $counter . '_' . $fileName;
                $counter++;
            }
*/
            file_put_contents($filePath, file_get_contents($_FILES['file']['tmp_name']));
          
    }
}
function getActivity($activityID){
	return qget("select * from smartdb.sm13_stk where stkm_id=" . $activityID);
}


function exportGaImages($activityID, $filename){
	$actvList=getActivity($activityID);

	if(count($actvList)==0)
		throw new Exception("Activity $activityID not exist",-1);
	$actv=$actvList[0];	
	
	/*echo json_encode(new ResponseMessage("OK",$actv));*/
	
    $assetList=exportGaActivity($activityID);
	
	
    
    $gaJson['file_version']      = 12;
	$gaJson['unique_file_id']    = uniqid('GA_',true);
	$gaJson['rc_totalsent'] =  count($assetList);
    
	$gaJson['stkm_id']           = $actv['stkm_id'];
    $gaJson['stk_id']            =  $actv['stk_id'];
 	$gaJson['stk_name']          =  $actv['stk_name'];
    $gaJson['type']              =  $actv['stk_type'];
    $gaJson['rc_orig']           =  $actv['rc_orig'];
    $gaJson['rc_orig_complete']  =  $actv['rc_orig_complete'];
    $gaJson['rc_extras']         =  $actv['rc_extras'];   
    $gaJson['dpn_extract_date']  =  $actv['dpn_extract_date'];
    $gaJson['dpn_extract_user']  =  $actv['dpn_extract_user'];
    $gaJson['smm_extract_date']  = date(DATE_ATOM);
    $gaJson['smm_extract_user']  = get_current_user();
	$gaJson['asset_lock_date']   = '';
    $gaJson['assetlist']= $assetList;


    


	$zipPath='images/' . $filename;

	$zip = new ZipArchive();
    $status=$zip->open($zipPath, ZIPARCHIVE::CREATE);
    
    if ($status === TRUE) {
		$zip->addFromString('data.json',json_encode($gaJson));
		$zip->addEmptyDir("images");

	   	$sql = " 
	   		SELECT 
	   			ass_id,
	            stkm_id,
	   			ledger_id,  
	   			rr_id, 
	 			res_asset_id,
	            res_fingerprint
	    FROM 	smartdb.sm14_ass 
	    WHERE 	stkm_id = $activityID
	    AND ((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))";

	    $assetList = qget($sql);
		
	    foreach($assetList as $asset){
	        $res_asset_id = $asset["res_asset_id"];
	        $res_fingerprint = $asset["res_fingerprint"];
	        if ($res_asset_id =="firstfound") {
	            $photo_name = "images/".$res_fingerprint;
	        }else{
	            $photo_name = "images/".$res_asset_id;
	        }

	        $original_photo_name = $photo_name;
	        $counter = 1;
	        $photo_name = $photo_name.'_'.$counter.'.jpg';

	        while (file_exists($photo_name)) {
				$zip->addFile($photo_name, $photo_name);
				$counter++;
	            $photo_name = $original_photo_name.'_'.$counter.'.jpg';
	        }
	
	    }        
		
        $zip->close();
		return $zipPath;
    } else {
        throw new Exception("Unable to create zip archive file", -1);
    }
}


function exportGaActivity($activityID){
   	$sql = " 
   		SELECT 
            stkm_id,
   			ass_id, 
   			ledger_id,  
   			0 AS stk_include, 
   			rr_id, 
   			sto_asset_id, 
   			sto_assetdesc1, 
   			sto_assetdesc2,
   			sto_assettext,
   			sto_class,
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
   			res_fingerprint,
   			res_reason_code,
   			res_rc_desc,
   			res_comment,
   			res_asset_id,
   			res_assetdesc1,
   			res_assetdesc2,
   			res_assettext,
   			res_class,
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
   			res_ccc,res_ccc_name,
   			res_ccc_grandparent,
   			res_ccc_grandparent_name,
   			res_wbs,res_fund,
   			res_responsible_ccc,
   			res_mfr,res_inventory,
   			res_inventno,
   			res_serialno,
   			res_site_no,
   			res_grpcustod,
   			res_plateno,
   			res_revodep,
   			res_date_lastinv,
   			res_date_cap,
   			res_date_pl_ret,
   			res_date_deact,
   			res_loc_latitude,
   			res_loc_longitude,
   			create_user, 
   			create_date, 
            delete_user,
   			delete_date,
            modify_user, 
			modify_date,
			version
    FROM 	smartdb.sm14_ass 
    WHERE 	stkm_id = $activityID
    AND ((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))";
    return qget($sql);
}

function exportIsActivity($activityID) {

	$activity=array();
	
	$activityRes=qget(" 
			SELECT * 
			FROM smartdb.sm13_stk
			WHERE 
				stkm_id=$activityID");
					
	if(count($activityRes)>0)
	{
		$activity=$activityRes[0];
		$imps =qget("
    		SELECT 
				imp.auto_storageID,
				imp.stkm_id,
				imp.storageID as storage_id,
				imp.DSTRCT_CODE,
				imp.WHOUSE_ID,
				imp.BIN_CODE,
				imp.STOCK_CODE,
				imp.ITEM_NAME,
				imp.STK_DESC,
				imp.SUPPLY_CUST_ID,
				imp.SC_ACCOUNT_TYPE,
				imp.SUPPLY_ACCT_METH,
				imp.INVENT_CAT,
				imp.INVENT_CAT_DESC,
				imp.TRACKING_IND,
				imp.SOH,
				imp.TRACKING_REFERENCE,
				imp.LAST_MOD_DATE as last_mod_date,
				imp.sampleFlag as sample_flag,
				imp.serviceableFlag,
				imp.checked_to_milis,
				imp.findingID,
				imp.isID,
				imp.targetID,
				imp.targetItemID,
				imp.isType as actType,
				imp.isBackup,
				imp.res_comment,
				imp.res_evidence_desc,
				imp.res_unserv_date,
				imp.res_parent_storageID as res_parent_storage_id,
				imp.res_create_date,
				imp.res_create_user,
				imp.res_update_user,
				imp.finalResult,
				imp.finalResultPath,
				imp.UUID,
				imp.checkFlag,
				imp.data_source,
				imp.extract_date as ExtractDate,
				imp.create_date,
				imp.create_user,
				imp.delete_date,
				imp.delete_user,
                imp.modify_date,
                imp.modify_user,
                imp.version,
				cat.resAbbr
    		FROM 
				smartdb.sm18_impairment imp 
				left join 
				smartdb.sm19_result_cats cat
				on imp.findingID = cast(cat.findingID as CHAR)
    		WHERE 
    			stkm_id=$activityID
				AND ((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))
		");
    			  
    	if(count($imps)>0){
    		$activity["impairments"]=$imps;
    		$activity["rc_totalsent"]=count($imps);
    	}else{
    		$activity["impairments"]=array();
    		$activity["rc_totalsent"]=0;
    	}
    		  
	};
	return $activity;
}
?>