<?php
include "01_dbcon.php"; 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function qget($sql){// Submits a basic sql and returns an array result
    global $con;
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res[] = $row;
    }}
    return $res;
}

$target_file    = $_FILES["file_upload"]["tmp_name"];
$file_contents  = file_get_contents($target_file);

// //This is to remove the unicode encoding on the file. It leaves two characters at the start of the file which throw an error.
$file_contents  = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $file_contents);
$arr            = json_decode($file_contents, true);
// echo "<pre>";print_r($arr);echo "</pre>";

$type               = $arr['type'];

// $sql = "  INSERT INTO smartdb.sm13_stk (stk_id, stk_name, stk_type) VALUES (0,'template','template');SELECT LAST_INSERT_ID();";
// runSql($sql);

if ($arr['type']=="ga_stk") {
    fnUpload_ga_stocktake($arr, 0);
}elseif ($arr['type']=="raw remainder v2"){
    fnUpload_rawremainder($arr, 0);
}elseif ($arr['type']=="ga_rr") {

}elseif ($arr['type']=="is_audit") {
    fnUpload_is_audit($arr, 0);
}
header("Location: index.php");


// $res = qget("SELECT * FROM smartdb.sm14_ass");
// echo "<pre>";print_r($res);echo "</pre>";















function fnUpload_ga_stocktake($arr, $dev){
    global $con;
    $type               = $arr['type'];
    $file_version       = $arr['file_version'];
    $stk_name           = $arr['stk_name'];
    $dpn_extract_date   = $arr['dpn_extract_date'];
    $dpn_extract_user   = $arr['dpn_extract_user'];
    $smm_extract_date   = $arr['smm_extract_date'];
    $smm_extract_user   = $arr['smm_extract_user'];
    $rc_orig            = $arr['rc_orig'];
    $rc_orig_complete   = $arr['rc_orig_complete'];
    $rc_extras          = $arr['rc_extras'];
    $unique_file_id     = $arr['unique_file_id'];
    $stk_id = $arr['stk_id'];
    $stmt   = $con->prepare("INSERT INTO smartdb.sm13_stk (stk_id, stk_name, stk_type, dpn_extract_date, dpn_extract_user, smm_extract_date, smm_extract_user, rc_orig, rc_orig_complete, rc_extras) VALUES(?,?,?,?,?,?,?,?,?,?);");
    $stmt   ->bind_param("ssssssssss", $stk_id, $stk_name, $type, $dpn_extract_date, $dpn_extract_user, $smm_extract_date, $smm_extract_user, $rc_orig, $rc_orig_complete, $rc_extras);
    $stmt   ->execute();

    $res            = qget("SELECT LAST_INSERT_ID();");
    $new_stkm_id    = $res[0]['LAST_INSERT_ID()'];
    // echo "<br>".$new_stkm_id;

    $assetlist     = $arr['assetlist'];
    // echo "<pre>";print_r($assetlist);echo "</pre>";

    $stmt   = $con->prepare("INSERT INTO smartdb.sm14_ass (
        create_date, create_user, stkm_id, ledger_id, rr_id,

        sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, 
        sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method,  sto_ccc, sto_ccc_name, sto_ccc_parent, sto_ccc_parent_name, sto_wbs, 
        sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_date_lastinv, 
        sto_date_cap, sto_loc_latitude, sto_loc_longitude, 
        
        genesis_cat, res_create_date, res_create_user, res_fingerprint, res_reason_code, res_rc_desc, res_comment, 
        
        res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, 
        res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method,  res_ccc, res_ccc_name, res_ccc_parent, res_ccc_parent_name, res_wbs, 
        res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_date_lastinv, 
        res_date_cap, res_loc_latitude, res_loc_longitude

    ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");

    foreach ($assetlist as $key => $row) {
        // $res_create_date = $row['res_create_date'];
        // echo "<br>[".$res_create_date."]";
        // echo "<br><pre>";print_r($row);echo "</pre>";
        $stmt   ->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss", 
            $row['create_date'],$row['create_user'],$new_stkm_id, $row['ledger_id'],$row['rr_id'],

            $row['sto_asset_id'], $row['sto_assetdesc1'], $row['sto_assetdesc2'], $row['sto_assettext'], $row['sto_class'], 
            $row['sto_class_ga_cat'], $row['sto_loc_location'], $row['sto_loc_room'], $row['sto_loc_state'],  $row['sto_quantity'], 
            $row['sto_val_nbv'], $row['sto_val_acq'], $row['sto_val_orig'], $row['sto_val_scrap'], $row['sto_valuation_method'], 
            $row['sto_ccc'], $row['sto_ccc_name'], $row['sto_ccc_parent'], $row['sto_ccc_parent_name'], $row['sto_wbs'], 
            $row['sto_fund'], $row['sto_responsible_ccc'], $row['sto_mfr'], $row['sto_inventory'], $row['sto_inventno'], 
            $row['sto_serialno'], $row['sto_site_no'], $row['sto_grpcustod'], $row['sto_plateno'], $row['sto_date_lastinv'], 
            $row['sto_date_cap'], $row['sto_loc_latitude'], $row['sto_loc_longitude'],

            $row['genesis_cat'], $row['res_create_date'], $row['res_create_user'], $row['res_fingerprint'], $row['res_reason_code'], $row['res_rc_desc'], $row['res_comment'],

            $row['res_asset_id'], $row['res_assetdesc1'], $row['res_assetdesc2'], $row['res_assettext'], $row['res_class'], 
            $row['res_class_ga_cat'], $row['res_loc_location'], $row['res_loc_room'], $row['res_loc_state'],  $row['res_quantity'], 
            $row['res_val_nbv'], $row['res_val_acq'], $row['res_val_orig'], $row['res_val_scrap'], $row['res_valuation_method'], 
            $row['res_ccc'], $row['res_ccc_name'], $row['res_ccc_parent'], $row['res_ccc_parent_name'], $row['res_wbs'], 
            $row['res_fund'], $row['res_responsible_ccc'], $row['res_mfr'], $row['res_inventory'], $row['res_inventno'], 
            $row['res_serialno'], $row['res_site_no'], $row['res_grpcustod'], $row['res_plateno'], $row['res_date_lastinv'], 
            $row['res_date_cap'], $row['res_loc_latitude'], $row['res_loc_longitude']

        );
        $stmt   ->execute();
    }
}



function fnUpload_rawremainder($arr, $dev){
    global $con;
    $extract_date  = $arr['extract_date'];
    $extract_user  = $arr['extract_user'];
    if ($dev) { echo "<br>extract_date:".$extract_date; }
    if ($dev) { echo "<br>extract_user:".$extract_user."<br>"; }
    ini_set('max_execution_time', 30000); //300 seconds = 5 minutes

    $sql_delete = "TRUNCATE TABLE smartdb.sm12_rwr    ; ";
    mysqli_multi_query($con,$sql_delete);

    
    $stmt   = $con->prepare("INSERT INTO smartdb.sm12_rwr (Asset,accNo,InventNo,AssetDesc1) VALUES (?,?,?,?);");


    $assetRows     = $arr['assetRows'];
    foreach($assetRows as $assetRow) {
         $Asset              = $assetRow['f1'];
         if ($Asset!="END") {
            $accNo         = $assetRow['f2'];
            $InventNo      = $assetRow['f3'];
            $AssetDesc1    = $assetRow['f4'];  
              
            $stmt   ->bind_param("ssss", $Asset, $accNo, $InventNo, $AssetDesc1);
            $stmt   ->execute();


            // $sql_save = "INSERT INTO smartdb.sm12_rwr (Asset,accNo,InventNo,AssetDesc1) VALUES ('$Asset','$accNo','$InventNo','$AssetDesc1'); ";
            // echo "<br>".$sql_save;
            //     mysqli_multi_query($con,$sql_save);
         }
    }
    $sql_save_details = " UPDATE smartdb.sm10_set SET rr_extract_date='$extract_date', rr_extract_user='$extract_user'; ";
    mysqli_multi_query($con,$sql_save_details);

    $sql_save = "TRUNCATE TABLE smartdb.sm16_file; ";
    mysqli_multi_query($con,$sql_save);
    
    $abbrevs       = $arr['abbrevs'];

    foreach($abbrevs as $abbRow) {
         $file_type     = $abbRow['file_type'];
         $file_ref      = $abbRow['file_ref'];
         $file_desc     = $abbRow['file_desc'];
         // echo "<br>".$file_type."   ".$file_ref."  ".$file_desc;
         $sql_save = "INSERT INTO smartdb.sm16_file (file_type,file_ref,file_desc) VALUES ('".$file_type."','".$file_ref."','".$file_desc."'); ";
         mysqli_multi_query($con,$sql_save);
    }

    // Update the RR with the updated abbreviations
    $sql_save = "UPDATE smartdb.sm12_rwr SET ParentName=(SELECT file_desc FROM smartdb.sm16_file WHERE file_type='abbrev_owner' AND file_ref=SUBSTRING(smartdb.sm12_rwr.Asset,1,1)), Class=(SELECT file_desc FROM smartdb.sm16_file WHERE file_type='abbrev_class' AND file_ref=SUBSTRING(smartdb.sm12_rwr.Asset,2,1)), Asset=SUBSTRING(smartdb.sm12_rwr.Asset,3)";
    mysqli_multi_query($con,$sql_save);

    $sql_save = "UPDATE smartdb.sm10_set SET rr_count = (SELECT COUNT(*) AS rr_count FROM smartdb.sm12_rwr) WHERE smartm_id =1";
    mysqli_multi_query($con,$sql_save);
}




Function fnUpload_is_audit($arr, $dev){
    // global $con;
    $stk_id                  = $arr['stk_id'];
    $stk_name                = $arr['stk_name'];
    $dpn_extract_date        = $arr['dpn_extract_date'];
    $dpn_extract_user        = $arr['dpn_extract_user'];
    $smm_extract_date        = $arr['smm_extract_date'];
    $smm_extract_user        = $arr['smm_extract_user'];
    $rc_orig                 = $arr['rc_orig'];
    $rc_orig_complete        = $arr['rc_orig_complete'];
    $rc_extras               = $arr['rc_extras'];

    if(empty($smm_extract_date)){
         $smm_extract_date="null";
    }else{
         $smm_extract_date="'".$smm_extract_date."'";
    }
    $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,dpn_extract_date,dpn_extract_user,smm_extract_date,smm_extract_user,rc_orig,rc_orig_complete, rc_extras, stk_type) VALUES ('".$stk_id."','".$stk_name."','".$dpn_extract_date."','".$dpn_extract_user."',".$smm_extract_date.",'".$smm_extract_user."','".$rc_orig."','".$rc_orig_complete."','".$rc_extras."','impairment'); ";
    if (true) { echo "<br>sql_save: ".$sql_save; }
    mysqli_multi_query($con,$sql_save);

    $sql = "SELECT * FROM smartdb.sm13_stk ORDER BY stkm_id DESC LIMIT 1;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
              $stkm_id_new    = $row["stkm_id"];
    }}
    $assets   = $arr['results'];

    
    $stmt   = $con->prepare("INSERT INTO smartdb.sm18_impairment (
        stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, 
        SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, 
        BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, 
        TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, 
        isType, targetID, delete_date, delete_user, res_create_date, 
        res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, 
        isChild, res_parent_storageID, fingerprint) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
    foreach($assets as $ass) {
        //  foreach($ass as $fieldname => $fieldvalue) {
        //       $ass[$fieldname] = cleanvalue($ass[$fieldname]);
        //  }
         $stmt   ->bind_param("sssssssssssssssssssssssssssssssss", 
                                $stkm_id_new, $ass['storageID'], $ass['rowNo'], $ass['DSTRCT_CODE'], $ass['WHOUSE_ID'], 
                                $ass['SUPPLY_CUST_ID'], $ass['SC_ACCOUNT_TYPE'], $ass['STOCK_CODE'], $ass['ITEM_NAME'], $ass['STK_DESC'], 
                                $ass['BIN_CODE'], $ass['INVENT_CAT'], $ass['INVENT_CAT_DESC'], $ass['TRACKING_IND'], $ass['SOH'], 
                                $ass['TRACKING_REFERENCE'], $ass['LAST_MOD_DATE'], $ass['sampleFlag'], $ass['serviceableFlag'], $ass['isBackup'], 
                                $ass['actType'], $ass['targetID'], $ass['delete_date'], $ass['delete_user'], $ass['res_create_date'], 
                                $ass['res_update_user'], $ass['findingID'], $ass['res_comment'], $ass['res_evidence_desc'], $ass['res_unserv_date'], 
                                $ass['isChild'], $ass['res_parent_storageID'], $ass['fingerprint']);
         $stmt   ->execute();


        //  $sql_save=" INSERT INTO smartdb.sm18_impairment (
        //       stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, fingerprint
        //  ) VALUES(".
         
        //  $stkm_id_new.",".$ass['storageID'].",".$ass['rowNo'].",".$ass['DSTRCT_CODE'].",".$ass['WHOUSE_ID'].",".$ass['SUPPLY_CUST_ID'].",".$ass['SC_ACCOUNT_TYPE'].",".$ass['STOCK_CODE'].",".$ass['ITEM_NAME'].",".$ass['STK_DESC'].",".$ass['BIN_CODE'].",".$ass['INVENT_CAT'].",".$ass['INVENT_CAT_DESC'].",".$ass['TRACKING_IND'].",".$ass['SOH'].",".$ass['TRACKING_REFERENCE'].",".$ass['LAST_MOD_DATE'].",".$ass['sampleFlag'].",".$ass['serviceableFlag'].",".$ass['isBackup'].",".$ass['isType'].",".$ass['targetID'].",".$ass['delete_date'].",".$ass['delete_user'].",".$ass['res_create_date'].",".$ass['res_update_user'].",".$ass['findingID'].",".$ass['res_comment'].",".$ass['res_evidence_desc'].",".$ass['res_unserv_date'].",".$ass['isChild'].",".$ass['res_parent_storageID'].",".$ass['fingerprint']." ); ";
        //  mysqli_multi_query($con,$sql_save);
    }
    // fnStats($stkm_id);



}







?>