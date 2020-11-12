<?php


function createIsAudit($connection, $record) {

    $stmt   = $connection->prepare(
        "INSERT INTO smartdb.sm13_stk (
            stk_id,
            stk_name,
            dpn_extract_date,
            dpn_extract_user,
            smm_extract_date,
            smm_extract_user,
            rc_orig,
            rc_orig_complete, 
            rc_extras, 
            stk_type) 
        VALUES(?,?,?,?,?,?,?,?,?,?);");

    $stmt->bind_param("ssssssssss", 
		$record->stk_id, 
		$record->stk_name, 
		$record->dpn_extract_date, 
		$record->dpn_extract_user, 
		$record->smm_extract_date, 
		$record->smm_extract_user, 
		$record->rc_orig, 
		$record->rc_orig_complete, 
		$record->rc_extras,
		$record->type);            
    $stmt->execute();
    if($stmt->error){
		$errorMsg = $stmt->error;
		$stmt->close();
		throw new Exception($errorMsg);
	}
    $id= $stmt->insert_id;
    $stmt->close();
    return $id;    
}

function createIsImpairments($connection, $stocktakeId, $impairments) { 
	if((! $stocktakeId) || ($stocktakeId=='')) {
		throw new Exception("Unable to create Impairment records, required stocktake id");
	}
	if(! $impairments){
		return;
	}
    $stmt   = $connection->prepare(
		"INSERT INTO smartdb.sm18_impairment (
        	stkm_id, 
			storageID, 
			DSTRCT_CODE, 
			WHOUSE_ID, 
			BIN_CODE,
			STOCK_CODE,
			ITEM_NAME,
			STK_DESC,
       		SUPPLY_CUST_ID,
			SC_ACCOUNT_TYPE,
			SUPPLY_ACCT_METH, 
			INVENT_CAT,
			INVENT_CAT_DESC,
			TRACKING_IND,
			SOH, 
        	TRACKING_REFERENCE,
			LAST_MOD_DATE,
			sampleFlag,
			serviceableFlag,			
			checked_to_milis,			
			findingID,
			isID,			 
			targetID,
			targetItemID,
			isType,
			isBackup,   
			res_comment,
			res_evidence_desc,
			res_unserv_date, 
			res_parent_storageID,
			res_create_date,			
			res_create_user, 
        	res_update_user,
			finalResult,
			finalResultPath,
			fingerprint,
			data_source,
			extract_date,
			create_date,
			create_user,
			delete_date,
			delete_user) 
		VALUES (?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?);");

    foreach( $impairments as $record) {
		$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssss", 
			$stocktakeId,
			$record->storage_id, 
			$record->DSTRCT_CODE, 
			$record->WHOUSE_ID, 
			$record->BIN_CODE,
			$record->STOCK_CODE,
			$record->ITEM_NAME,
			$record->STK_DESC,
			$record->SUPPLY_CUST_ID,
			$record->SC_ACCOUNT_TYPE,
			$record->SUPPLY_ACCT_METH, 
			$record->INVENT_CAT,
			$record->INVENT_CAT_DESC,
			$record->TRACKING_IND,
			$record->SOH, 
			$record->TRACKING_REFERENCE,
			$record->LAST_MOD_DATE,
			$record->sampleFlag,
			$record->serviceableFlag,
			$record->checked_to_milis,
			$record->findingID,
			$record->isID,
			$record->targetID,
			$record->targetItemID,
			$record->actType,
			$record->isBackup,   
			$record->res_comment,
			$record->res_evidence_desc,
			$record->res_unserv_date, 
			$record->res_parent_storageID,
			$record->res_create_date,
			$record->res_create_user, 
			$record->res_update_user,
			$record->finalResult,
			$record->finalResultPath,
			$record->fingerprint,
			$record->data_source,
			$record->extract_date,
			$record->create_date,
			$record->create_user,
			$record->delete_date,
			$record->delete_user);		
		$stmt->execute();
		if($stmt->error){
			$errorMsg = $stmt->error;
			$stmt->close();
			throw new Exception($errorMsg);
		}
  	}
	$stmt->close();
}

function createGaStocktake($connection, $record) {

    $stmt   = $connection->prepare(
        "INSERT INTO smartdb.sm13_stk (
			stk_id, 
			stk_name, 
			stk_type, 
			dpn_extract_date, 
			dpn_extract_user, 
			smm_extract_date, 
			smm_extract_user, 
			rc_orig, 
			rc_orig_complete, 
			rc_extras) 
		VALUES(?,?,?,?,?,?,?,?,?,?);");
    $stmt->bind_param("ssssssssss", 
		$record->stk_id, 
		$record->stk_name, 
		$record->type, 
		$record->dpn_extract_date, 
		$record->dpn_extract_user, 
		$record->smm_extract_date, 
		$record->smm_extract_user, 
		$record->rc_orig, 
		$record->rc_orig_complete, 
		$record->rc_extras);
    $stmt->execute();
    if($stmt->error){
		$errorMsg = $stmt->error;
		$stmt->close();
		throw new Exception($errorMsg);
	}    
    $id=$stmt->insert_id;
    $stmt->close();
    return $id;
}
function createGaAssets($connection, $stocktakeId, $assets) {
	if((! $stocktakeId) || ($stocktakeId=='')) {
		throw new Exception("Unable to create Asset records, required stocktake id");
	}
	if(! $assets){
		return;
	}
	$stmt   = $connection->prepare(
		"INSERT INTO smartdb.sm14_ass (
			create_date,
			create_user,
			stkm_id,
			ledger_id,
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
			sto_date_lastinv, 
			sto_date_cap,
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
			res_ccc, 
			res_ccc_name, 
			res_ccc_parent, 
			res_ccc_parent_name, 
			res_wbs, 
			res_fund, 
			res_responsible_ccc, 
			res_mfr, 
			res_inventory, 
			res_inventno, 
			res_serialno, 
			res_site_no, 
			res_grpcustod, 
			res_plateno, 
			res_date_lastinv, 
			res_date_cap, 
			res_loc_latitude, 
			res_loc_longitude
		) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
	
	foreach ($assets as $row) {
		$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss", 
			$row->create_date,
			$row->create_user,
			$stocktakeId, 
			$row->ledger_id,
			$row->rr_id,
			$row->sto_asset_id, 
			$row->sto_assetdesc1, 
			$row->sto_assetdesc2, 
			$row->sto_assettext, 
			$row->sto_class, 
			$row->sto_class_ga_cat, 
			$row->sto_loc_location, 
			$row->sto_loc_room, 
			$row->sto_loc_state,  
			$row->sto_quantity, 
			$row->sto_val_nbv, 
			$row->sto_val_acq, 
			$row->sto_val_orig, 
			$row->sto_val_scrap, 
			$row->sto_valuation_method, 
			$row->sto_ccc, 
			$row->sto_ccc_name, 
			$row->sto_ccc_parent, 
			$row->sto_ccc_parent_name, 
			$row->sto_wbs, 
			$row->sto_fund, 
			$row->sto_responsible_ccc, 
			$row->sto_mfr, 
			$row->sto_inventory, 
			$row->sto_inventno, 
			$row->sto_serialno, 
			$row->sto_site_no, 
			$row->sto_grpcustod, 
			$row->sto_plateno, 
			$row->sto_date_lastinv, 
			$row->sto_date_cap, 
			$row->sto_loc_latitude, 
			$row->sto_loc_longitude,
			$row->genesis_cat, 
			$row->res_create_date, 
			$row->res_create_user, 
			$row->res_fingerprint, 
			$row->res_reason_code, 
			$row->res_rc_desc, 
			$row->res_comment,
			$row->res_asset_id, 
			$row->res_assetdesc1, 
			$row->res_assetdesc2, 
			$row->res_assettext, 
			$row->res_class, 
			$row->res_class_ga_cat, 
			$row->res_loc_location, 
			$row->res_loc_room, 
			$row->res_loc_state,  
			$row->res_quantity, 
			$row->res_val_nbv, 
			$row->res_val_acq, 
			$row->res_val_orig, 
			$row->res_val_scrap, 
			$row->res_valuation_method, 
			$row->res_ccc, 
			$row->res_ccc_name, 
			$row->res_ccc_parent, 
			$row->res_ccc_parent_name, 
			$row->res_wbs, 
			$row->res_fund, 
			$row->res_responsible_ccc, 
			$row->res_mfr, 
			$row->res_inventory, 
			$row->res_inventno, 
			$row->res_serialno, 
			$row->res_site_no, 
			$row->res_grpcustod, 
			$row->res_plateno, 
			$row->res_date_lastinv, 
			$row->res_date_cap, 
			$row->res_loc_latitude, 
			$row->res_loc_longitude);

		$stmt->execute();
		if($stmt->error){
			$errorMsg = $stmt->error;
			$stmt->close();
			throw new Exception($errorMsg);
		}	
	}
	$stmt->close();
}
?>