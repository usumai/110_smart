<?php
include "../01_dbcon.php"; 
include "../05_scripts.php";
$debugMode  = true;
$log = "";

fnGetStkDetails();


function fnGetStkDetails(){// Returns stkm_id_one, stkm_id_two, stkm_id_new
    global $con;
    $stkd = [];
    $cherry = 0;

    $sql = "SELECT * FROM smartdb.sm13_stk WHERE  stk_include = 1 and smm_delete_date IS NULL";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if ($cherry==0){
                $cherry=1;
                $stkm_id_one    = $row["stkm_id"];
            }else{
                $stkm_id_two    = $row["stkm_id"];
            }
            $stk_id             = $row["stk_id"];
            $stk_name           = $row["stk_name"];
            $dpn_extract_date   = $row["dpn_extract_date"];
            $stk_type           = $row["stk_type"];
    }}

    // Create a new name for the stocktake
    if(strpos($stk_name, "MERGE")===false){
         $stk_name_disp = "MERGE_".$stk_name;
    }else{
         $stk_name_disp = $stk_name;
    }

    //Creating new stocktake record
    $sql = "    INSERT INTO smartdb.sm13_stk (stk_id, stk_name,dpn_extract_date,stk_type, merge_lock)
                VALUES ('$stk_id','$stk_name_disp','$dpn_extract_date','".$stk_type."', 1)";
    runSql($sql);
    
    // Get newly created stkm_id
    $sql = "SELECT MAX(stkm_id) AS stkm_id_new  FROM smartdb.sm13_stk";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $stkm_id_new = $row['stkm_id_new'];   
    }}

    if ($stk_type=="impairment") {
        $tableName      = "smartdb.sm18_impairment";
        $capableOfFF    = false;
        
        $pkName         = "auto_storageID";
        $storageIDName  = "storageID";
        $sqlFilter      = " AND LEFT(isType,3)='imp' AND storageID<>0";
        $mergeType      = "imp";
        fnMerge($stk_type, $mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF, $stkm_id_new);
        $pkName         = "BIN_CODE";
        $storageIDName  = "BIN_CODE";
        $sqlFilter      = "  ";
        $mergeType      = "b2r";
        fnMerge($stk_type, $mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF, $stkm_id_new);
        
    }elseif ($stk_type=="stocktake") {
        // $tableName      = "smartdb.sm18_impairment";
        // $capableOfFF    = false;        
    }

}








function fnMerge($stk_type, $mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF, $stkm_id_new){
    global $con;
    $qar    = [];
    $rows   = "";
    
    if($mergeType=="b2r"){    
        $sql1 = "SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment 
        WHERE isType='b2r' AND stkm_id=$stkm_id_one GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE";
        $sql2 = "SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment 
        WHERE isType='b2r' AND stkm_id=$stkm_id_two GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE";
        $sql1   = "($sql1) AS vt1";
        $sql2   = "($sql2) AS vt2";
    }else{ 
        $qBldr  = "SELECT $pkName AS pkID, $storageIDName AS stID, fingerprint AS fpID FROM $tableName WHERE $pkName IS NOT NULL $sqlFilter";
        $sql1   = "($qBldr AND stkm_id=$stkm_id_one) AS vt1";
        $sql2   = "($qBldr AND stkm_id=$stkm_id_two) AS vt2";
        $sql1n  = "($qBldr AND stkm_id=$stkm_id_one AND $storageIDName IS NULL) AS vt1";
        $sql2n  = "($qBldr AND stkm_id=$stkm_id_two AND $storageIDName IS NULL) AS vt2";
    }


    $qar["a"]["t"] = $title = "Full match";
    $qar["a"]["d"] = "Match on storage and fingerprint";
    $qar["a"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fpID2, '$title' AS gcat, vt1.pkID
                        FROM $sql1, $sql2 
                        WHERE vt1.stID = vt2.stID
                        AND  vt1.fpID = vt2.fpID";

    $qar["b"]["t"] = $title = "Single stk result(STK1)";
    $qar["b"]["d"] = "Storage ID exists in both files, but only one has a result";
    $qar["b"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fpID2, '$title' AS gcat, vt1.pkID
                        FROM $sql1, $sql2 
                        WHERE vt1.stID = vt2.stID
                        AND vt1.fpID IS NOT NULL
                        AND vt2.fpID IS NULL";

    $qar["c"]["t"] = $title = "Single stk result(STK2)";
    $qar["c"]["d"] = "Storage ID exists in both files, but only one has a result";
    $qar["c"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fpID2, '$title' AS gcat, vt2.pkID
                        FROM $sql1, $sql2 
                        WHERE vt1.stID = vt2.stID
                        AND vt1.fpID IS NULL
                        AND vt2.fpID IS NOT NULL";

    if($capableOfFF){
        $qar["d"]["t"] = $title = "FF Match";
        $qar["d"]["d"] = "Storage ID doesn't exist, but record was found in both stks with identical fingerprint. This cannot happen on the first merge. It can only happen once two stocktakes have been merged, then two of those merged stocktakes are merged with each other. ";
        $qar["d"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fpID2, '$title' AS gcat, vt1.pkID
                            FROM $sql1n, $sql2n
                            WHERE vt1.fpID = vt2.fpID";

        $qar["e"]["t"] = $title = "FF STK1";
        $qar["e"]["d"] = "A first found exists in STK1 and has no record of a matching fingerprint with STK2";
        $qar["e"]["q"] = "  SELECT NULL AS stID1, fingerprint AS fpID1, NULL AS stID2, NULL AS fpID2, '$title' AS gcat, $pkName AS pkID
                            FROM $tableName
                            WHERE stkm_id = $stkm_id_one
                            AND $storageIDName IS NULL
                            AND fingerprint IS NOT NULL
                            AND $pkName NOT IN (
                                SELECT vt1.pkID
                                FROM $sql1n, $sql2n
                                WHERE vt1.fpID = vt2.fpID)";

        $qar["f"]["t"] = $title = "FF STK2";
        $qar["f"]["d"] = "A first found exists in STK2 and has no record of a matching fingerprint with STK1";
        $qar["f"]["q"] = "  SELECT NULL AS stID1, fingerprint AS fpID1, NULL AS stID2, NULL AS fpID2, '$title' AS gcat, $pkName AS pkID
                            FROM $tableName
                            WHERE stkm_id = $stkm_id_two
                            AND $storageIDName IS NULL
                            AND fingerprint IS NOT NULL
                            AND $pkName NOT IN (
                                SELECT vt1.pkID
                                FROM $sql1n, $sql2n
                                WHERE vt1.fpID = vt2.fpID)";
    }
    $qar["g"]["t"] = $title = "No result";
    $qar["g"]["d"] = "The record exists in both stocktakes, but neither have a result recorded against them";
    $qar["g"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fpID2, '$title' AS gcat, vt1.pkID
                        FROM $sql1, $sql2 
                        WHERE vt1.stID = vt2.stID
                        AND vt1.fpID IS NULL
                        AND vt2.fpID IS NULL";

    $qar["h"]["t"] = $title = "Conflict";
    $qar["h"]["d"] = "Both stocktakes have a record of this, but they have different fingerprints. This is only applicable to original records";
    $qar["h"]["q"] = "  SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fp2, '$title' AS gcat, 
                        vt1.pkID AS pkID1, vt2.pkID AS pkID2
                        FROM $sql1, $sql2 
                        WHERE vt1.stID = vt2.stID
                        AND vt1.fpID IS NOT NULL
                        AND vt2.fpID IS NOT NULL
                        AND vt1.fpID <> vt2.fpID";

    $sql_allgood        = $qar["a"]["q"]." UNION ".$qar["b"]["q"]." UNION ".$qar["c"]["q"]." UNION ".$qar["g"]["q"];
    // $log .= !$debugMode ? $log: "<br><br><br><b>sql_allgood</b>$sql_allgood";

    $sql_needscomparison= $qar["h"]["q"];
    // $log .= !$debugMode ? $log: "<br><br><br><b>sql_needscomparison</b><br>$sql_h";






    
    foreach ($qar as $key => $value) {
        $recordCount=0;
        $recordCount = fnCountSQL($value["q"]);
        $extrapolatedCount = "";
        if ($mergeType=="b2r") {
            // echo "<br>".$key;
            $stkm_id_target = $stkm_id_one;
            if($key=="c"){
                $stkm_id_target = $stkm_id_two;
            }
            if($key!="h"){
                $extrapolatedSql = "SELECT * FROM smartdb.sm18_impairment WHERE BIN_CODE IN (SELECT pkID FROM (".$value["q"].") AS vtFull ) AND stkm_id=$stkm_id_target";
                $extrapolatedCount = fnCountSQL($extrapolatedSql);
            }
        }
        $rows.= "<tr>
                    <td>".$key."</td>
                    <td>".$value["t"]."</td>
                    <td>".$value["d"]."</td>
                    <td>".$value["q"]."</td>
                    <td>".$recordCount."</td>
                    <td>".$extrapolatedCount."</td>
                </tr>";
    }
    $table = "<table border=1><tr><td>$mergeType</td></tr><tr><td>Serial</td><td>Title</td><td width='20%'>Description</td><td>Query</td><td>Count</td><td>Result</td></tr>$rows</table>";
    echo $table;










    //Action area
    if ($stk_type=="impairment"&&$mergeType=="imp") {
        $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
        SELECT $stkm_id_new, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
        FROM smartdb.sm18_impairment
        WHERE auto_storageID IN (SELECT pkID FROM ($sql_allgood) AS vt_merge_allgood);";
        //  echo "<br><br><br>$sql";
        runSql($sql);
    
    
        $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
        SELECT $stkm_id_new, stID1, stID2 FROM ($sql_needscomparison) AS vtCompare;";
        // echo "<br><br><br>$sql";
        runSql($sql);
    }elseif ($stk_type=="impairment"&&$mergeType=="b2r") {
        // B2R merge process has a list of the rows aggregated to bin_code, when the transfer happens it needs to extrapolate this to include transactional records

        // Take everything from stocktake 1 except for stocktake 2 exceptions
        $sql_base = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
        SELECT $stkm_id_new, storageID, rowNo, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
        FROM smartdb.sm18_impairment ";
        
        $sql_b2r_stk1  = $qar["a"]["q"]." UNION ".$qar["b"]["q"]." UNION ".$qar["g"]["q"];
        $sql_b2r_stk2  = $qar["c"]["q"];
        $sql_b2r_stk1a = $sql_base." WHERE BIN_CODE IN (SELECT pkID FROM ($sql_b2r_stk1) AS vt_merge_allgood) AND stkm_id=$stkm_id_one;";
        $sql_b2r_stk2a = $sql_base." WHERE BIN_CODE IN (SELECT pkID FROM ($sql_b2r_stk2) AS vt_merge_allgood) AND stkm_id=$stkm_id_two;";
        // echo "<br><br><br><h1>sql_b2r_stk1</h1>$sql_b2r_stk1a";
        // echo "<br><br><br>$sql_b2r_stk2a";
        runSql($sql_b2r_stk1a);
        runSql($sql_b2r_stk2a);
        
        
            $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
            SELECT $stkm_id_new, stID1, stID2 FROM ($sql_needscomparison) AS vtCompare;";
            // echo "<br><br><br>$sql";
            runSql($sql);
    }




















    // mysqli_multi_query($con,$sql_allgood);
    // mysqli_multi_query($con,$sql_needscomparison);


}// Thus ends the reign of fnMerge




function fnCountSQL($stmt){
    global $con;
    $sql = "SELECT COUNT(*) AS recCount FROM ($stmt) AS vt";
    // echo "<br><br>".$sql;
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $recCount = $row['recCount'];   
    }}
    return $recCount;
}
?>
