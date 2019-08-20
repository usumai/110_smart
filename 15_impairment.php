<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php


$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT COUNT(*) as count_total, SUM(CASE WHEN res_create_date IS NOT NULL AND delete_date IS NULL THEN 1 ELSE 0 END) AS count_complete FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude )";

$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $count_total    = $row["count_total"];
        $count_complete = $row["count_complete"];
}}
$perc_complete = 0;
if ($count_total>0&&$count_complete>0) {
    $perc_complete = round(($count_complete/$count_total)*100,0); 
}

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
            <h2><?=$count_complete?>/<?=$count_total?> total (<?=$perc_complete?>%)&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="IS~">Add Impairment Samples filter</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="FIN~">Add completed filter</button>&nbsp;
            <button class="btn btn-primary btn_search_term" data-search_term="NYC~">Add incomplete filter</button>&nbsp;
            <button class="btn btn-warning btn_search_term_clear">Clear search terms</button></h2>
        </div>
    </div>

    <span id="area_rr_count">Enter a search term greater than four characters to search the Raw Remainder dataset.</span>
    <table id="table_assets" class="table table-sm" width="100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>DIST</th>
                <th>WHSE</th>
                <th>SCA</th>
                <th>BIN_CODE</th>
                <th>Stockcde</th>
                <th>Name</th>
                <th>Cat</th>
                <th>SOH</th>
                <th>TrkInd</th>
                <th>TrkRef</th>
                <th>Status</th>
                <th class='text-right'>Action</th>
            </tr>
        </thead>
        <tbody>





<?php

$assetType = "";

$rw_ass = "";


$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude )";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
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
        $res_create_date    = $row['res_create_date'];

        $flag_status = "<span class='text-danger'>NYC~</span>";
        if(!empty($res_create_date)){
            $flag_status = "<span class='text-success'>FIN~<br>$res_findings</span>";
        }

        $btnAction = "<a href='16_bin.php?auto_storageID=$auto_storageID' class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>";
        echo "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."</td><td>".$WHOUSE_ID."</td><td>".$SUPPLY_CUST_ID."</td><td>".$BIN_CODE."</td><td>".$STOCK_CODE."</td><td>".$ITEM_NAME."</td><td>".substr($INVENT_CAT,0,2)."</td><td>".$SOH."</td><td>".$TRACKING_IND."</td><td>".$TRACKING_REFERENCE."</td><td>".$flag_status."</td><td class='text-right'>".$btnAction."</td></tr>";

}}



?>




        </tbody>
    </table>
    
</div>

<?php include "04_footer.php"; ?>