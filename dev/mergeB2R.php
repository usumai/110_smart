<?php
include "../01_dbcon.php"; 
include "../05_scripts.php";
$debugMode  = true;
$log = "";


// ENd result is:

// We want a table X that looks like:
// BIN_CODE
// stkm_id 

// Then we simply run an 
// insert into impairment table, from impairment table where stkmid=[STKMID] AND BIN_CODE = [BIN_CODE] AND isType='b2r'


// To build table X:
// We run the same comparison, but we ignore FF
// FF's might be present, but they will be wrapped in the BIN_CODE and transfered acordingly



$tableName          = "smartdb.sm18_impairment";
$stkm_id_one        = 1;
$stkm_id_two        = 2;
$pkName             = "BIN_CODE";
$storageIDName      = "BIN_CODE";
fnMerge($tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName);



function fnMerge($tableName, $stkm_id_one, $stkm_id_two, $pkName, $storageIDName){
    $qar    = [];
    $rows   = "";
    $sql1 = "SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment 
    WHERE isType='b2r' AND stkm_id=$stkm_id_one GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE";
    $sql2 = "SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment 
    WHERE isType='b2r' AND stkm_id=$stkm_id_two GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE";
    $sql1   = "($sql1) AS vt1";
    $sql2   = "($sql2) AS vt2";

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



    
    $table = "<table border=1><tr><td>Serial</td><td>Title</td><td width='20%'>Description</td><td>Query</td><td>Count</td><td>Result</td></tr>$rows</table>";
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
