<?php
include "php/common/common.php";

function fnInitiateDatabase(){
    global $con, $dbname, $date_version_published, $log;

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
    						$softwareRemoteVersion,'$softwareRemoteRevision', NOW()); ";
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


    $log .= "<br>"."creating $dbname.sm14_ass_old ";
    $sql_save = "CREATE TABLE `$dbname`.`sm14_ass` (
              `ass_id` int(11) NOT NULL AUTO_INCREMENT,
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
              `sto_ccc_grandparent` varchar(50) DEFAULT NULL,
              `sto_ccc_grandparentname` varchar(50) DEFAULT NULL,
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
              `res_ccc_grandparent` varchar(50) DEFAULT NULL,
              `res_ccc_grandparent_name` varchar(50) DEFAULT NULL,
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
              `duplicate` int(11) DEFAULT -1,
              `create_user` varchar(255) DEFAULT NULL,
              `create_date` datetime DEFAULT NULL,
              `delete_user` varchar(255) DEFAULT NULL,
              `delete_date` datetime DEFAULT NULL,
              `modify_user` varchar(255) DEFAULT NULL,              
              `modify_date` datetime DEFAULT NULL,
              `version` int(11) NOT NULL,
              PRIMARY KEY (`ass_id`),
              UNIQUE KEY `ass_id_UNIQUE` (`ass_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    mysqli_multi_query($con,$sql_save); 
    try{
	   $con->query(
"CREATE TRIGGER asset_insert 
BEFORE INSERT 
ON sm14_ass
FOR EACH ROW 
BEGIN		
	IF(new.version is null) THEN
		set new.version=0;
	END IF;
    set new.delete_date=null;
    IF (new.create_date is null) THEN
    	set new.create_date=now(), 
            new.create_user=getCurrentProfileName();
    END IF;
    
    IF((new.ledger_id IS NULL) AND (new.res_fingerprint IS NULL)) THEN
    	set new.res_fingerprint=UUID();
    END IF;
    
	SELECT 
    	stk_id 
    into @new_stocktake_id
    FROM sm13_stk
    WHERE
    	stkm_id=new.stkm_id;
    
    SET @edit_status='NEW', @old_ass_id=NULL;
    
    IF(new.ledger_id IS NULL) THEN    	
		SELECT 
			(
				CASE 
				WHEN (new.version > a.version) THEN 'NEW' 	
				WHEN (new.version = a.version) THEN 'DUP'
				WHEN (new.version < a.version) THEN 'OLD'	
				END       		
			), ass_id, modify_date, version
			INTO @edit_status, @old_ass_id, @old_modify_date, @old_version
		FROM 
			sm14_ass as a inner join 
			sm13_stk as t on ((a.stkm_id=t.stkm_id) and 
	                          (t.stk_id = @new_stocktake_id) and 
	                          (t.smm_delete_date is null))
		WHERE
			a.res_fingerprint=new.res_fingerprint;     					
    ELSE
    	
		SELECT 
			(
				CASE 
				WHEN (new.version > a.version) THEN 'NEW' 	
				WHEN (new.version = a.version) THEN 'DUP'
				WHEN (new.version < a.version) THEN 'OLD'	
				END       		
			), ass_id, modify_date, version
			INTO @edit_status, @old_ass_id, @old_modify_date, @old_version
		FROM 
			sm14_ass as a inner join 
			sm13_stk as t on ((a.stkm_id=t.stkm_id) and 
	                          (t.stk_id = @new_stocktake_id) and 
	                          (t.smm_delete_date is null))
		WHERE
			a.ledger_id=new.ledger_id; 
	END IF;		

	IF ((@edit_status='NEW') AND (@old_ass_id is not null))  THEN
		SET new.duplicate=@old_ass_id;
	ELSEIF (@edit_status='DUP') THEN
    	IF((@old_modify_date IS NULL AND new.modify_date IS NOT NULL) OR (new.modify_date > @old_modify_date)) THEN
            SET new.duplicate=@old_ass_id;
        ELSE    
			SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT='Asset record already exist',
            MYSQL_ERRNO=20000;
        END IF;       
    ELSEIF (@edit_status='OLD') THEN
		SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT='Asset record is older',
        MYSQL_ERRNO=20000;    
	END IF;
END"
	    );
	   
	   
        $con->query("
CREATE TRIGGER asset_update 
BEFORE UPDATE 
ON sm14_ass
FOR EACH ROW 
BEGIN
	 IF((select smm_delete_date 
        from sm13_stk 
        WHERE 
        	stkm_id=old.stkm_id) is NULL) THEN
		IF((new.stk_include=old.stk_include) 
			AND(new.duplicate=old.duplicate)) THEN
			set new.version = old.version + 1,
                new.modify_date = now(),
                new.modify_user=getCurrentProfileName();
    	END IF;
    END IF;
END"
       );
        
        $con->query("
CREATE DEFINER=root@localhost 
FUNCTION getCurrentProfileName() 
RETURNS varchar(100) CHARSET utf8mb4
NO SQL
SQL SECURITY INVOKER
BEGIN
    SELECT profile_name
    INTO @currentProfileName
    FROM sm11_pro
    WHERE profile_id=(
        SELECT active_profile_id
        FROM sm10_set
        LIMIT 1
        );
    RETURN @currentProfileName;
END"
    );        
        

        
    }catch(Throwable $e){

    }
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


    $sql_save = "CREATE TABLE $dbname.sm16_file (`file_id` INT NOT NULL AUTO_INCREMENT,`file_type` VARCHAR(255) NULL,`file_ref` VARCHAR(255) NULL,`file_desc` VARCHAR(255) NULL,PRIMARY KEY (`file_id`),UNIQUE INDEX `file_id_UNIQUE` (`file_id` ASC));";
//     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);

    $sql_save = "CREATE TABLE $dbname.sm17_history (`history_id` INT(11) NOT NULL AUTO_INCREMENT,`create_date` DATETIME NULL,`create_user` VARCHAR(255) NULL,`history_link` VARCHAR(255) NULL,`history_type` VARCHAR(255) NULL,`history_desc` VARCHAR(255) NULL, PRIMARY KEY (`history_id`));";
//     echo "<br><br>".$sql_save;
    mysqli_multi_query($con,$sql_save);
    
    $sql_save = "INSERT INTO ".$dbname.".sm17_history (create_date, create_user, history_type, history_desc) VALUES ( NOW(),'System Robot','System Initialisation','The system initiated a new deployment');";
    mysqli_multi_query($con,$sql_save);


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

    mysqli_multi_query($con,$sql_save);
    $con->query("INSERT INTO $dbName.sm11_pro(profile_name,create_date) VALUES ('Haibang.Mai', now());");    
    
    $sql_save = "UPDATE smartdb.sm10_set SET active_profile_id=1,rr_count = (SELECT COUNT(*) AS rr_count FROM smartdb.sm12_rwr) WHERE smartm_id =1";
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