<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$stkm_id = $_GET["stkm_id"];

// $sql = "SELECT *
//         FROM
//             smartdb.sm20_quarantine AS qrtn,
//             smartdb.sm18_impairment AS im1,
//             smartdb.sm18_impairment AS im2
//         WHERE   qrtn.auto_storageID_one = im1.auto_storageID
//         AND     qrtn.auto_storageID_two = im2.auto_storageID
//         AND     stkm_id = $stkm_id";




$cherry=0;
$sql = "SELECT* FROM smartdb.sm13_stk WHERE  stkm_id = $stkm_id";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
          $stk_id             = $row["stk_id"];
          $stk_name           = $row["stk_name"];
          $dpn_extract_date   = $row["dpn_extract_date"];
          $stk_type           = $row["stk_type"];
}}

$sql = "SELECT COUNT(*) AS incompleteCount FROM smartdb.sm20_quarantine WHERE stkm_id_new = $stkm_id AND complete_date IS NULL";
$result = $con->query($sql);
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {     
    $incompleteCount      = $row['incompleteCount'];  
}}

$btnFinish = "<button href='#' class='btn btn-outline-dark float-right' disabled>Finalise</button>";
if ($incompleteCount==0){
    $btnFinish = "<a href='05_action.php?act=save_merge_finalise&stkm_id=$stkm_id' class='btn btn-outline-dark float-right'>Finalise</a>";
}


$arrRCs = array();
$sql = "SELECT * FROM smartdb.sm15_rc;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {     
    $res_reason_code    = $row['res_reason_code'];  
    $rc_desc            = $row['rc_desc']; 
    $arrRCs[$res_reason_code] = $rc_desc;
}}

$arrFindings = array();
$sql = "SELECT * FROM smartdb.sm19_result_cats;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {     
    $findingID      = $row['findingID'];  
    $findingName    = $row['findingName']; 
    $arrFindings[$findingID] = $findingName;
}}

$rowz= "";
$sql = "SELECT * FROM smartdb.sm20_quarantine WHERE stkm_id_new = $stkm_id";
$result2 = $con->query($sql);
if ($result2->num_rows > 0) {
while($row2 = $result2->fetch_assoc()) {  
    $q_id                   = $row2['q_id'];
    $stkm_id_one            = $row2['stkm_id_one'];  
    $stkm_id_two            = $row2['stkm_id_two'];
    $isType                 = $row2['isType'];
    $pkID1                  = $row2['pkID1'];
    $pkID2                  = $row2['pkID2'];
    $BIN_CODE               = $row2['BIN_CODE'];
    $res_pkID_selected      = $row2['res_pkID_selected'];
    $res_stkm_id_selected   = $row2['res_stkm_id_selected'];
    $complete_date          = $row2['complete_date'];

    // echo "<br>stkm_id_one:$stkm_id_one<br>stkm_id_two:$stkm_id_two<br>pkID1:$pkID1<br>pkID2:$pkID2<br>res_pkID_selected:$res_pkID_selected<br>res_stkm_id_selected:$res_stkm_id_selected<br>";


    if($stk_type=="stocktake"){
        $rowz .= fnMakeRow_stk($q_id,$pkID1,$pkID2,$res_pkID_selected,$complete_date);
    }elseif($stk_type=="impairment"){
        if($isType=="imp"){
            $rowz .= fnMakeRow_imp($q_id,$pkID1,$pkID2,$res_pkID_selected,$complete_date);
        }elseif($isType=="b2r"){
            $rowz .= fnMakeRow_b2r($q_id,$stkm_id_one,$stkm_id_two,$BIN_CODE,$res_stkm_id_selected,$complete_date);
        }
    }

}}




function fnMakeRow_stk($q_id,$pkID1,$pkID2,$res_pkID_selected,$complete_date){
    global $con, $stkm_id, $arrRCs;
    $btnOneColor = " btn-outline-dark ";
    $btnTwoColor = " btn-outline-dark ";
    if ($res_pkID_selected==$pkID1){
        $btnOneColor = " btn-dark ";
    }elseif ($res_pkID_selected==$pkID2){
        $btnTwoColor = " btn-dark ";
    }

    $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $pkID1";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $Asset              = $row['Asset'];  
        $Subnumber          = $row['Subnumber'];  
        $Class              = $row['Class'];  
        $AssetDesc1         = $row['AssetDesc1']; 
        $AssetDesc2         = $row['AssetDesc2']; 
        $assetType          = $row['assetType'];   

        $res_reason_codeA    = $row['res_reason_code'];  
        $res_comment1A       = $row['res_comment'];  
        $res_create_dateA    = $row['res_create_date'];
    }}

    $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $pkID2";
    // echo "<br><br><br>$sql";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $res_reason_codeB    = $row['res_reason_code'];  
        $res_comment1B       = $row['res_comment'];  
        $res_create_dateB    = $row['res_create_date'];
    }}
    $btnTakeOne = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=one' class='btn $btnOneColor'>Use this</a>";
    $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=two' class='btn $btnTwoColor'>Use this</a>";
        
    $fOne = fnGetReasonCodeName($res_reason_codeA);
    $fTwo = fnGetReasonCodeName($res_reason_codeB);

    $btnLink1 = "<a href='11_ass.php?ass_id=".$pkID1."' class='btn btn-outline-dark'>Link</a>";
    $btnLink2 = "<a href='11_ass.php?ass_id=".$pkID2."' class='btn btn-outline-dark'>Link</a>";

    $row  = "<tr>";
    $row .= "<td>$Asset - $Subnumber</td>";
    $row .= "<td>$Class</td>";
    $row .= "<td>$AssetDesc1</td>";
    $row .= "<td>$AssetDesc2</td>";
    $row .= "<td>$assetType</td>";
    $row .= "<td>$res_create_dateA<br>$res_reason_codeA: $fOne<br>$res_comment1A<br>$btnTakeOne<br>$btnLink1</td>";
    $row .= "<td>$res_create_dateB<br>$res_reason_codeB: $fTwo<br>$res_comment1B<br>$btnTakeTwo<br>$btnLink2</td>";
    $row .= "</tr>";
    return $row;
}













function fnMakeRow_b2r($q_id,$stkm_id_one,$stkm_id_two,$BIN_CODE,$res_stkm_id_selected,$complete_date){
    global $con, $stkm_id, $arrFindings;

    $btnOneColor = " btn-outline-dark ";
    $btnTwoColor = " btn-outline-dark ";
    if ($res_stkm_id_selected==$stkm_id_one){
        $btnOneColor = " btn-dark ";
    }elseif ($res_stkm_id_selected==$stkm_id_two) {
        $btnTwoColor = " btn-dark ";
    }

    $sql = "SELECT DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE, COUNT(*) AS scCount1
            FROM smartdb.sm18_impairment 
            WHERE BIN_CODE = '$BIN_CODE' 
            AND stkm_id=$stkm_id_one 
            GROUP BY DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, BIN_CODE";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];  
        $WHOUSE_ID          = $row['WHOUSE_ID'];  
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];  
        $BIN_CODE           = $row['BIN_CODE'];  
        $scCount1           = $row['scCount1'];   
    }}

    $sql = "SELECT BIN_CODE, COUNT(*) AS scCount2
            FROM smartdb.sm18_impairment 
            WHERE BIN_CODE = '$BIN_CODE' 
            AND stkm_id=$stkm_id_two 
            GROUP BY BIN_CODE";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $BIN_CODE           = $row['BIN_CODE'];   
        $scCount2           = $row['scCount2']; 
    }}

    $btnTakeOne = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=one' class='btn $btnOneColor'>Use this</a>";
    $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=two' class='btn $btnTwoColor'>Use this</a>";
    $linkOne = "<a href='17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id_one'>View record</a>";
    $linkTwo = "<a href='17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id_two'>View record</a>";
    $row  = "<tr>";
    $row .= "<td>B2R</td>";
    $row .= "<td>$DSTRCT_CODE<br>$WHOUSE_ID<br>$SUPPLY_CUST_ID</td>";
    $row .= "<td>$BIN_CODE</td>";
    $row .= "<td></td>";
    $row .= "<td>Stockcode count:$scCount1<br>$btnTakeOne<br>$linkOne</td>";
    $row .= "<td>Stockcode count:$scCount2<br>$btnTakeTwo<br>$linkOne</td>";
    $row .= "</tr>";

    return $row;
}





function fnMakeRow_imp($q_id,$pkID1,$pkID2,$res_pkID_selected,$complete_date){
    global $con, $stkm_id, $arrFindings;

    $btnOneColor = " btn-outline-dark ";
    $btnTwoColor = " btn-outline-dark ";
    if ($res_pkID_selected==$pkID1){
        $btnOneColor = " btn-dark ";
    }elseif ($res_pkID_selected==$pkID2) {
        $btnTwoColor = " btn-dark ";
    }

    $sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $pkID1";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];  
        $WHOUSE_ID          = $row['WHOUSE_ID'];  
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];  
        $STOCK_CODE         = $row['STOCK_CODE'];  
        $ITEM_NAME          = $row['ITEM_NAME'];  
        $STK_DESC           = $row['STK_DESC'];  
        $BIN_CODE           = $row['BIN_CODE'];  
        $TRACKING_REFERENCE = $row['TRACKING_REFERENCE'];  

        $res_create_date1   = $row['res_create_date'];  
        $findingID1         = $row['findingID'];  
        $res_comment1       = $row['res_comment'];  
    }}

    $sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $pkID2";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $res_create_date2   = $row['res_create_date'];  
        $findingID2         = $row['findingID'];  
        $res_comment2       = $row['res_comment']; 
    }}

    $btnTakeOne = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=one' class='btn $btnOneColor'>Use this</a>";
    $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&q_id=$q_id&stmnum=two' class='btn $btnTwoColor'>Use this</a>";
    $fOne = $arrFindings[$findingID1];
    $fTwo = $arrFindings[$findingID2];
    $linkOne = "<a href='16_imp.php?auto_storageID=$pkID1'>View record</a>";
    $linkTwo = "<a href='16_imp.php?auto_storageID=$pkID2'>View record</a>";
    $row  = "<tr>";
    $row .= "<td>IMP</td>";
    $row .= "<td>$DSTRCT_CODE<br>$WHOUSE_ID<br>$SUPPLY_CUST_ID</td>";
    $row .= "<td>$BIN_CODE</td>";
    $row .= "<td>$STOCK_CODE<br>$ITEM_NAME<br>$STK_DESC</td>";
    $row .= "<td>$res_create_date1<br>$findingID1: $fOne<br>$res_comment1<br>$btnTakeOne<br>$linkOne</td>";
    $row .= "<td>$res_create_date2<br>$findingID2: $fTwo<br>$res_comment2<br>$btnTakeTwo<br>$linkTwo</td>";
    $row .= "</tr>";

    return $row;
}












// }elseif ($stk_type=="stocktake"){

//     $arrRCs = array();
//     $sql = "SELECT * FROM smartdb.sm15_rc;";
//     $result = $con->query($sql);
//     if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {     
//         $res_reason_code    = $row['res_reason_code'];  
//         $rc_desc            = $row['rc_desc']; 
//         $arrRCs[$res_reason_code] = $rc_desc;
//     }}
//     // echo "<br><br><br>";
//     // print_r($arrRCs);
//     $rws ="";
//     $sql = "SELECT * FROM smartdb.sm20_quarantine WHERE stkm_id = $stkm_id";
//     $result2 = $con->query($sql);
//     if ($result2->num_rows > 0) {
//     while($row2 = $result2->fetch_assoc()) {  
//         $q_id                       = $row2['q_id'];
//         $auto_storageID_one         = $row2['auto_storageID_one'];  
//         $auto_storageID_two         = $row2['auto_storageID_two'];
//         $complete_date              = $row2['complete_date'];
//         $selected_auto_storageID    = $row2['selected_auto_storageID'];

//         $btnOneColor = " btn-outline-dark ";
//         if ($auto_storageID_one==$selected_auto_storageID){
//             $btnOneColor = " btn-dark ";
//         }

//         $btnTwoColor = " btn-outline-dark ";
//         if ($auto_storageID_two==$selected_auto_storageID){
//             $btnTwoColor = " btn-dark ";
//         }

//         $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $auto_storageID_one";
//         $result = $con->query($sql);
//         if ($result->num_rows > 0) {
//         while($row = $result->fetch_assoc()) {    
//             // ass_id, create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, impairment_code, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, classDesc, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_classDesc, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo, res_isq_5, res_isq_6, res_isq_7, res_isq_8, res_isq_9, res_isq_10, res_isq_13, res_isq_14, res_isq_15
//             $Asset              = $row['Asset'];  
//             $Subnumber          = $row['Subnumber'];  
//             $Class              = $row['Class'];  
//             $AssetDesc1         = $row['AssetDesc1']; 
//             $AssetDesc2         = $row['AssetDesc2']; 
//             $assetType          = $row['assetType'];   

//             $res_reason_codeA    = $row['res_reason_code'];  
//             $res_comment1A       = $row['res_comment'];  
//             $res_create_dateA    = $row['res_create_date'];
//         }}

//         $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = $auto_storageID_two";
//         // echo "<br><br><br>$sql";
//         $result = $con->query($sql);
//         if ($result->num_rows > 0) {
//         while($row = $result->fetch_assoc()) {    
//             $res_reason_codeB    = $row['res_reason_code'];  
//             $res_comment1B       = $row['res_comment'];  
//             $res_create_dateB    = $row['res_create_date'];
//         }}

//         $btnTakeOne = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_one' class='btn $btnOneColor'>Use this</a>";
//         $btnTakeTwo = "<a href='05_action.php?act=save_merge_select&stkm_id=$stkm_id&q_id=$q_id&selected_auto_storageID=$auto_storageID_two' class='btn $btnTwoColor'>Use this</a>";
        

//         // echo "<br>A: $auto_storageID_one<br>A: $res_reason_codeA";
//         // echo "<br>B: $auto_storageID_two<br>B: $res_reason_codeB";

//         // $keyExists = array_key_exists($res_reason_codeA, $arrRCs);
//         // echo "<br>keyExists: $keyExists";
//         // $fOne = $arrRCs[$res_reason_codeA];
//         // $fTwo = $arrRCs[$res_reason_codeB];
//         $fOne = fnGetReasonCodeName($res_reason_codeA);
//         $fTwo = fnGetReasonCodeName($res_reason_codeB);


//         $btnLink1 = "<a href='11_ass.php?ass_id=".$auto_storageID_one."' class='btn btn-outline-dark'>Link</a>";
//         $btnLink2 = "<a href='11_ass.php?ass_id=".$auto_storageID_two."' class='btn btn-outline-dark'>Link</a>";

//         $rws .= "<tr>";
//         $rws .= "<td>$Asset - $Subnumber</td>";
//         $rws .= "<td>$Class</td>";
//         $rws .= "<td>$AssetDesc1</td>";
//         $rws .= "<td>$AssetDesc2</td>";
//         $rws .= "<td>$assetType</td>";
//         $rws .= "<td>$res_create_dateA<br>$res_reason_codeA: $fOne<br>$res_comment1A<br>$btnTakeOne<br>$btnLink1</td>";
//         $rws .= "<td>$res_create_dateB<br>$res_reason_codeB: $fTwo<br>$res_comment1B<br>$btnTakeTwo<br>$btnLink2</td>";
//         $rws .= "</tr>";



//     }}
// }

function fnGetReasonCodeName($res_reason_code){
    global $arrRCs;
    $keyExists = array_key_exists($res_reason_code, $arrRCs);
    if($keyExists){
        return $arrRCs[$res_reason_code];
    }else{
        return "";
    }
}

?>


<br><br><br>
<div class='row'>
    <div class='col'>
    <div class='display-4'>
        Merge deconfliction
        <?=$btnFinish?>
    </div>
        <table class='table' id='mainTable'>
            <!-- <tr>
                <th>District<br>Warehouse<br>SCA</th>
                <th>Stock code</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Bin code</th>
                <th>TrackingRef</th>
                <th>Result 1<br>Create Date<br>FindingID<br>Comment</th>
                <th>Result 2<br>Create Date<br>FindingID<br>Comment</th>
            </tr> -->
            <tr>
                <th>Type</th>
                <th>Location</th>
                <th>Bin</th>
                <th>Stock desc</th>
                <th>Result 1</th>
                <th>Result 2</th>
            </tr>
            <?=$rowz?>
        </table>
    </div>
</div>


<?php include "04_footer.php"; ?>