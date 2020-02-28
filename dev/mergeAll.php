<?php
include "../01_dbcon.php"; 
include "../05_scripts.php";
$debugMode  = true;
$log = "";

$mergeType          = "imp";
$tableName          = "smartdb.sm18_impairment";
$stkm_id_one        = 1;
$stkm_id_two        = 2;
$pkName             = "auto_storageID";
$storageIDName      = "storageID";
$sqlFilter          = " AND LEFT(isType,3)='imp' AND storageID<>0";
$capableOfFF        = false;
fnMerge($mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF);

$mergeType          = "b2r";
$tableName          = "smartdb.sm18_impairment";
$stkm_id_one        = 1;
$stkm_id_two        = 2;
$pkName             = "BIN_CODE";
$storageIDName      = "BIN_CODE";
$sqlFilter          = " ";
$capableOfFF        = false;
fnMerge($mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF);



function fnMerge($mergeType, $tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName, $sqlFilter, $capableOfFF){
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

    // $sql_allgood        = "$sql_a UNION $sql_b UNION $sql_c UNION $sql_d UNION $sql_e UNION $sql_f UNION $sql_g ";
    // $log .= !$debugMode ? $log: "<br><br><br><b>sql_allgood</b>$sql_allgood";

    // $sql_needscomparison= $sql_h;
    // $log .= !$debugMode ? $log: "<br><br><br><b>sql_needscomparison</b><br>$sql_h";

    foreach ($qar as $key => $value) {
        $recordCount = fnCountSQL($value["q"]);
        $rows.= "<tr>
                    <td>".$key."</td>
                    <td>".$value["t"]."</td>
                    <td>".$value["d"]."</td>
                    <td>".$value["q"]."</td>
                    <td>".$recordCount."</td>
                    <td></td>
                </tr>";
    }



    
    $table = "<table border=1><tr><td>$mergeType</td></tr><tr><td>Serial</td><td>Title</td><td width='20%'>Description</td><td>Query</td><td>Count</td><td>Result</td></tr>$rows</table>";
    echo $table;
}// Thus ends the reign of fnMerge




function fnCountSQL($stmt){
    global $con;
    $sql = "SELECT COUNT(*) AS recCount FROM ($stmt) AS vt";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $recCount = $row['recCount'];   
    }}
    return $recCount;
}
?>
