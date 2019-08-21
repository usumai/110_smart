<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

// apply ajax calls to update db
// create split cats
// apply date picker
// status maker
// arming clear switch
// Clear breaks




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

        $complete = 'false';
        if(!empty($res_create_date)){
            $complete = 'true';
        }


}}

// $complete = 'true';
?>


<script type="text/javascript">
let TRACKING_IND = "<?=$TRACKING_IND?>";
let complete = <?=$complete?>;

let dispQtrack  = TRACKING_IND=="Q";
let dispStrack  = TRACKING_IND=="S";
dispQtrack      = complete ? false : dispQtrack;
dispStrack      = complete ? false : dispStrack;

console.log("TRACKING_IND:"+TRACKING_IND);
console.log("dispQtrack:"+dispQtrack);
console.log("dispStrack:"+dispStrack);
console.log("complete:"+complete);


$(document).ready(function() {
    
    let menuright = $('#menuleft').html();
    $('#menuright').html(menuright);

    setPage()


    $('.dispStrack').click(function(){
        complete = true;
        let resultSelection = $(this).val();
        $("#resultSelection").val(resultSelection);
        setPage()
    });

    $('.dispQtrack').click(function(){
        complete = true;
        let resultSelection = $(this).val();
        $("#resultSelection").val(resultSelection);
        setPage()
    });



    $('body').on('click', '#btnClear', function() {
        complete = false;
        setPage()
    });



    function setPage(){
        dispQtrack = complete ? false : TRACKING_IND=="Q"
        dispStrack = complete ? false : TRACKING_IND=="S"
        $('.dispQtrack').toggle(dispQtrack);
        $('.dispStrack').toggle(dispStrack);
        $('.complete').toggle(complete);
    }

});
</script>


<style>
.list-group-item{
    margin-bottom:10px;
}
</style>



<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Bin impairment</h1>
    </div>
</div>



<div class='row'>

    <div class='col-3 lead' id='menuleft'>
        
        <div class='text-center'><button class='btn btn-danger complete float-center' id='btnClear'>Clear</button></div>
        <ul class="list-group list-group-flush text-center">
            <li class="list-group-item dispStrack"><b>Item sighted</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='1'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='2'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='3'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispQtrack"><b>Sighted&nbsp;or&nbsp;found&nbsp;evidence&nbsp;of&nbsp;all&nbsp;items</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='4'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='5'>None&nbsp;serviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='6'>None&nbsp;serviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispStrack"><b>Item not sighted, evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='7'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='8'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='9'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispQtrack"><b>Split&nbsp;category</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispQtrack" value='10'>One, some or all of the following:
                <br>-Not all items were found 
                <br>-Items were in different categories 
                <br>-Found more than original quantity
            </button>

            <li class="list-group-item dispQtrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispQtrack" value='11'>Not sighted - No evidence</button>

            <li class="list-group-item dispStrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispStrack" value='11'>Not sighted - No evidence</button>
        </ul>
    </div>

    <div class='col-6 lead'>
        <input type='text' class='form-control' id='resultSelection'>
        <table class='table table-sm'>
            <tr><td><b>District</b></td><td><?=$DSTRCT_CODE?></td></tr>
            <tr><td><b>Warehouse</b></td><td><?=$WHOUSE_ID?></td></tr>
            <tr><td><b>SCA</b></td><td><?=$SUPPLY_CUST_ID?></td></tr>
            <tr><td><b>Bin</b></td><td><?=$BIN_CODE?></td></tr>
            <tr><td><b>SOH</b></td><td><?=$SOH?></td></tr>
            <tr><td nowrap><b>SC Account type</b></td><td><?=$SC_ACCOUNT_TYPE?></td></tr>
            <tr><td nowrap><b>Tracking indicator</b></td><td><?=$TRACKING_IND?></td></tr>
            <tr><td colspan='2'><b>Comments</b><textarea class='form-control' rows='5'></textarea></td></tr>
            <tr><td><b>Date</b></td><td><input type='text' class='form-control' name='' readonly></td></tr>
            <tr><td colspan='2'>###############<br>Split area!<br>###############</td></tr>
        </table>
    </div>

    
    <div class='col-3 lead' id='menuright'></div>

</div>


<?php include "04_footer.php"; ?>