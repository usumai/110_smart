<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$auto_storageID = $_GET["auto_storageID"];

$sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $auto_storageID";
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


}}

?>

<br><br><br>
<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Bin impairment</h1>
    </div>
</div>


<div class='row'>
    <div class='col-4'>
        <table class='table'>
            <tr><td></td><td></td></tr>
            <tr><td>DSTRCT_CODE</td><td><?=$DSTRCT_CODE?></td></tr>
        </table>
    </div>

    <div class='col-4'>
        <div class="form-group"><label>DSTRCT_CODE</label><input type="text" v-model="ar.best_AssetDesc1" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data('AssetDesc1')"></div>
        <div class="form-group"><label>WHOUSE_ID</label><input type="text" v-model="ar.best_AssetDesc2" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data('AssetDesc2')"></div>
        <div class="form-group"><label>Asset Main No Text</label><input type="text" v-model="ar.best_AssetMainNoText" class="form-control" :disabled="ar.lock_all" v-on:keyup="sync_data('AssetMainNoText')"></div>
        <div class="form-group"><label>Inventory</label><input type="text" v-model="ar.best_Inventory" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data('Inventory')"></div>
        <div class="form-group"><label>InventNo</label><input type="text" v-model="ar.best_InventNo" class="form-control" :disabled="ar.lock_limited" v-on:keyup="sync_data('InventNo')"></div>
    </div>

</div>


<?php include "04_footer.php"; ?>