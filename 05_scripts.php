<?php //include "01_dbcon.php"; ?><?php

function runSql($stmt){
    global $con;
    if (!mysqli_multi_query($con,$stmt)){
         $save_error = mysqli_error($con);
         $log ='failure: '.$save_error;
    }else{
         $log ='success';     
    }
    return $log;
}














function cleanvalue($fieldvalue) {
    // $fieldvalue = str_replace("'", "\'", $fieldvalue);
    // $fieldvalue = str_replace('"', '\"', $fieldvalue);
    $fieldvalue = str_replace("'", "''", $fieldvalue);
    $fieldvalue = str_replace('"', '""', $fieldvalue);
    // $fieldvalue = str_replace("""", "/""", $fieldvalue);
    if ($fieldvalue=="") {
         $fieldvalue="NULL";
    }elseif (empty($fieldvalue)) {
         $fieldvalue="NULL";
    }elseif ($fieldvalue=="NULL") {
         $fieldvalue="NULL";
    }elseif ($fieldvalue=="null") {
         $fieldvalue="NULL";
    }elseif (strlen($fieldvalue)==0) {
         $fieldvalue="NULL";
    }else{
         $fieldvalue="'".$fieldvalue."'";
    }
    return $fieldvalue;
}

function checkExtrasFinished($BIN_CODE, $stkm_id){
    global $con;

    $fingerprint        = time();
    $sql = "SELECT COUNT(*) AS extraCount, SUM(CASE WHEN finalResult IS NULL THEN 0 ELSE 1 END) AS extraComplete FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE' AND isChild=1 AND isType='b2r' AND delete_date IS NULL";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
         $extraCount    = $row['extraCount']; 
         $extraComplete = $row['extraComplete'];  
    }}
    if($extraCount==$extraComplete){
         $sql = "UPDATE smartdb.sm18_impairment SET 
         findingID=16,
         fingerprint='$fingerprint'
         WHERE BIN_CODE='$BIN_CODE' 
         AND isType='b2r' 
         AND stkm_id=$stkm_id";
    }else{
         $sql = "UPDATE smartdb.sm18_impairment SET 
         findingID=15
         WHERE BIN_CODE='$BIN_CODE' 
         AND isType='b2r' 
         AND stkm_id=$stkm_id";
    }
    runSql($sql);
    
    fnStats($stkm_id);
}
































function fnUpload_stocktake($arr, $dev){
    global $con;
    $stk_id                  = $arr['stk_id'];
    $stk_name                = $arr['stk_name'];
    $dpn_extract_date        = cleanvalue($arr['dpn_extract_date']);
    $dpn_extract_user        = cleanvalue($arr['dpn_extract_user']);
    $smm_extract_date        = cleanvalue($arr['smm_extract_date']);
    $smm_extract_user        = cleanvalue($arr['smm_extract_user']);
    $rc_orig                 = $arr['rc_orig'];
    $rc_orig_complete        = $arr['rc_orig_complete'];
    $rc_extras               = $arr['rc_extras'];
    $assets                  = $arr['results'];
    if ($dev) {
         echo "<br>stk_id:".$stk_id ."<br>stk_name:".$stk_name ."<br>dpn_extract_date:".$dpn_extract_date ."<br>dpn_extract_user:".$dpn_extract_user ."<br>smm_extract_date:".$smm_extract_date ."<br>smm_extract_user:".$smm_extract_user ."<br>rc_orig:".$rc_orig ."<br>rc_orig_complete:".$rc_orig_complete."<br>rc_extras:".$rc_extras;
    }
    $sql_save = "INSERT INTO smartdb.sm13_stk (stk_id,stk_name,rc_orig,stk_type) VALUES ('".$stk_id."','".$stk_name."','".$rc_orig."','stocktake'); ";
    if ($dev) { echo "<br>sql_save: ".$sql_save; }
    mysqli_multi_query($con,$sql_save);
    echo "<br><br>sql_save: ".$sql_save."<br><br>";
    
    $sql = "SELECT * FROM smartdb.sm13_stk ORDER BY stkm_id DESC LIMIT 1;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
              $stkm_id_new    = $row["stkm_id"];
    }}

    // Get a list of the sql fields- the array fields need to match the db for this system to work
    $keys = array_keys($assets["0"]);
    unset($keys[0]); //Remove ass_id since it is a primary key
    unset($keys[107]);//Remove the last array item which is a 'end' holder
    $tags = implode(', ', $keys);

    if(end($assets)['ass_id']=="END") {//We don't want this to happen for exports from the DPN (Which have this)
         array_pop($assets);// Remove the last asset from the array- it is an 'end' holder
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    foreach($assets as $ass) {
         foreach($ass as $fieldname => $fieldvalue) {
              $ass[$fieldname] = cleanvalue($ass[$fieldname]);
         }
         $sql_save=" INSERT INTO smartdb.sm14_ass ($tags) VALUES(".$ass['create_date'].",".$ass['create_user'].",".$ass['delete_date'].",".$ass['delete_user'].",".$stkm_id_new.",".$ass['storage_id'].",".$ass['stk_include'].",".$ass['Asset'].",".$ass['Subnumber'].",".$ass['genesis_cat'].",".$ass['first_found_flag'].",".$ass['rr_id'].",".$ass['fingerprint'].",".$ass['res_create_date'].",".$ass['res_create_user'].",".$ass['res_reason_code'].",".$ass['res_reason_code_desc'].",".$ass['res_completed'].",".$ass['res_comment'].",".$ass['AssetDesc1'].",".$ass['AssetDesc2'].",".$ass['AssetMainNoText'].",".$ass['Class'].",".$ass['assetType'].",".$ass['Inventory'].",".$ass['Quantity'].",".$ass['SNo'].",".$ass['InventNo'].",".$ass['accNo'].",".$ass['Location'].",".$ass['Room'].",".$ass['State'].",".$ass['latitude'].",".$ass['longitude'].",".$ass['CurrentNBV'].",".$ass['AcqValue'].",".$ass['OrigValue'].",".$ass['ScrapVal'].",".$ass['ValMethod'].",".$ass['RevOdep'].",".$ass['CapDate'].",".$ass['LastInv'].",".$ass['DeactDate'].",".$ass['PlRetDate'].",".$ass['CCC_ParentName'].",".$ass['CCC_GrandparentName'].",".$ass['GrpCustod'].",".$ass['CostCtr'].",".$ass['WBSElem'].",".$ass['Fund'].",".$ass['RspCCtr'].",".$ass['CoCd'].",".$ass['PlateNo'].",".$ass['Vendor'].",".$ass['Mfr'].",".$ass['UseNo'].",".$ass['res_AssetDesc1'].",".$ass['res_AssetDesc2'].",".$ass['res_AssetMainNoText'].",".$ass['res_Class'].",".$ass['res_assetType'].",".$ass['res_Inventory'].",".$ass['res_Quantity'].",".$ass['res_SNo'].",".$ass['res_InventNo'].",".$ass['res_accNo'].",".$ass['res_Location'].",".$ass['res_Room'].",".$ass['res_State'].",".$ass['res_latitude'].",".$ass['res_longitude'].",".$ass['res_CurrentNBV'].",".$ass['res_AcqValue'].",".$ass['res_OrigValue'].",".$ass['res_ScrapVal'].",".$ass['res_ValMethod'].",".$ass['res_RevOdep'].",".$ass['res_CapDate'].",".$ass['res_LastInv'].",".$ass['res_DeactDate'].",".$ass['res_PlRetDate'].",".$ass['res_ccc_name'].",".$ass['res_ccc_grandparent_name'].",".$ass['res_GrpCustod'].",".$ass['res_CostCtr'].",".$ass['res_WBSElem'].",".$ass['res_Fund'].",".$ass['res_RspCCtr'].",".$ass['res_CoCd'].",".$ass['res_PlateNo'].",".$ass['res_Vendor'].",".$ass['res_Mfr'].",".$ass['res_UseNo'].",".$ass['flagTemplate']."); ";
     //     echo "<br><br>".$sql_save;
         mysqli_multi_query($con,$sql_save);
    }
    $sql = "UPDATE smartdb.sm14_ass SET stk_include=0 WHERE stkm_id=$stkm_id_new; ";
    runSql($sql);
    fnStats($stkm_id_new);
}

// impairment_code
// res_impairment_completed
// classDesc
// res_classDesc
















function fnUpload_impairment($arr, $dev){
    global $con;
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

    foreach($assets as $ass) {
         foreach($ass as $fieldname => $fieldvalue) {
              $ass[$fieldname] = cleanvalue($ass[$fieldname]);
         }
         $sql_save=" INSERT INTO smartdb.sm18_impairment (
              stkm_id, storageID,  DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, fingerprint
         ) VALUES(".
         
         $stkm_id_new.",".$ass['storageID'].",".$ass['DSTRCT_CODE'].",".$ass['WHOUSE_ID'].",".$ass['SUPPLY_CUST_ID'].",".$ass['SC_ACCOUNT_TYPE'].",".$ass['STOCK_CODE'].",".$ass['ITEM_NAME'].",".$ass['STK_DESC'].",".$ass['BIN_CODE'].",".$ass['INVENT_CAT'].",".$ass['INVENT_CAT_DESC'].",".$ass['TRACKING_IND'].",".$ass['SOH'].",".$ass['TRACKING_REFERENCE'].",".$ass['LAST_MOD_DATE'].",".$ass['sampleFlag'].",".$ass['serviceableFlag'].",".$ass['isBackup'].",".$ass['isType'].",".$ass['targetID'].",".$ass['delete_date'].",".$ass['delete_user'].",".$ass['res_create_date'].",".$ass['res_update_user'].",".$ass['findingID'].",".$ass['res_comment'].",".$ass['res_evidence_desc'].",".$ass['res_unserv_date'].",".$ass['isChild'].",".$ass['res_parent_storageID'].",".$ass['fingerprint']." ); ";
         mysqli_multi_query($con,$sql_save);
    }
    fnStats($stkm_id);
}




















function fnUpload_rawremainder($arr, $dev){
    global $con;
    $extract_date  = $arr['extract_date'];
    $extract_user  = $arr['extract_user'];
    if ($dev) { echo "<br>extract_date:".$extract_date; }
    if ($dev) { echo "<br>extract_user:".$extract_user."<br>"; }
    ini_set('max_execution_time', 30000); //300 seconds = 5 minutes

    $sql_delete = "TRUNCATE TABLE smartdb.smart_l03_rr; ";
    mysqli_multi_query($con,$sql_delete);
    $assetRows     = $arr['assetRows'];
    foreach($assetRows as $assetRow) {
         $Asset              = $assetRow['f1'];
         if ($Asset!="END") {
              $accNo         = $assetRow['f2'];
              $InventNo      = $assetRow['f3'];
              $AssetDesc1    = $assetRow['f4'];  
              $sql_save = "INSERT INTO smartdb.sm12_rwr (Asset,accNo,InventNo,AssetDesc1) VALUES ('$Asset','$accNo','$InventNo','$AssetDesc1'); ";    
                   mysqli_multi_query($con,$sql_save);
         }
    }
    $sql_save_details = " UPDATE smartdb.sm10_set SET rr_extract_date='$extract_date', rr_extract_user='$extract_user'; ";
    mysqli_multi_query($con,$sql_save_details);

    $sql_save = "TRUNCATE TABLE smartdb.sm16_file; ";
    mysqli_multi_query($con,$sql_save);
    
    $abbrevs       = $arr['abbrevs'];
    //print_r($abbrevs);
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




























// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 'On');
// error_reporting(-1);
// // mysqli_report(MYSQLI_REPORT_ALL); 


// function fnStats($stkm_id){
//      global $con;
 
//      $sql_rc_orig = "SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_orig FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";
 
//      $sql_rc_orig_complete = "SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL  AND res_reason_code IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";
 
//      $sql_rc_extras = "SELECT SUM(CASE WHEN  first_found_flag=1 AND flagTemplate IS NULL THEN 1 WHEN rr_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_extras FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL ";
 
 
//      $sql_save = "UPDATE smartdb.sm13_stk SET 
//           rc_orig=($sql_rc_orig),
//           rc_orig_complete=($sql_rc_orig_complete),
//           rc_extras=($sql_rc_extras)
//           WHERE stkm_id = $stkm_id;";
//      // echo $sql_save;
//      mysqli_multi_query($con,$sql_save);
 
//  }

// function fnCalcStkStats($stkm_id){
//      global $con;
//      $rc_orig            = 0;
//      $rc_orig_complete   = 0;
//      $rc_extras          = 0;

//      $sql1 = " SELECT 
//                COUNT(DISTINCT BIN_CODE, DSTRCT_CODE) AS rc_orig, 
//                COUNT(DISTINCT CASE WHEN res_create_date THEN BIN_CODE ELSE NULL END) AS rc_orig_complete,
//                COUNT(DISTINCT CASE WHEN storageID IS NULL THEN BIN_CODE ELSE NULL END) AS rc_extras
//                FROM smartdb.sm18_impairment
//                WHERE isType='b2r'
//                AND delete_date IS NULL 
//                AND isBackup IS NULL
//                AND stkm_id=$stkm_id";
//      $sql2 = "SELECT 
//                SUM(CASE WHEN storageID IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig, 
//                SUM(CASE WHEN storageID IS NOT NULL AND res_create_date IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete, 
//                SUM(CASE WHEN res_parent_storageID IS NOT NULL THEN 1 ELSE 0 END) AS rc_extras
//                FROM smartdb.sm18_impairment 
//                WHERE delete_date IS NULL 
//                AND LEFT(isType,3)='imp' 
//                AND sampleFlag=1 
//                AND isBackup IS NULL
//                AND stkm_id= $stkm_id 
//                GROUP BY stkm_id";


//      $sql3 = "$sql1 UNION ALL $sql2";
//      $sql4 = "SELECT SUM(rc_orig) AS rc_orig, SUM(rc_orig_complete) AS rc_orig_complete, SUM(rc_extras) AS rc_extras FROM ($sql3) AS vt";
//      // echo "<br><br>$sql4";
//      $result3 = $con->query($sql4);
//      if ($result3->num_rows > 0) {
//      while($row3 = $result3->fetch_assoc()) {
//           $rc_orig            = $row3["rc_orig"];
//           $rc_orig_complete   = $row3["rc_orig_complete"];
//           $rc_extras          = $row3["rc_extras"];
//      }}

//      $sql5 = " UPDATE smartdb.sm13_stk SET 
//                rc_orig = '$rc_orig', 
//                rc_orig_complete = '$rc_orig_complete', 
//                rc_extras = '$rc_extras' 
//                WHERE stkm_id =$stkm_id
//                ";
//      mysqli_multi_query($con,$sql5);
//  }






// function fnCalcImpairmentStats(){
//     global $con;

    
//     $sql = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include = 1 AND delete_date IS NULL";
//     $result = $con->query($sql);
//     if ($result->num_rows > 0) {
//         while($row = $result->fetch_assoc()) {
//               $stkm_id            = $row["stkm_id"];
//               $rc_orig            = 0;
//               $rc_orig_complete   = 0;

//               $sql_count_b2r_extras = "SELECT COUNT(*) AS rc_extras FROM smartdb.sm18_impairment 
//               WHERE isType='b2r' AND stkm_id=$stkm_id AND isChild IS NOT NULL AND delete_date IS NULL";
//               $sql1 = "SELECT 
//                         COUNT(*) as rc_orig, 
//                         SUM(CASE WHEN findingID = 14 THEN 1 WHEN findingID = 16 THEN 1 ELSE 0 END) as rc_orig_complete,
//                         ($sql_count_b2r_extras) AS rc_extras
//                         FROM (SELECT stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, isChild, findingID FROM smartdb.sm18_impairment WHERE isType='b2r' AND delete_date IS NULL AND storageID=1  AND isBackup IS NULL AND stkm_id=$stkm_id GROUP BY stkm_id, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, isChild, findingID) AS vtOne";
//               $sql2 = "SELECT 
//                         SUM(CASE WHEN storageID IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig, 
//                         SUM(CASE WHEN storageID IS NOT NULL AND res_create_date IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete, 
//                         SUM(CASE WHEN res_parent_storageID IS NOT NULL THEN 1 ELSE 0 END) AS rc_extras
//                         FROM smartdb.sm18_impairment 
//                         WHERE delete_date IS NULL 
//                         AND LEFT(isType,3)='imp' 
//                         AND sampleFlag=1 
//                         AND isBackup IS NULL
//                         AND stkm_id= $stkm_id 
//                         GROUP BY stkm_id";


//               $sql3 = "$sql1 UNION $sql2";
//           //     echo $sql3;
//               $sql4 = "SELECT SUM(rc_orig) AS rc_orig, SUM(rc_orig_complete) AS rc_orig_complete, SUM(rc_extras) AS rc_extras FROM ($sql3) AS vt";
//           //     echo "<br><br>$sql4";
//               $result2 = $con->query($sql4);
//               if ($result2->num_rows > 0) {
//               while($row2 = $result2->fetch_assoc()) {
//                    $rc_orig            = $row2["rc_orig"];
//                    $rc_orig_complete   = $row2["rc_orig_complete"];
//                    $rc_extras          = $row2["rc_extras"];
//               }}

//               $sql5 = " UPDATE smartdb.sm13_stk SET 
//                         rc_orig = '$rc_orig', 
//                         rc_orig_complete = '$rc_orig_complete', 
//                         rc_extras = '$rc_extras' 
//                         WHERE stkm_id =$stkm_id
//                         ";
//               mysqli_multi_query($con,$sql5);

//     }}


// }









function fnStats($stkm_id){
     global $con;
     $rc_orig            = 0;
     $rc_orig_complete   = 0;
     $rc_extras          = 0;

     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=$stkm_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
               $stk_type           = $row["stk_type"];
     }}
     // echo "stk_type:$stk_type";
     if ($stk_type=="stocktake") {

          $sql_rc_orig = "              SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_orig 
                                        FROM smartdb.sm14_ass 
                                        WHERE stkm_id=$stkm_id 
                                        AND delete_date IS NULL ";
 
          $sql_rc_orig_complete = "     SELECT 
                                             SUM(CASE 
                                                  WHEN storage_id IS NOT NULL 
                                                  AND flagTemplate IS NULL  
                                                  AND res_reason_code IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete 
                                        FROM smartdb.sm14_ass 
                                        WHERE stkm_id=$stkm_id 
                                        AND delete_date IS NULL ";
      
          $sql_rc_extras = "            SELECT 
                                             SUM(CASE 
                                                  WHEN  first_found_flag=1 
                                                  AND flagTemplate IS NULL THEN 1 
                                                  WHEN rr_id IS NOT NULL 
                                                  AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_extras 
                                        FROM smartdb.sm14_ass 
                                        WHERE stkm_id=$stkm_id 
                                        AND delete_date IS NULL ";
      
          $sql_save = "  UPDATE smartdb.sm13_stk SET 
                              rc_orig=($sql_rc_orig),
                              rc_orig_complete=($sql_rc_orig_complete),
                              rc_extras=($sql_rc_extras)
                         WHERE stkm_id = $stkm_id;";
          echo $sql_rc_extras;
          mysqli_multi_query($con,$sql_save);
     }elseif ($stk_type=="impairment") {
          $sql1 = " SELECT 
                    COUNT(DISTINCT BIN_CODE, DSTRCT_CODE) AS rc_orig, 
                    COUNT(DISTINCT CASE WHEN res_create_date THEN BIN_CODE ELSE NULL END) AS rc_orig_complete,
                    0 AS rc_extras
                    FROM smartdb.sm18_impairment
                    WHERE isType='b2r'
                    AND delete_date IS NULL 
                    AND isBackup IS NULL
                    AND stkm_id=$stkm_id";
          $sql2 = "SELECT 
                    SUM(CASE WHEN storageID IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig, 
                    SUM(CASE WHEN storageID IS NOT NULL AND res_create_date IS NOT NULL THEN 1 ELSE 0 END) AS rc_orig_complete, 
                    SUM(CASE WHEN res_parent_storageID > 0 THEN 1 ELSE 0 END) AS rc_extras
                    FROM smartdb.sm18_impairment 
                    WHERE delete_date IS NULL 
                    AND LEFT(isType,3)='imp' 
                    AND sampleFlag=1 
                    AND isBackup IS NULL
                    AND stkm_id= $stkm_id 
                    GROUP BY stkm_id";

          $sql3 = "$sql1 UNION ALL $sql2";
          $sql4 = "SELECT SUM(rc_orig) AS rc_orig, SUM(rc_orig_complete) AS rc_orig_complete, SUM(rc_extras) AS rc_extras FROM ($sql3) AS vt";
          $result3 = $con->query($sql4);
          if ($result3->num_rows > 0) {
          while($row3 = $result3->fetch_assoc()) {
               $rc_orig            = $row3["rc_orig"];
               $rc_orig_complete   = $row3["rc_orig_complete"];
               $rc_extras          = $row3["rc_extras"];
          }}
          $sql5 = " UPDATE smartdb.sm13_stk SET 
                    rc_orig = '$rc_orig', 
                    rc_orig_complete = '$rc_orig_complete', 
                    rc_extras = '$rc_extras' 
                    WHERE stkm_id =$stkm_id";
          mysqli_multi_query($con,$sql5);
     }

}



?>