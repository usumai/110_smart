<?php
include "01_dbcon.php"; 
include "05_scripts.php";
$debugMode  = false;
// $debugMode  = true;
$log = "";
if(fnCheckPreMergeConditions()){
    echo $stk_type;
    fnGetStkDetails();
}


function fnCheckPreMergeConditions(){
    global $con, $debugMode, $stk_type;
    $sql = "SELECT count(*) AS mergeCount FROM smartdb.sm13_stk WHERE  stk_include = 1 and smm_delete_date IS NULL";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $mergeCount     = $row["mergeCount"];
    }}
    return ($mergeCount==2);
}










function fnGetSourceStkStats($stkm_id){
    global $con, $debugMode;

    $sql_rc_orig = "            SELECT SUM(CASE WHEN storage_id IS NOT NULL AND flagTemplate IS NULL THEN 1 ELSE 0 END) AS rc_orig 
                                FROM smartdb.sm14_ass 
                                WHERE stkm_id=$stkm_id 
                                AND delete_date IS NULL ";
    
    $sql_rc_orig_complete = "   SELECT SUM(CASE 
                                            WHEN storage_id IS NOT NULL 
                                            AND flagTemplate IS NULL 
                                            AND res_reason_code IS NOT NULL 
                                            THEN 1 ELSE 0 END) AS rc_orig_complete 
                                FROM smartdb.sm14_ass 
                                WHERE stkm_id=$stkm_id 
                                AND delete_date IS NULL ";

    $sql_rc_extras = "          SELECT SUM(CASE 
                                            WHEN  first_found_flag=1 
                                            AND flagTemplate IS NULL THEN 1 
                                            WHEN rr_id IS NOT NULL 
                                            AND flagTemplate IS NULL THEN 1 
                                            ELSE 0 END) AS rc_extras 
                                FROM smartdb.sm14_ass 
                                WHERE stkm_id=$stkm_id 
                                AND delete_date IS NULL ";
    
    $sqlStats = "SELECT 
                    ($sql_rc_orig) AS rc_orig,
                    ($sql_rc_orig_complete) AS rc_orig_complete,
                    ($sql_rc_extras) AS rc_extras";
    $result = $con->query($sqlStats);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $rc_orig            = $row["rc_orig"];
            $rc_orig_complete   = $row["rc_orig_complete"];
            $rc_extras          = $row["rc_extras"];
    }}
    
    $resultz = "<tr>
                    <td>$stkm_id</td>
                    <td>$rc_orig</td>
                    <td>$rc_orig_complete</td>
                    <td>$rc_extras</td>
                </tr>";

    return $resultz;
}




function fnGetSourceStats($stk_type, $stkm_id_one, $stkm_id_two){
    if ($stk_type=="stocktake") {
        $preStats  = fnGetSourceStkStats($stkm_id_one);
        $preStats .= fnGetSourceStkStats($stkm_id_two);
        $preStats  = "<table border='1'>
                        <tr>
                            <td>stkm_id</td>
                            <td>rc_orig</td>
                            <td>rc_orig_complete</td>
                            <td>rc_extras</td>
                        </tr>$preStats
                    </table>";
    }elseif ($stk_type=="impairment") {
        $preStats  = fnGetSourceImpStats($stkm_id_one);
        $preStats .= fnGetSourceImpStats($stkm_id_two);
        $preStats  = "<table border='1'>
                        <tr>
                            <td>stkm_id</td>
                            <td>impComplete</td>
                            <td>impTotal</td>
                            <td>b2rComplete</td>
                            <td>b2rTotal</td>
                            <td>binCount_stkm</td>
                            <td>XXX</td>
                            
                            <td>impPrimeComplete</td>
                            <td>impPrimeTotal</td>
                            <td>b2rPrimeComplete</td>
                            <td>b2rPrimeTotal</td>
                            <td>binCount_stkm_prime</td>
                        </tr>$preStats
                    </table>";
    }
    return $preStats;
}
function fnGetSourceImpStats($stkm_id){

    global $con, $debugMode;
    $sqlStats = "SELECT 
    SUM(CASE WHEN LEFT(isType,3)='imp' AND isBackup IS NULL AND res_create_date THEN 1 ELSE 0 END)  AS impPrimeComplete, 
    SUM(CASE WHEN LEFT(isType,3)='imp' AND isBackup IS NULL THEN 1 ELSE 0 END)	                    AS impPrimeTotal,
    SUM(CASE WHEN LEFT(isType,3)='imp' AND isBackup=1 AND res_create_date THEN 1 ELSE 0 END)        AS impBackupComplete,
    SUM(CASE WHEN LEFT(isType,3)='imp' AND isBackup=1 THEN 1 ELSE 0 END) 	                        AS impBackupTotal,
    SUM(CASE WHEN LEFT(isType,3)='imp' AND res_create_date THEN 1 ELSE 0 END)                       AS impComplete, 
    SUM(CASE WHEN LEFT(isType,3)='imp' THEN 1 ELSE 0 END)	                                        AS impTotal,
    SUM(CASE WHEN isType='b2r' AND isBackup IS NULL AND res_create_date THEN 1 ELSE 0 END)          AS b2rPrimeComplete,
    SUM(CASE WHEN isType='b2r' AND isBackup IS NULL THEN 1 ELSE 0 END)	                            AS b2rPrimeTotal,
    SUM(CASE WHEN isType='b2r' AND isBackup=1 AND res_create_date THEN 1 ELSE 0 END)                AS b2rBackupComplete,
    SUM(CASE WHEN isType='b2r' AND isBackup=1 THEN 1 ELSE 0 END) 	                                AS b2rBackupTotal,
    SUM(CASE WHEN isType='b2r' AND res_create_date THEN 1 ELSE 0 END)                               AS b2rComplete,
    SUM(CASE WHEN isType='b2r' THEN 1 ELSE 0 END)	                                                AS b2rTotal
    FROM smartdb.sm18_impairment
    WHERE stkm_id = $stkm_id";
        $result = $con->query($sqlStats);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $impPrimeComplete   = $row["impPrimeComplete"];
                $impPrimeTotal      = $row["impPrimeTotal"];
                // $impBackupComplete  = $row["impBackupComplete"];
                // $impBackupTotal     = $row["impBackupTotal"];
                $impComplete        = $row["impComplete"];
                $impTotal           = $row["impTotal"];
                $b2rPrimeComplete   = $row["b2rPrimeComplete"];
                $b2rPrimeTotal      = $row["b2rPrimeTotal"];
                // $b2rBackupComplete  = $row["b2rBackupComplete"];
                // $b2rBackupTotal     = $row["b2rBackupTotal"];
                $b2rComplete        = $row["b2rComplete"];
                $b2rTotal           = $row["b2rTotal"];
        }}



        $sqlStats = "SELECT count(DISTINCT BIN_CODE) AS binCount_stkm
        FROM smartdb.sm18_impairment
        WHERE stkm_id = $stkm_id
        AND isType='b2r'";
            $result = $con->query($sqlStats);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $binCount_stkm   = $row["binCount_stkm"];
        }}
        
        $sqlStats = "SELECT count(DISTINCT BIN_CODE) AS binCount_stkm_prime
        FROM smartdb.sm18_impairment
        WHERE stkm_id = $stkm_id
        AND isType='b2r' AND isBackup IS NULL ";
            $result = $con->query($sqlStats);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $binCount_stkm_prime   = $row["binCount_stkm_prime"];
        }}

        $resultz = "<tr>
                        <td>$stkm_id</td>
                        <td>$impComplete</td>
                        <td>$impTotal</td>
                        <td>$b2rComplete</td>
                        <td>$b2rTotal</td>
                        <td>$binCount_stkm</td>
                        <td>&nbsp;</td>
                        <td>$impPrimeComplete</td>
                        <td>$impPrimeTotal</td>
                        <td>$b2rPrimeComplete</td>
                        <td>$b2rPrimeTotal</td>
                        <td>$binCount_stkm_prime</td>

                    </tr>";

        return $resultz;
}





















function fnGetStkDetails(){// Returns stkm_id_one, stkm_id_two, stkm_id_new
    global $con, $debugMode;
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



    $preStats = fnGetSourceStats($stk_type, $stkm_id_one, $stkm_id_two);
    if ($debugMode){
        echo "These are the statistics of the stocktakes prior to the merge.<br>".$preStats;
    }

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
    if ($debugMode){
        echo "<br>Newly create merged stk_id:".$stkm_id_new;
    }

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
        $tableName      = "smartdb.sm14_ass";
        $capableOfFF    = true;
        
        $pkName         = "ass_id";
        $storageIDName  = "storage_id";
        $sqlFilter      = " ";
        $mergeType      = "stk";   
        fnMerge($stk_type, $mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF, $stkm_id_new);   
    }





    

    $sql = "SELECT COUNT(*) AS qCount FROM smartdb.sm20_quarantine WHERE stkm_id_new = '$stkm_id_new'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
         $qCount    = $row['qCount'];  
    }}

    $qCount = (empty($qCount) ? 0 : $qCount);
    if ($qCount>0){
         $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=1 WHERE stkm_id = $stkm_id_new;";
         $nextAddr = "Location: 22_merge.php?stkm_id=$stkm_id_new";
    }else{
         $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=NULL WHERE stkm_id = $stkm_id_new;";
         $nextAddr = "Location: index.php";
    }


    fnStats($stkm_id_new);

    if (!$debugMode){
        runSql($sql);
        header($nextAddr);
   }
}








function fnMerge($stk_type, $mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF, $stkm_id_new){
    global $con, $debugMode;
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
                            AND fingerprint NOT IN (
                                SELECT vt1.fpID
                                FROM $sql1n, $sql2n
                                WHERE vt1.fpID = vt2.fpID)";

        $qar["f"]["t"] = $title = "FF STK2";
        $qar["f"]["d"] = "A first found exists in STK2 and has no record of a matching fingerprint with STK1";
        $qar["f"]["q"] = "  SELECT NULL AS stID1, fingerprint AS fpID1, NULL AS stID2, NULL AS fpID2, '$title' AS gcat, $pkName AS pkID
                            FROM $tableName
                            WHERE stkm_id = $stkm_id_two
                            AND $storageIDName IS NULL
                            AND fingerprint IS NOT NULL
                            AND fingerprint NOT IN (
                                SELECT vt1.fpID
                                FROM $sql1n,$sql2n
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
        $extrapolatedCount = "na";
        if ($mergeType=="b2r") {
            $stkm_id_target = $stkm_id_one;
            if($key=="c"){
                $stkm_id_target = $stkm_id_two;
            }
            if($key!="h"){
                $extrapolatedSql = "SELECT * FROM smartdb.sm18_impairment WHERE BIN_CODE IN (SELECT pkID FROM (".$value["q"].") AS vtFull ) AND stkm_id=$stkm_id_target AND isType='b2r' ";
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
    $table = "<table border=1><tr><td>$mergeType</td></tr><tr><td>Serial</td><td>Title</td><td width='20%'>Description</td><td>Query</td><td>Count</td><td>Extrapolated Bins</td></tr>$rows</table>";
    if ($debugMode){
        echo $table;
    }








    //Action area
    if ($stk_type=="impairment"&&$mergeType=="imp") {
        $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
        SELECT $stkm_id_new, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
        FROM smartdb.sm18_impairment
        WHERE auto_storageID IN (SELECT pkID FROM ($sql_allgood) AS vt_merge_allgood);";
        runSql($sql);
    
    
        $sql = "  INSERT IGNORE INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
        SELECT $stkm_id_new, pkID1, pkID2 FROM ($sql_needscomparison) AS vtCompare;";
        $sql = "    INSERT INTO smartdb.sm20_quarantine (
                        stkm_id_new, stkm_id_one, stkm_id_two, isType, pkID1, pkID2, stID1 )
                    SELECT 
                        $stkm_id_new, $stkm_id_one, $stkm_id_two, 'imp', pkID1, pkID2, pkID1  
                    FROM ($sql_needscomparison) AS vtCompare;";
        runSql($sql);
    }elseif ($stk_type=="impairment"&&$mergeType=="b2r") {
        // B2R merge process has a list of the rows aggregated to bin_code, when the transfer happens it needs to extrapolate this to include transactional records

        // Take everything from stocktake 1 except for stocktake 2 exceptions
        $sql_base = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
        SELECT $stkm_id_new, storageID,  DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
        FROM smartdb.sm18_impairment ";
        
        $sql_b2r_stk1  = $qar["a"]["q"]." UNION ".$qar["b"]["q"]." UNION ".$qar["g"]["q"];
        $sql_b2r_stk2  = $qar["c"]["q"];
        $sql_b2r_stk1a = $sql_base." WHERE isType='b2r' AND BIN_CODE IN (SELECT pkID FROM ($sql_b2r_stk1) AS vt_merge_allgood) AND stkm_id=$stkm_id_one;";
        $sql_b2r_stk2a = $sql_base." WHERE isType='b2r' AND BIN_CODE IN (SELECT pkID FROM ($sql_b2r_stk2) AS vt_merge_allgood) AND stkm_id=$stkm_id_two;";
        runSql($sql_b2r_stk1a);
        runSql($sql_b2r_stk2a);

        $sql = "    INSERT IGNORE INTO smartdb.sm20_quarantine (
                        stkm_id_new, stkm_id_one, stkm_id_two, isType, stID1, BIN_CODE)
                    SELECT 
                        $stkm_id_new, $stkm_id_one, $stkm_id_two, '$mergeType', stID1 , stID1
                    FROM ($sql_needscomparison) AS vtCompare;";
        runSql($sql);
        
    }elseif ($stk_type=="stocktake") {

        $sql_allgood        = $qar["a"]["q"]." UNION ".$qar["b"]["q"]." UNION ".$qar["c"]["q"]." UNION ".$qar["d"]["q"]." UNION ".$qar["e"]["q"]." UNION ".$qar["f"]["q"]." UNION ".$qar["g"]["q"];


        $sql = "  INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
        SELECT create_date, create_user, delete_date, delete_user, $stkm_id_new, storage_id, 0, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
        FROM smartdb.sm14_ass
        WHERE ass_id IN (SELECT pkID FROM ($sql_allgood) AS vt_merge_allgood);";
        runSql($sql);





        $sql = "    INSERT IGNORE INTO smartdb.sm20_quarantine (
            stkm_id_new, stkm_id_one, stkm_id_two, isType, pkID1, pkID2, stID1 )
        SELECT 
            $stkm_id_new, $stkm_id_one, $stkm_id_two, 'stk', pkID1, pkID2, stID1  
        FROM ($sql_needscomparison) AS vtCompare;";
        runSql($sql);


    }



















    // mysqli_multi_query($con,$sql_allgood);
    // mysqli_multi_query($con,$sql_needscomparison);


}// Thus ends the reign of fnMerge




function fnCountSQL($stmt){
    global $con, $debugMode;
    $sql = "SELECT COUNT(*) AS recCount FROM ($stmt) AS vt";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $recCount = $row['recCount'];   
    }}
    return $recCount;
}
?>
