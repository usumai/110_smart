<?php 
function exportGaActivity($activityID){
   	$sql = " 
   		SELECT 
   			ass_id, 
   			stkm_id, 
   			0 AS stk_include, 
   			rr_id, 
   			ledger_id, 
   			create_date, 
   			create_user, 
   			delete_date, 
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
   			sto_ccc_parent,
   			sto_ccc_parent_name,
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
   			res_ccc_parent,
   			res_ccc_parent_name,
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
   			res_loc_longitude
    FROM 	smartdb.sm14_ass 
    WHERE 	stkm_id = $activityID";
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
				imp.fingerprint,
				imp.checkFlag,
				imp.data_source,
				imp.extract_date as ExtractDate,
				imp.create_date,
				imp.create_user,
				imp.delete_date,
				imp.delete_user,
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