<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php


function fnClNum($fv){
    $fv = (empty($fv) ? 0 : $fv);
    $fv = (is_nan($fv) ? 0 : $fv);
    return $fv;
}

function fnPerc($tot,$sub){
    $tot = fnClNum($tot);
    $sub = fnClNum($sub);
    if($tot>0){
        $perc = $sub/$tot;
        $perc = round(($perc*100),2);
    }else{
        $perc = 0;
    }
    return $perc;
}


$sql = "SELECT SUM(rc_orig) AS rc_orig, SUM(rc_orig_complete) AS rc_orig_complete FROM smartdb.sm13_stk  
WHERE stk_include=1 AND smm_delete_date IS NULL";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rc_orig            = $row["rc_orig"];
        $rc_orig_complete   = $row["rc_orig_complete"];
}}

$perc_complete  = fnPerc($rc_orig,$rc_orig_complete);


$fltrBtns='';
$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT DSTRCT_CODE, WHOUSE_ID FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND isBackup IS NULL AND isType='imp' AND delete_date IS NULL GROUP BY DSTRCT_CODE, WHOUSE_ID  ";
// $sql .= " LIMIT 500; ";   
// echo $sql;
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $fltrBtns.="<button class='btn btn-info btn_search_term' data-search_term='$DSTRCT_CODE~$WHOUSE_ID'>$DSTRCT_CODE~$WHOUSE_ID</button>&nbsp;";
    }}








?>



<!-- <script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
});
</script> -->

<script type="text/javascript">
$(document).ready(function() {
    $('#table_assets').DataTable({
        stateSave: true
    });
    var table = $('#table_assets').DataTable();
    $('#table_assets').on('search.dt', function() {
        rr_search();
    }); 
    $(".btn_search_term").click(function(){
        var search_term_new = $(this).data("search_term");
        var search_term_current = $('.dataTables_filter input').val();
        table.search(search_term_current+" "+search_term_new).draw();
    });
    $(".btn_search_term_clear").click(function(){
        table.search(" ").draw();
    });
    rr_search();
    function rr_search() {
        var search_term = $('.dataTables_filter input').val();
        if (search_term.length>4) {
            $("#table_rr").html("");
            $.post("05_action.php",
            {
                act: "get_rawremainder_asset_count",
                search_term: search_term
            },
            function(data, status){
                $("#area_rr_count").html(data)
            });
        }else{
            $("#area_rr_count").html("Enter a search term greater than four characters to search the Raw Remainder dataset.")
        }
    }
});
</script>



















<div class="container-fluid">
    <br><br>
    <div class="row">
        <div class="col">
            <h2><?=$rc_orig_complete?>/<?=$rc_orig?> total (<?=$perc_complete?>%)&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="FIN~">Completed</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="NYC~">Incomplete</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="~TBA">Pending</button>&nbsp;
            <button class="btn btn-warning btn_search_term_clear">Clear search terms</button>
            <?=$fltrBtns?></h2>
        </div>
    </div>

    <table id="table_assets" class="table table-sm" width="100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>DIST~WHSE</th>
                <th>SCA</th>
                <th>BIN_CODE</th>
                <th>Stockcde</th>
                <th>Name</th>
                <th>Cat</th>
                <th>SOH</th>
                <th>TrkInd</th>
                <th>TrkRef</th>
                <th>Type</th>
                <th>Status</th>
                <th class='text-right'>Action</th>
            </tr>
        </thead>
        <tbody>





<?php

$assetType = "";

$rw_ass = "";






$arF = array();
$sql = "SELECT findingID, color AS fCol, resAbbr AS fAbr FROM smartdb.sm19_result_cats;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $findingID  = $row['findingID'];    
        $fCol       = $row['fCol'];   
        $fAbr       = $row['fAbr'];
        $arF['col'][$findingID] = $fCol;
        $arF['abr'][$findingID] = $fAbr;
}}



$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND sampleFlag = 1 AND isBackup IS NULL AND isType='imp' AND delete_date IS NULL ";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $stkm_id            = $row['stkm_id'];  
        $storageID          = $row['storageID'];
        $rowNo              = $row['rowNo'];
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $SC_ACCOUNT_TYPE    = $row['SC_ACCOUNT_TYPE'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $BIN_CODE           = $row['BIN_CODE'];
        $INVENT_CAT         = $row['INVENT_CAT'];
        $TRACKING_IND       = $row['TRACKING_IND'];
        $SOH                = $row['SOH'];
        $TRACKING_REFERENCE = $row['TRACKING_REFERENCE'];
        $STK_DESC           = $row['STK_DESC'];
        $sampleFlag         = $row['sampleFlag'];
        $isType             = $row['isType'];
        $res_create_date    = $row['res_create_date'];
        $findingID          = $row['findingID'];

        $flag_status = "<h4><span class='badge badge-secondary'>NYC~</span></h4>";
        if(!empty($res_create_date)){
            $fCol = $arF['col'][$findingID];
            $fAbr = $arF['abr'][$findingID];
            $flag_status = "<h4><span class='badge badge-$fCol'>FIN~$fAbr</span></h4>";
            if ($findingID==13){
                $flag_status = "<h4><span class='badge badge-$fCol'>NYC~$fAbr</span></h4>";
            }
        }
        
        $flag_type = "<h4><span class='badge badge-dark'>IMP</span></h4>";
        $btnAction = "<a href='16_imp.php?auto_storageID=$auto_storageID' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";



        echo "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."~".$WHOUSE_ID."</td><td>".$SUPPLY_CUST_ID."</td><td>".$BIN_CODE."</td><td>".$STOCK_CODE."</td><td>".$ITEM_NAME."</td><td>".substr($INVENT_CAT,0,2)."</td><td>".$SOH."</td><td>".$TRACKING_IND."</td><td>".$TRACKING_REFERENCE."</td><td>".$flag_type."</td><td>".$flag_status."</td><td class='text-right'>".$btnAction."</td></tr>";

}}



// B2R items which have a storageID of 1 are those which are teh skeleton rows, without them, if a bin location is listed as a target, but does not have stock, the bin will not be sent from the DPN.
$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT  stkm_id, DSTRCT_CODE, WHOUSE_ID, BIN_CODE, findingID, COUNT(DISTINCT STOCK_CODE) AS countSCs  FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude ) AND isBackup IS NULL AND isType='b2r' AND delete_date IS NULL AND storageID=1 GROUP BY stkm_id, DSTRCT_CODE, WHOUSE_ID, BIN_CODE, findingID ";
// echo $sql;
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {        
        $stkm_id            = $row['stkm_id'];  
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        // $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $BIN_CODE           = $row['BIN_CODE'];
        $findingID          = $row['findingID'];
        $countSCs           = $row['countSCs'];

        $flag_status = "<h4><span class='badge badge-secondary'>NYC~</span></h4>";
        if(!empty($findingID)){
            $fCol = $arF['col'][$findingID];
            $fAbr = $arF['abr'][$findingID];
            $flag_status = "<h4><span class='badge badge-$fCol'>FIN~$fAbr</span></h4>";
            if ($findingID==13){
                $flag_status = "<h4><span class='badge badge-$fCol'>NYC~$fAbr</span></h4>";
            }
        }
        $flag_type = "<h4><span class='badge badge-dark'>B2R</span></h4>";
        $btnAction = "<a href='17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";



        echo "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."~".$WHOUSE_ID."</td><td></td><td>".$BIN_CODE."</td><td></td><td></td><td></td><td>".$countSCs."</td><td></td><td></td><td>".$flag_type."</td><td>".$flag_status."</td><td class='text-right'>".$btnAction."</td></tr>";

}}

?>




        </tbody>
    </table>
    
</div>

<?php include "04_footer.php"; ?>