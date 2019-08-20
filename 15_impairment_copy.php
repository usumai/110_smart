<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php

$tableRows = "";
$sqlInclude = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include=1 AND smm_delete_date IS NULL";
$sql = "SELECT * FROM smartdb.sm18_impairment  WHERE stkm_id IN ($sqlInclude )";
// echo "<br><br><br>".$sql;
$result = $con->query($sql);
$rowMaker = "";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

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

//         if ($rowStatus==1) {
//             $rowStatus = "<span class='bg-success btn btn-sm'>Complete</span>";
//         }else{
//             $rowStatus = "<span class='bg-danger btn btn-sm'>Incomplete</span>"; 
//         }



//         // $rowStatus = "";


//         if (strlen($DSTRCT_CODE)==0&&strlen($WHOUSE_ID)==0) {
//             $rowType = "SCA";
//             $rowIdent =  $SUPPLY_CUST_ID."(".$SC_ACCOUNT_TYPE.")";
//         }else{
//             $rowType = "WHS";
//             $rowIdent =   $DSTRCT_CODE.$WHOUSE_ID;
//         }

//         if ($sampleFlag==1) {
//             $sampleFlagName = "Primary";
//             $rowColor="";
//         }elseif ($sampleFlag==2) {
//             $sampleFlagName = "Backup";
//             $rowColor=" style='background-color:#777;' ";
//             $rowStatus = "<span class='bg-success btn btn-sm'>Backup</span>"; 

//         }

//         $rowBtn = "<a href='row.php?storageID=".$storageID."&targetID=".$targetID."&isID=".$isID."' class='btn btn-xs btn-dark'>".$rowNo."</a>";        // Adam Added isID for Breadcrumb
//         $completeness = "";

        $btnAction = "<a href='' class='btn btn-outline-dark'>Action</a>";
        $tableRows  .= "<tr><td>".$btnAction."</td><td>".$DSTRCT_CODE."</td><td>".$WHOUSE_ID."</td><td>".$SUPPLY_CUST_ID."</td><td>".$BIN_CODE."</td><td>".$STOCK_CODE."</td><td>".$ITEM_NAME."</td><td>".substr($INVENT_CAT,0,2)."</td><td>".$SOH."</td><td>".$TRACKING_IND."</td><td>".$TRACKING_REFERENCE."</td><td class='text-right'>".$btnAction."</td></tr>";
}}
?>
<script type="text/javascript" class="init">
$(document).ready(function() {

    $('#activityTable').DataTable({
        stateSave: true
    });

} );
</script>

</div><div class='container-fluid'>
<br><br>
  <div class="row">
    <div class="col-lg">
      <table id="activityTable" class="table table-sm" cellspacing="0" width="100%">
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
                <th class='text-right'>Action</th>
            </tr>
        </thead>
        <tbody>
            <?=$tableRows?>
        </tbody>
    </table>
    </div>
  </div>










<?php include "04_footer.php"; ?>

