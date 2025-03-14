<?php


function addFile($connection, $record) {

    $stmt   = $connection->prepare(
        "INSERT INTO smartdb.sm16_File (
            file_name,
            file_type,
            file_ref,
            format_version,
            file_desc,
            import_date
        ) 
        VALUES(?,?,?,?,?,?);");

    $stmt->bind_param("ssssss", 
		$record->file_name, 
		$record->file_type, 
        $record->file_ref,
        $record->format_version,
		$record->file_desc, 
		$record->import_date
		 
	);            
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
			UUID,
			data_source,
			extract_date,
			create_date,
			create_user,
			delete_date,
			delete_user,
            modify_date,
            modify_user,
            version) 
		VALUES (?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?,?,?,?,?,?,
				?,?,?,?,?);");

    foreach( $impairments as $record) {
    	$createDate = DateTime::createFromFormat("d-m-Y h:i:s A", str_replace('/','-', $record->create_date));
    	$createDate = ($createDate ? $createDate->format("Y-m-d H:i:s") : "0000-00-00 00:00:00");
    	if(isset($record->modify_date)) {
    		$modifyDate = DateTime::createFromFormat("d-m-Y h:i:s A", str_replace('/','-', $record->modify_date));
    	 	$modifyDate = ($modifyDate ? $modifyDate->format("Y-m-d H:i:s") : "0000-00-00 00:00:00");
    	}
    	
		$modDate = DateTime::createFromFormat("d-m-Y", str_replace('/','-', $record->last_mod_date));
		$modDate = ($modDate ? $modDate->format("Y-m-d") : "0000-00-00");
		
		$extractDate=DateTime::createFromFormat("d-m-Y", str_replace('/','-', $record->ExtractDate));
		$extractDate= $extractDate ? $extractDate->format("Y-m-d") : "0000-00-00";
		
		$stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssss", 
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
			$modDate,
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
			$record->res_parent_storage_id,
			$record->res_create_date,
			$record->res_create_user, 
			$record->res_update_user,
			$record->finalResult,
			$record->finalResultPath,
			$record->UUID,
			$record->data_source,
			$extractDate,
			$createDate,
			$record->create_user,
			$record->delete_date,
			$record->delete_user,
		    $modifyDate,
		    $record->modify_user,
		    $record->version);		
		$stmt->execute();
		if($stmt->error){
			$errorMsg = $stmt->error;
			$stmt->close();
			throw new Exception($errorMsg);
		}
  	}
	$stmt->close();
}

function clearGaRawRemainder($connection){
    $sql = "TRUNCATE TABLE smartdb.sm12_rwr; ";
    mysqli_multi_query($connection, $sql);
    
    $sql = "TRUNCATE TABLE smartdb.sm16_file; ";
    mysqli_multi_query($connection,$sql);
}

function createGaAbbrs($connection, $abbrList){
   
    $sql = "
        INSERT INTO smartdb.sm16_file (
            file_type,
            file_ref,
            file_desc) 
        VALUES (?,?,?)";
    
    $stmt = $connection->prepare($sql);

    foreach ($abbrList as $record) {
       
        $stmt->bind_param("sss",
            $record->file_type,
            $record->file_ref,
            $record->file_desc);
        
        $stmt->execute();
        
        if($stmt->error){
            $errorMsg = $stmt->error;
            $stmt->close();
            throw new Exception($errorMsg);
        }	
    }
    $stmt->close();
}

function createGaRawRemainders($connection, $rrList){

    $sql="  INSERT INTO smartdb.sm12_rwr (
                Asset,
                accNo,
                InventNo,
                AssetDesc1,
                Class,
                ParentName) 
            VALUES (?,?,?,?,
                    (SELECT
                        file_desc
                    FROM
                        smartdb.sm16_file
                    WHERE
                        file_type='abbrev_class'
                        AND file_ref = ?
                    ),
                    (SELECT
                        file_desc
                    FROM
                        smartdb.sm16_file
                    WHERE
                        file_type='abbrev_owner'
                        AND file_ref = ?
                    )
            )";
    
    $stmt = $connection->prepare($sql);
    
    
    foreach ($rrList as $record) {
        
        if ($record->f1 != "END") {
            
            $parent=substr($record->f1,0,1);
            $class= substr($record->f1,1,1);
            $asset = substr($record->f1,2);
            $accNo     = $record->f2;
            $inventNo  = $record->f3;
            $assetDesc = $record->f4;
            
            $stmt->bind_param("ssssss", 
                    $asset, 
                    $accNo, 
                    $inventNo, 
                    $assetDesc,
                    $class,
                    $parent);
            
            $stmt->execute();       
            
            if($stmt->error){
                $errorMsg = $stmt->error;
                $stmt->close();
                throw new Exception($errorMsg);
            }	
        }
    }
    
    $stmt->close();
  
}

function updateGaRawRemainderAbbr($connection){
    
    // Update the RR with the updated abbreviations
    
    $sql = "
        UPDATE smartdb.sm12_rwr r
        SET ParentName= (SELECT
                            file_desc
                        FROM
                            smartdb.sm16_file
                        WHERE
                            file_type='abbrev_owner'
                            AND file_ref = SUBSTRING(r.Asset,1,1)
            ),
            Class =     (SELECT
                            file_desc
                        FROM
                            smartdb.sm16_file
                        WHERE
                            file_type='abbrev_class'
                            AND file_ref = SUBSTRING(r.Asset,2,1)
            ),
            Asset = SUBSTRING(r.Asset,3)";
    
    mysqli_multi_query($connection, $sql);
}

function updateSettings($connection, $record){
    $sql = "
        UPDATE smartdb.sm10_set
        SET rr_extract_date='$record->rr_extract_date',
            rr_extract_user='$record->rr_extract_user',
            rr_count = $record->rr_count
        WHERE smartm_id = $record->smartm_id; ";
    
    mysqli_multi_query($connection, $sql);
}

function createStocktakeActivity($connection, $record) {
    
    $file_id=addFile($connection, $record);
    
    $stk_id=$record->stk_id;

    $results=qget("SELECT stkm_id FROM smartdb.sm13_stk WHERE (stk_id= $stk_id) and ((delete_date is null) || (date(delete_date)='0000-00-00'))");
    if(count($results)>0){
        $id=$results[0]['stkm_id'];
    }else{
    
        $stmt   = $connection->prepare(
            "INSERT INTO smartdb.sm13_stk (
    			stk_id, 
    			stk_name, 
    			stk_type, 
                file_id,
    			dpn_extract_date, 
    			dpn_extract_user, 
    			rc_orig) 
    		VALUES(?,?,?,?,?,?,?);");
        
        $stmt->bind_param("sssssss", 
    		$record->stk_id, 
    		$record->stk_name, 
    		$record->type, 
            $file_id,
    		$record->dpn_extract_date, 
    		$record->dpn_extract_user, 
     		$record->rc_orig);
        
        $stmt->execute();
        
        if($stmt->error){
    		$errorMsg = $stmt->error;
    		$stmt->close();
    		throw new Exception($errorMsg);
    	}    
        $id=$stmt->insert_id;
	
        $stmt->close();
    }
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
			sto_type_name, 
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
			res_ccc_grandparent, 
			res_ccc_grandparent_name, 
			res_wbs, 
			res_fund, 
			res_responsible_ccc, 
			res_mfr, 
			res_inventory, 
			res_inventno, 
			res_serialno, 
			res_site_no, 
			res_grpcustod, 
			res_type_name,
			res_plateno, 
			res_date_lastinv, 
			res_date_cap, 
			res_loc_latitude, 
			res_loc_longitude,
			create_user,
			create_date,
            delete_user,
            delete_date,
            modify_user,
            modify_date,
            version
		) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
	
	foreach ($assets as $row) {
		$stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss", 
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
			$row->sto_ccc_grandparent, 
			$row->sto_ccc_grandparentname, 
			$row->sto_wbs, 
			$row->sto_fund, 
			$row->sto_responsible_ccc, 
			$row->sto_mfr, 
			$row->sto_inventory, 
			$row->sto_inventno, 
			$row->sto_serialno, 
			$row->sto_site_no, 
			$row->sto_grpcustod, 
			$row->sto_type_name, 
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
			$row->res_ccc_grandparent, 
			$row->res_ccc_grandparent_name, 
			$row->res_wbs, 
			$row->res_fund, 
			$row->res_responsible_ccc, 
			$row->res_mfr, 
			$row->res_inventory, 
			$row->res_inventno, 
			$row->res_serialno, 
			$row->res_site_no, 
			$row->res_grpcustod, 
			$row->res_type_name, 
			$row->res_plateno, 
			$row->res_date_lastinv, 
			$row->res_date_cap, 
			$row->res_loc_latitude, 
		    $row->res_loc_longitude,   				    
		    $row->create_user,
		    $row->create_date,
		    $row->delete_user,
		    $row->delete_date,
		    $row->modify_user,
		    $row->modify_date,
		    $row->version);
		try{
		    $stmt->execute();
		}catch(Exception $e){

	        if(($stmt->sqlstate == DB_ERR_STATE) 
	            && ($stmt->errno == DB_ERR_RECORD_EXIST)){
                    ;
	        }else{
	            $stmt->close();
	            throw $e;
	        }	    
		}
	}

	qget("
        DELETE  d1 
        FROM    smartdb.sm14_ass d1 
            JOIN smartdb.sm14_ass d2 
            ON  (d1.ass_id = d2.duplicate) 
                AND (d2.stkm_id=$stocktakeId) 
                AND (d2.duplicate > -1)");
	/*	
	qget("DELETE FROM smartdb.sm14_ass 
          WHERE ass_id in (
            SELECT duplicate 
            FROM smartdb.sm14_ass 
            WHERE stkm_id=$stocktakeId AND duplicate > -1)");

	qget("UPDATE smartdb.sm14_ass 
          SET duplicate = -1
          WHERE duplicate >= 0");
	*/          
	$stmt->close();
}

function importGaImages($activityID){
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK
        && is_uploaded_file($_FILES['file']['tmp_name'])) {
            
            $fileName=  $_FILES['file']['name'];
            $filePath = 'images/' . $fileName;
                        
            if (!file_exists($filePath)) {
                file_put_contents($filePath, file_get_contents($_FILES['file']['tmp_name']));
            }
        }
}
?>