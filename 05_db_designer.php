<?php
include "php/common/common.php";

function fnInitiateDatabase(){
    global $con, $dbname,$date_version_published,$log;

    $sql_save = "CREATE TABLE $dbname.sm10_set (
         `smartm_id` INT(11) NOT NULL AUTO_INCREMENT,
         `create_date` DATETIME NULL DEFAULT NULL,
         `delete_date` DATETIME NULL DEFAULT NULL,
         `update_date` DATETIME NULL DEFAULT NULL,
         `active_profile_id` INT NULL DEFAULT NULL,
         `last_access_date` DATETIME NULL,
         `last_access_profile_id` INT(11) NULL,
         `rr_extract_date` DATETIME NULL, 
         `rr_extract_user` VARCHAR(255) NULL DEFAULT NULL,
         `rr_count` INT(11) NULL,
         `journal_id` INT(11) NULL,
         `help_shown` INT(11) NULL,
         `theme_type` INT(11) NULL,
         `versionLocal` INT(11) NULL,
         `versionLocalRevision` VARCHAR(25) NULL,
         `versionRemote` INT(11) NULL,
         `versionRemoteRevision` VARCHAR(25) NULL,
         `date_last_update_check` DATETIME NULL, 
         PRIMARY KEY (`smartm_id`),UNIQUE INDEX `smartm_id_UNIQUE` (`smartm_id` ASC));";
    mysqli_multi_query($con,$sql_save);
	$versionInfo=getSoftwareVersion();

	$softwareLocalVersion=$versionInfo['localVersion'];	
	$softwareLocalRevision=$versionInfo['localRevision'];
	$softwareRemoteVersion=$versionInfo['remoteVersion'];
	$softwareRemoteRevision=$versionInfo['remoteRevision'];
	
    $sql_save = "INSERT INTO $dbname.sm10_set (create_date, update_date, last_access_date, journal_id, help_shown, theme_type, versionLocal, versionLocalRevision, versionRemote, versionRemoteRevision, date_last_update_check) 
    					VALUES (NOW(), NOW(), NOW(),1,0,0, 
    						$softwareLocalVersion,'$softwareLocalRevision', 
    						$softwareRemoteVersion,'$softwareRemoteRevision', '$date_version_published'); ";
    mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm11_pro (`profile_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL DEFAULT NULL,`delete_date` DATETIME NULL DEFAULT NULL,`update_date` DATETIME NULL DEFAULT NULL,`profile_name` VARCHAR(255) NULL DEFAULT NULL,`profile_drn` VARCHAR(255) NULL DEFAULT NULL,`profile_phone_number` VARCHAR(255) NULL DEFAULT NULL,`profile_pic` LONGTEXT NULL DEFAULT NULL,`profile_color_a` VARCHAR(255) NULL DEFAULT NULL,`profile_color_b` VARCHAR(255) NULL DEFAULT NULL,PRIMARY KEY (`profile_id`),UNIQUE INDEX `profile_id_UNIQUE` (`profile_id` ASC));";
    mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm12_rwr (`rr_id` INT(11) NOT NULL AUTO_INCREMENT,`Asset` VARCHAR(15) NULL DEFAULT NULL,`accNo` VARCHAR(5) NULL DEFAULT NULL, `InventNo` VARCHAR(30) NULL DEFAULT NULL, `AssetDesc1` VARCHAR(255) NULL DEFAULT NULL, `Class` VARCHAR(255) NULL DEFAULT NULL, `ParentName` VARCHAR(255) NULL DEFAULT NULL, `rr_included` int(11) DEFAULT NULL, PRIMARY KEY (`rr_id`),UNIQUE INDEX `rr_id_UNIQUE` (`rr_id` ASC));";
    mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm13_stk (
         `stkm_id` INT NOT NULL AUTO_INCREMENT,
         `stk_id` INT NULL,
         `stk_name` VARCHAR(255) NULL,
         `dpn_extract_date` DATETIME NULL,
         `dpn_extract_user` VARCHAR(255) NULL,`smm_extract_date` DATETIME NULL,
         `smm_extract_user` VARCHAR(255) NULL,
         `smm_delete_date` DATETIME NULL,
         `smm_delete_user` VARCHAR(255) NULL,
         `stk_include` INT NULL,
         `rc_orig` INT NULL,
         `rc_orig_complete` INT NULL,
         `rc_extras` INT NULL,
         `stk_type` VARCHAR(255) NULL, 
         `journal_text` LONGTEXT NULL,
         `merge_lock` INT NULL, 
         
         PRIMARY KEY (`stkm_id`),
         UNIQUE INDEX `stkm_id_UNIQUE` (`stkm_id` ASC));";
    mysqli_multi_query($con,$sql_save);



    // $log .= "<br>"."creating $dbname.sm14_ass_old ";
    // $sql_save = "CREATE TABLE `$dbname`.`sm14_ass_old` (
    //           `ass_id` int(11) NOT NULL AUTO_INCREMENT,
    //           `create_date` datetime DEFAULT NULL,
    //           `create_user` varchar(255) DEFAULT NULL,
    //           `delete_date` datetime DEFAULT NULL,
    //           `delete_user` varchar(255) DEFAULT NULL,
    //           `stkm_id` int(11) DEFAULT NULL,
    //           `storage_id` int(11) DEFAULT NULL,
    //           `stk_include` int(11) DEFAULT NULL,

    //           `Asset` varchar(255) DEFAULT NULL,
    //           `Subnumber` varchar(255) DEFAULT NULL,

    //           `genesis_cat` varchar(255) DEFAULT NULL,
    //           `first_found_flag` int(11) DEFAULT NULL,
    //           `rr_id` int(11) DEFAULT NULL,
    //           `fingerprint` varchar(100) DEFAULT NULL,

    //           `res_create_date` datetime DEFAULT NULL,
    //           `res_create_user` varchar(255) DEFAULT NULL,
    //           `res_reason_code` varchar(255) DEFAULT NULL,
    //           `res_reason_code_desc` varchar(255) DEFAULT NULL,
    //           `res_completed` int(1) DEFAULT NULL,
    //           `res_comment` varchar(2000) DEFAULT NULL,

    //           `AssetDesc1` varchar(255) DEFAULT NULL,
    //           `AssetDesc2` varchar(255) DEFAULT NULL,
    //           `AssetMainNoText` varchar(255) DEFAULT NULL,
    //           `Class` varchar(10) DEFAULT NULL,
    //           `assetType` varchar(20) DEFAULT NULL,
    //           `Inventory` varchar(50) DEFAULT NULL,
    //           `Quantity` int(11) DEFAULT NULL,
    //           `SNo` varchar(255) DEFAULT NULL,
    //           `InventNo` varchar(50) DEFAULT NULL,
    //           `accNo` varchar(10) DEFAULT NULL,
    //           `Location` varchar(255) DEFAULT NULL,
    //           `Room` varchar(255) DEFAULT NULL,
    //           `State` varchar(20) DEFAULT NULL,
    //           `latitude` varchar(20) DEFAULT NULL,
    //           `longitude` varchar(255) DEFAULT NULL,
    //           `CurrentNBV` decimal(15,2) DEFAULT NULL,
    //           `AcqValue` decimal(15,2) DEFAULT NULL,
    //           `OrigValue` decimal(15,2) DEFAULT NULL,
    //           `ScrapVal` decimal(15,2) DEFAULT NULL,
    //           `ValMethod` varchar(50) DEFAULT NULL,
    //           `RevOdep` varchar(50) DEFAULT NULL,
    //           `CapDate` datetime DEFAULT NULL,
    //           `LastInv` datetime DEFAULT NULL,
    //           `DeactDate` datetime DEFAULT NULL,
    //           `PlRetDate` datetime DEFAULT NULL,
    //           `CCC_ParentName` varchar(255) DEFAULT NULL,
    //           `CCC_GrandparentName` varchar(255) DEFAULT NULL,
    //           `GrpCustod` varchar(50) DEFAULT NULL,
    //           `CostCtr` varchar(10) DEFAULT NULL,
    //           `WBSElem` varchar(10) DEFAULT NULL,
    //           `Fund` varchar(10) DEFAULT NULL,
    //           `RspCCtr` varchar(10) DEFAULT NULL,
    //           `CoCd` varchar(10) DEFAULT NULL,
    //           `PlateNo` varchar(50) DEFAULT NULL,
    //           `Vendor` varchar(50) DEFAULT NULL,
    //           `Mfr` varchar(50) DEFAULT NULL,
    //           `UseNo` varchar(50) DEFAULT NULL,


    //           `res_AssetDesc1` varchar(255) DEFAULT NULL,
    //           `res_AssetDesc2` varchar(255) DEFAULT NULL,
    //           `res_AssetMainNoText` varchar(255) DEFAULT NULL,
    //           `res_Class` varchar(10) DEFAULT NULL,
    //           `res_assetType` varchar(20) DEFAULT NULL,
    //           `res_Inventory` varchar(50) DEFAULT NULL,
    //           `res_Quantity` int(11) DEFAULT NULL,
    //           `res_SNo` varchar(255) DEFAULT NULL,
    //           `res_InventNo` varchar(20) DEFAULT NULL,
    //           `res_accNo` varchar(10) DEFAULT NULL,
    //           `res_Location` varchar(255) DEFAULT NULL,
    //           `res_Room` varchar(255) DEFAULT NULL,
    //           `res_State` varchar(10) DEFAULT NULL,
    //           `res_latitude` varchar(20) DEFAULT NULL,
    //           `res_longitude` varchar(20) DEFAULT NULL,
    //           `res_CurrentNBV` decimal(15,2) DEFAULT NULL,
    //           `res_AcqValue` decimal(15,2) DEFAULT NULL,
    //           `res_OrigValue` decimal(15,2) DEFAULT NULL,
    //           `res_ScrapVal` decimal(15,2) DEFAULT NULL,
    //           `res_ValMethod` varchar(50) DEFAULT NULL,
    //           `res_RevOdep` varchar(50) DEFAULT NULL,
    //           `res_CapDate` datetime DEFAULT NULL,
    //           `res_LastInv` datetime DEFAULT NULL,
    //           `res_DeactDate` datetime DEFAULT NULL,
    //           `res_PlRetDate` datetime DEFAULT NULL,
    //           `res_CCC_ParentName` varchar(255) DEFAULT NULL,
    //           `res_CCC_GrandparentName` varchar(255) DEFAULT NULL,
    //           `res_GrpCustod` varchar(50) DEFAULT NULL,
    //           `res_CostCtr` varchar(10) DEFAULT NULL,
    //           `res_WBSElem` varchar(10) DEFAULT NULL,
    //           `res_Fund` varchar(10) DEFAULT NULL,
    //           `res_RspCCtr` varchar(10) DEFAULT NULL,
    //           `res_CoCd` varchar(10) DEFAULT NULL,
    //           `res_PlateNo` varchar(50) DEFAULT NULL,
    //           `res_Vendor` varchar(50) DEFAULT NULL,
    //           `res_Mfr` varchar(50) DEFAULT NULL,
    //           `res_UseNo` varchar(50) DEFAULT NULL,

    //           `flagTemplate` int(11) DEFAULT NULL,

    //           PRIMARY KEY (`ass_id`),
    //           UNIQUE KEY `ass_id_UNIQUE` (`ass_id`)
    //           ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    // echo "<br><br>".$sql_save;
    // mysqli_multi_query($con,$sql_save); 



    $log .= "<br>"."creating $dbname.sm14_ass_old ";
    $sql_save = "CREATE TABLE `$dbname`.`sm14_ass` (
              `ass_id` int(11) NOT NULL AUTO_INCREMENT,
              `create_date` datetime DEFAULT NULL,
              `create_user` varchar(255) DEFAULT NULL,
              `delete_date` datetime DEFAULT NULL,
              `delete_user` varchar(255) DEFAULT NULL,
              `stkm_id` int(11) DEFAULT NULL,
              `ledger_id` int(11) DEFAULT NULL,
              `stk_include` int(11) DEFAULT NULL,
              `rr_id` int(11) DEFAULT NULL,

              `sto_asset_id` varchar(20) DEFAULT NULL,
              `sto_assetdesc1` varchar(100) DEFAULT NULL,
              `sto_assetdesc2` varchar(100) DEFAULT NULL,
              `sto_assettext` varchar(100) DEFAULT NULL,
              `sto_class` varchar(10) DEFAULT NULL,
              `sto_class_name` varchar(10) DEFAULT NULL,
              `sto_class_ga_cat` varchar(10) DEFAULT NULL,
              `sto_loc_location` varchar(50) DEFAULT NULL,
              `sto_loc_room` varchar(50) DEFAULT NULL,
              `sto_loc_state` varchar(50) DEFAULT NULL,
              `sto_quantity` int(11) DEFAULT NULL,
              `sto_val_nbv` decimal(15,2) DEFAULT NULL,
              `sto_val_acq` decimal(15,2) DEFAULT NULL,
              `sto_val_orig` decimal(15,2) DEFAULT NULL,
              `sto_val_scrap` decimal(15,2) DEFAULT NULL,
              `sto_valuation_method` varchar(50) DEFAULT NULL,
              `sto_ccc` varchar(10) DEFAULT NULL,
              `sto_ccc_name` varchar(50) DEFAULT NULL,
              `sto_ccc_parent` varchar(50) DEFAULT NULL,
              `sto_ccc_parent_name` varchar(50) DEFAULT NULL,
              `sto_wbs` varchar(50) DEFAULT NULL,
              `sto_fund` varchar(50) DEFAULT NULL,
              `sto_responsible_ccc` varchar(50) DEFAULT NULL,
              `sto_mfr` varchar(50) DEFAULT NULL,
              `sto_inventory` varchar(50) DEFAULT NULL,
              `sto_inventno` varchar(50) DEFAULT NULL,
              `sto_serialno` varchar(30) DEFAULT NULL,
              `sto_site_no` varchar(10) DEFAULT NULL,
              `sto_grpcustod` varchar(30) DEFAULT NULL,
              `sto_plateno` varchar(30) DEFAULT NULL,
              `sto_revodep` varchar(30) DEFAULT NULL,

              `sto_date_lastinv` datetime DEFAULT NULL,
              `sto_date_cap` datetime DEFAULT NULL,
              `sto_date_pl_ret` datetime DEFAULT NULL,
              `sto_date_deact` datetime DEFAULT NULL,
              `sto_loc_latitude` varchar(10) DEFAULT NULL,
              `sto_loc_longitude` varchar(10) DEFAULT NULL,

              `genesis_cat` varchar(255) DEFAULT NULL,
              `res_create_date` datetime DEFAULT NULL,
              `res_create_user` varchar(255) DEFAULT NULL,
              `res_fingerprint` varchar(100) DEFAULT NULL,
              `res_reason_code` varchar(255) DEFAULT NULL,
              `res_rc_desc` varchar(255) DEFAULT NULL,
              `res_comment` varchar(2000) DEFAULT NULL,

              `res_asset_id` varchar(20) DEFAULT NULL,
              `res_assetdesc1` varchar(100) DEFAULT NULL,
              `res_assetdesc2` varchar(100) DEFAULT NULL,
              `res_assettext` varchar(100) DEFAULT NULL,
              `res_class` varchar(10) DEFAULT NULL,
              `res_class_name` varchar(10) DEFAULT NULL,
              `res_class_ga_cat` varchar(10) DEFAULT NULL,
              `res_loc_location` varchar(50) DEFAULT NULL,
              `res_loc_room` varchar(50) DEFAULT NULL,
              `res_loc_state` varchar(50) DEFAULT NULL,
              `res_quantity` int(11) DEFAULT NULL,
              `res_val_nbv` decimal(15,2) DEFAULT NULL,
              `res_val_acq` decimal(15,2) DEFAULT NULL,
              `res_val_orig` decimal(15,2) DEFAULT NULL,
              `res_val_scrap` decimal(15,2) DEFAULT NULL,
              `res_valuation_method` varchar(50) DEFAULT NULL,
              `res_ccc` varchar(10) DEFAULT NULL,
              `res_ccc_name` varchar(50) DEFAULT NULL,
              `res_ccc_parent` varchar(50) DEFAULT NULL,
              `res_ccc_parent_name` varchar(50) DEFAULT NULL,
              `res_wbs` varchar(50) DEFAULT NULL,
              `res_fund` varchar(50) DEFAULT NULL,
              `res_responsible_ccc` varchar(50) DEFAULT NULL,
              `res_mfr` varchar(50) DEFAULT NULL,
              `res_inventory` varchar(50) DEFAULT NULL,
              `res_inventno` varchar(50) DEFAULT NULL,
              `res_serialno` varchar(30) DEFAULT NULL,
              `res_site_no` varchar(10) DEFAULT NULL,
              `res_grpcustod` varchar(30) DEFAULT NULL,
              `res_plateno` varchar(30) DEFAULT NULL,
              `res_revodep` varchar(50) DEFAULT NULL,
              `res_date_lastinv` datetime DEFAULT NULL,
              `res_date_cap` datetime DEFAULT NULL,
              `res_date_pl_ret` datetime DEFAULT NULL,
              `res_date_deact` datetime DEFAULT NULL,
              `res_loc_latitude` varchar(10) DEFAULT NULL,
              `res_loc_longitude` varchar(10) DEFAULT NULL,
              PRIMARY KEY (`ass_id`),
              UNIQUE KEY `ass_id_UNIQUE` (`ass_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    //  echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save); 


    $sql_save = "CREATE TABLE $dbname.sm15_rc (
        `reason_code_id` INT(11) NOT NULL AUTO_INCREMENT,
        `create_date` datetime DEFAULT NULL,
        `delete_date` datetime DEFAULT NULL,
        `res_reason_code` VARCHAR(10) NULL, 
        `rc_desc` VARCHAR(255) NULL, 
        `rc_long_desc` VARCHAR(255) NULL, 
        `rc_example` VARCHAR(255) NULL, 
        `rc_origin` VARCHAR(255) NULL,
        `rc_action` VARCHAR(255) NULL,
        `rc_states` VARCHAR(255) NULL,
        `rc_sorting_cat` VARCHAR(255) NULL,
        `rc_color` VARCHAR(255) NULL,
        PRIMARY KEY (`reason_code_id`),
        UNIQUE INDEX `reason_code_id_UNIQUE` (`reason_code_id` ASC));";
    //     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);
    upload_reason_codes();
// $sql_save = "CREATE TABLE $dbname.sm15_rc_old (
//     `reason_code_id` INT(11) NOT NULL AUTO_INCREMENT,
//     `res_reason_code` VARCHAR(255) NULL, 
//     `rc_desc` VARCHAR(255) NULL, 
//     `rc_long_desc` VARCHAR(255) NULL, 
//     `rc_examples` VARCHAR(255) NULL, 
//     `rc_action` VARCHAR(255) NULL,
//     `rc_section` VARCHAR(255) NULL,
//     PRIMARY KEY (`reason_code_id`),
//     UNIQUE INDEX `reason_code_id_UNIQUE` (`reason_code_id` ASC));";
// //     echo "<br><br>".$sql_save;
// mysqli_multi_query($con,$sql_save);


    // $sql_save = "INSERT INTO $dbname.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_examples, rc_action, rc_section) VALUES  
    // ('ND10','Asset Found - GA counted','No financial discrepancies - No action required','Asset found with all details correct.','ND','ND'),

    // ('FF98','Asset incorrectly added as First Found (FF)','Assets added to this stocktake then removed. Found to be in error during to reconciliation process.','Assets added to this stocktake then removed, whilst under reconciliation this asset was found to be, (i) already in ROMAN under another asset number, (ii) assets not in our count schedule e.g. class 6000 - low value.  Or ASD Asset added to E&IG count etc.','NIC','RFC'),
    // ('FF99','DFG excluded adjustments, as approved by DFG.','Assets First Found which are project related, to be removed from the count as approved by DFG.','Pending ROMAN adjustments relating to a Project Rollout. The rollout of these assets will be conducted IAW Project Rollout processes.','NIC','RFC'),
    // ('NC10','Removed from count','Other G&S have stocktake resposibilty for this Asset','Assets belong to owner not included in this Stocktake. Asset in original data list but managed and counted by Others (e.g. CIOG counted assets / TAMIT) Note: Pre Dec 2019 this was the only removal code available in SMART','NIC','RFC'),
    // ('NC15','Unable to count','Assets excluded from the Original count List (GA team is unable to count these assets on this cycle).','Asset where the site is inaccessible by anyone. i.e. WHS issue, remote locality or project construction areas.','NIC','RFC'),
    // ('NF35','Asset under disposal processing, as approved by DFG.','Disposal evidence provided to DFG - DFG will process the original request (No SAV required).','Disposal paperwork is with DFG for process, asset will be deactivated in ROMAN.','NIC','RFC'),
    // ('NF40','Asset under Transfer adjustments, as approved by DFG.','Transfer evidence provided to DFG - DFG will process the original request (No SAV required)','Demountable moved between Defence properties without asset transfer documentation sent to DFG. Asset not found (Sighted) - Assets have been transferred to another Site or Base. The Asset owner has provided evidence of the transfer request [Asset Transfer Voucher] has been sent to DFG for processing. The GA stocktaking team to ensure the transfer is completed in ROMAN','NIC','RFC'),

    // ('FF10','Asset First Found - Project Acquisition','Asset first found - Procured under a National Project Works.','Project asset not brought on to the asset register/system, not communicated to be added to the asset register/system.','SAV','FF'),
    // ('FF15','Asset First Found - Local Estate Works','Asset first found - Procured under Local Estate Works','Asset acquired under the local estate works contract (repair/replacement). Procurement not communicated to DFG, and not added to the asset register/system.','SAV','FF'),
    // ('FF20','Asset First Found - Externally Acquired','Asset first found - asset received from organisation external to Defence.','Asset acquired from another government department without documentation. Asset could have been `Gifted to Defence`.','SAV','FF'),
    // ('FF25','Asset First Found - Unexplained','Asset first found - Unexplained.','Asset purchase with no history, no explanation as to its existence. Not communicated to DFG, and not added to the FAR','SAV','FF'),

    // ('ND11','Asset Found - Counted by custodian','No financial discrepancies - Counted by custodian','Asset counted by custodian, unseen by GA staff. This covers all virtual stocktakes and Assets counted by the custodian when site and room access has been restricted. This includes assets that are away from the site and have been confirmed by the custodian (e.g. Drones, Cameras) Assets managed by custodian in house asset lists.','ND','ERR'),
    // ('RE20','Asset Found - Register Error','General non-financial related errors.','Simple record updates such as, location data changes within the current Site and CCC, barcode updates, transcription, spelling errors, description i.e. asset description not in UPPER CASE.','ND','ERR'),
    // ('AF10','Asset Found - Ownership','Asset ownership error. The asset management system to be updated to reflect correct owners.','Asset found with incorrect Cost Centre Code.','SAV','ERR'),
    // ('AF15','Asset Found - Incorrect Register','Asset found - asset accounted for in the incorrect asset register/system.','An asset found that should be accounted for in MILIS and not ROMAN.','SAV','ERR'),
    // ('AF20','Asset Found - Asset Transferred onto Site','Asset found, however the asset register indicated the asset resided on another base/site. Asset added from Raw Remainder.','Demountable moved between Defence properties without asset transfer documentation sent to DFG.  (Asset found in Raw Remainder and the Transfer was not completed in ROMAN). The GA stocktaking team to ensure the transfer is completed in ROMAN.','SAV','ERR'),
    // ('PE15','Prior Stocktake Error - Write on','Stocktake Adjustment error in the asset register/ system, where the error has occurred as a direct result of a previous or current stocktake adjustment.','Reversal of a `write-off` action from a previous stocktake. Asset was disposed in error on previous stocktake.','SAV','ERR'),
    // ('RE25','Asset Split','Errors relating to assets that may form part of Merge/Split process.','A Split error is where a single asset record may have been initially created, however the assets characteristics distinctly display two separate physical assets','SAV','ERR'),


    // ('NF30','Asset Not Found - Unexplained','Asset not found - Unexplained','Asset owner cannot provide information as to its whereabouts.','SAV','NF'),
    // ('NF10','Asset Not Found - Project Disposal','Asset not found - Disposal under National Project','Asset disposed under a National Project not communicated to DFG, not removed from the asset register/system.','SAV','NF'),
    // ('NF15','Asset Not Found - Local Disposal','Asset not found - Locally disposed asset.','Asset disposal, failed to advise DFG of disposal, not removed from the asset register/system.','SAV','NF'),
    // ('NF20','Asset Not Found - Trade in','Asset not found - Procurement Trade-In','Asset used as `Traded-in` in the procurement process, asset owner failed to follow correct disposal process, not communicated to DFG, not removed from the asset register/system.','SAV','NF'),
    // ('NF25','Asset Not Found - Local Estate Works','Asset not found - Disposal under Local Estate Works','Asset disposed under a local works, not communicated to DFG, not removed from the asset register/system.','SAV','NF'),
    // ('PE10','Prior Stocktake Error - Write off','Stocktake Adjustment error in the asset register/ system, where the error has occurred as a direct result of a previous or current stocktake adjustment.','Reversal of a `write-on` action from a previous stocktake. AFF that should not have been created.','SAV','NF'),
    // ('RE10','Asset Duplication - Different Register','Errors found for the same asset record in separate registers/ systems/company codes where the error is a direct result of register actions by DFG Register Authority.','Duplication: assets recorded and financially accounted for in multiple register/ systems (ROMAN and MILIS), or in multiple Company Codes, (1000 and 4100).','SAV','NF'),
    // ('RE15','Asset Duplication - Same Register','Errors found for the same asset record in same asset register/ system, where the error is a direct result of register actions by the Register Authority','Duplication: assets recorded twice for the same physical asset. Assets created as a result of revaluation adjustments.','SAV','NF'),
    // ('RE30','Asset Merge','Errors relating to assets that may form part of Merge/Split process.','A Merge error is where two asset records may have been initially created, when it should have been a single asset record;','SAV','NF'); "; 
    // // echo "<br><br>".$sql_save;
    // mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm16_file (`file_id` INT NOT NULL AUTO_INCREMENT,`file_type` VARCHAR(255) NULL,`file_ref` VARCHAR(255) NULL,`file_desc` VARCHAR(255) NULL,PRIMARY KEY (`file_id`),UNIQUE INDEX `file_id_UNIQUE` (`file_id` ASC));";
//     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm17_history (`history_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL,`create_user` VARCHAR(255) NULL,`history_link` VARCHAR(255) NULL,`history_type` VARCHAR(255) NULL,`history_desc` VARCHAR(255) NULL, PRIMARY KEY (`history_id`));";
//     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);
    
    $sql_save = "INSERT INTO ".$dbname.".sm17_history (create_date, create_user, history_type, history_desc) VALUES ( NOW(),'System Robot','System Initialisation','The system initiated a new deployment');";
    mysqli_multi_query($con,$sql_save);

    // $sql_save = "CREATE TABLE $dbname.sm20_is (`auto_isID` INT(11) NOT NULL AUTO_INCREMENT, `isID` INT(11), `create_date` DATETIME NULL,`create_user` VARCHAR(255) NULL,`isName` VARCHAR(255) NULL, `dpn_extract_date` DATETIME NULL, `dpn_extract_user` VARCHAR(255) NULL, `rowcount_original` INT(11), `rowcount_firstfound` INT(11), `rowcount_other` INT(11), `rowcount_completed` INT(11), PRIMARY KEY (`auto_isID`));";
    // echo "<br><br>".$sql_save;
    // mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm18_impairment (         
        auto_storageID INT(11) NOT NULL AUTO_INCREMENT,   
        stkm_id INT(11),
        storageID INT(11),
        DSTRCT_CODE VARCHAR(255) NULL,
        WHOUSE_ID VARCHAR(255) NULL,
        BIN_CODE VARCHAR(255) NULL,
        STOCK_CODE VARCHAR(255) NULL,
        ITEM_NAME VARCHAR(255) NULL,
        STK_DESC VARCHAR(255) NULL,
        SUPPLY_CUST_ID VARCHAR(255) NULL,
        SC_ACCOUNT_TYPE VARCHAR(255) NULL, 
        SUPPLY_ACCT_METH VARCHAR(50) NULL, 
        INVENT_CAT VARCHAR(255) NULL,
        INVENT_CAT_DESC VARCHAR(255) NULL,
        TRACKING_IND VARCHAR(255) NULL,
        SOH INT(11),
        TRACKING_REFERENCE VARCHAR(255) NULL,
        LAST_MOD_DATE DATETIME NULL,
        sampleFlag INT(11),
        serviceableFlag INT(11), 
        checked_to_milis int(11) NULL DEFAULT 0,
        findingID VARCHAR(255) NULL,
        isID INT(11),
        targetID INT(11),
        targetItemID INT(11),
        isType VARCHAR(255) NULL,
        isBackup INT(11),
        res_comment text NULL,
        res_evidence_desc VARCHAR(255) NULL,
        res_unserv_date datetime NULL,
        res_parent_storageID VARCHAR(255) NULL,
        res_create_date datetime NULL,
        res_create_user VARCHAR(255) NULL, 
        res_update_user VARCHAR(255) NULL,
        finalResult VARCHAR(255) NULL,
        finalResultPath VARCHAR(255) NULL,
        fingerprint varchar(255) DEFAULT NULL,
        checkFlag int(11) NULL,
        data_source varchar(30) DEFAULT NULL,
        extract_date datetime NULL,
        create_date datetime NULL,
        create_user VARCHAR(255) NULL,
        delete_date datetime NULL,
        delete_user VARCHAR(255) NULL,
        PRIMARY KEY (auto_storageID));";
    mysqli_multi_query($con,$sql_save);


    $sql_save = "CREATE TABLE $dbname.sm19_result_cats (
         `findingID` INT(11) NOT NULL AUTO_INCREMENT, 
         `findingName` VARCHAR(255) NULL,
         `isType` VARCHAR(30) NULL,
         `color` VARCHAR(255) NULL,
         `reqDate` INT(11),
         `reqSplit` INT(11),
         `reqComment` INT(11),
         `resAbbr` VARCHAR(30),
         `resHelp` VARCHAR(1000),          
         PRIMARY KEY (`findingID`));";

         mysqli_multi_query($con,$sql_save);


    $sql_save = "INSERT INTO $dbname.sm19_result_cats (findingID, findingName, isType, color, reqDate, reqSplit, reqComment, resAbbr, resHelp) VALUES 
    (1, 'Serial tracked - Item sighted - Serviceable','imp','success',0,0,0,'SER','Also includes items currently in use.'),
    (2, 'Serial tracked - Item sighted - Unserviceable - with date','imp','success',1,0,1,'USWD','Comments and Date mandatory with this option. Date must be cross-checked with MILIS, the date refers to the date the item inventory category first changed to unserviceable.'),
    (3, 'Serial tracked - Item sighted - Unserviceable - no date','imp','success',0,0,1,'USND','Comments mandatory with this option. Only select this option if a date cannot be verified within MILIS. Ensure evidence has been provided to support this option.'),
    (4, 'Serial tracked - Item not sighted - Serviceable','imp','warning',0,0,0,'SER','Also includes items currently in use.'),
    (5, 'Serial tracked - Item not sighted - Unserviceable - with date','imp','warning',1,0,1,'USWD','Comments and Date mandatory with this option. Date must be cross-checked with MILIS, the date refers to the date the item inventory category first changed to unserviceable.'),
    (6, 'Serial tracked - Item not sighted - Unserviceable - no date','imp','warning',0,0,1,'USND','Comments mandatory with this option. Only select this option if a date cannot be verified within MILIS. Ensure evidence has been provided to support this option.'),
    (7, 'Serial tracked - Item not found, no evidence provided','imp','danger',0,0,1,'NIC','Comments mandatory with this option. Ensure communication to EDLA and DLAP and site Point of Contact is aware of evidential requirements.'),
    (8, 'Quantity tracked - Sighted or found evidence of all items - All serviceable','imp','success',0,0,0,'SER','Also includes items currently in use.'),
    (9, 'Quantity tracked - Sighted or found evidence of all items - None serviceable - with date','imp','success',1,0,1,'USWD','Comments and Date mandatory with this option. Date must be cross-checked with MILIS, the date refers to the date the item inventory category first changed to unserviceable.'),
    (10, 'Quantity tracked - Sighted or found evidence of all items - None serviceable - no date','imp','success',0,0,1,'USND','Comments mandatory with this option. Only select this option if a date cannot be verified within MILIS. Ensure evidence has been provided to support this option.'),

    (11, 'Quantity tracked - Split category - One, some or all of the following:<br>+ Not all items were found<br>+ Items were in different categories<br>+ Found more than original quantity','imp','warning',0,1,1,'SPLT',''),

    (12, 'Quantity tracked - No items found, no evidence provided','imp','danger',0,0,1,'NIC','Comments mandatory with this option. Ensure communication to EDLA and DLAP and site Point of Contact is aware of evidential requirements.'),
    (13, 'In progress - Come back to it later','imp','info',0,0,0,'TBA','Parked and complete when more information is available.'),


    (14, 'No additional stockcodes were found','b2r','success',0,0,0,'NSTR',''),
    (15, 'You have found some additional stockcodes but have not investigated them','b2r','info',0,0,0,'TBA',''),
    (16, 'You have found some additional stockcodes and have investigated them all','b2r','warning',0,0,0,'INV',''),


    (17, 'Split - Item sighted - Serviceable','imp','success',0,0,0,'SER','Also includes items currently in use.'),
    (18, 'Split - Item sighted - Unserviceable - with date','imp','success',1,0,1,'USWD','Comments and Date mandatory with this option. Date must be cross-checked with MILIS, the date refers to the date the item inventory category first changed to unserviceable.'),
    (19, 'Split - Item sighted - Unserviceable - no date','imp','success',0,0,1,'USND','Comments mandatory with this option. Only select this option if a date cannot be verified within MILIS. Ensure evidence has been provided to support this option.'),
    (20, 'Split - Item not sighted - Serviceable','imp','warning',0,0,0,'SER','Also includes items currently in use.'),
    (21, 'Split - Item not sighted - Unserviceable - with date','imp','warning',1,0,1,'USWD','Comments and Date mandatory with this option. Date must be cross-checked with MILIS, the date refers to the date the item inventory category first changed to unserviceable.'),
    (22, 'Split - Item not sighted - Unserviceable - no date','imp','warning',0,0,1,'USND','Comments mandatory with this option. Only select this option if a date cannot be verified within MILIS. Ensure evidence has been provided to support this option.'),
    (23, 'Split - Item not found, no evidence provided','imp','danger',0,0,1,'NIC','Comments mandatory with this option. Ensure communication to EDLA and DLAP and site Point of Contact is aware of evidential requirements.')

    
    ; "; 
    // echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);


    $sql_save = "CREATE TABLE $dbname.sm20_quarantine (
        `q_id` INT(11) NOT NULL AUTO_INCREMENT, 
        `stkm_id_new` INT(11) NULL,
        `stkm_id_one` INT(11) NULL,
        `stkm_id_two` INT(11) NULL,
        `isType` VARCHAR(255) NULL,
        `stID1` VARCHAR(255) NULL,
        `pkID1` VARCHAR(255) NULL,
        `pkID2` VARCHAR(255) NULL,
        `BIN_CODE` VARCHAR(255) NULL,
        `res_pkID_selected` VARCHAR(255) NULL,
        `res_stkm_id_selected` VARCHAR(255) NULL,
        `complete_date` datetime NULL,
        PRIMARY KEY (`q_id`),
        UNIQUE KEY `index_single_storage_candidate` (`stkm_id_new`, `stID1`));";
//     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);

    $sql_save = "UPDATE smartdb.sm10_set SET rr_count = (SELECT COUNT(*) AS rr_count FROM smartdb.sm12_rwr) WHERE smartm_id =1";
    mysqli_multi_query($con,$sql_save);


     header("Location: index.php");
}










function get_file(){
    foreach(glob('config/*.*') as $filename) {//find the filename in the config folder
        if (strpos($filename, 'reason_codes') !== false) {
            return $filename;
        }
    }
}

function upload_reason_codes(){
    global $con;
    $sql            = "TRUNCATE TABLE smartdb.sm15_rc;";
    $res_truncate   = mysqli_multi_query($con,$sql);
    $filename       = get_file('reason_codes');
    $file_contents  = file_get_contents($filename);
    $string         = "[" . trim($file_contents) . "]";
    $json           = json_decode($string, true);
    $header         = $json[0];
    
    $stmt   = $con->prepare("INSERT INTO smartdb.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_example, rc_origin, rc_action, rc_states, rc_sorting_cat, rc_color) VALUES (?,?,?,?,?,?,?,?,?);");
    foreach ($header['reason_codes'] as $key => $val) {    
        // echo "<br>";
        // print_r($val);
        $stmt   ->bind_param("sssssssss", $val['res_reason_code'], $val['rc_desc'], $val['rc_long_desc'], $val['rc_example'], $val['rc_origin'], $val['rc_action'], $val['rc_states'], $val['rc_sorting_cat'], $val['rc_color']);
        $stmt   ->execute();
    }

}
?>