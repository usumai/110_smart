<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$BIN_CODE = $_GET["BIN_CODE"];
$binC='';
$arrSample = array();
$sql = "SELECT * FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE'  AND isChild IS NULL";
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
        $res_update_user    = $row['res_update_user'];
        $findingID          = $row['findingID'];
        $res_comment        = $row['res_comment'];
        $res_unserv_date    = $row['res_unserv_date'];

        $binC .= "<tr><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td align='right'>$SOH</td><td>Original</td></tr>";

        $arrSample[] = $row;
}}

$binExtra = '';
$sql = "SELECT auto_storageID, STOCK_CODE, ITEM_NAME, SOH, finalResult FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE' AND isChild=1";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $extraSTOCK_CODE         = $row['STOCK_CODE'];
        $extraITEM_NAME          = $row['ITEM_NAME'];
        $extraSOH                = $row['SOH'];
        $finalResult            = $row['finalResult'];

        if(empty($finalResult)){
            $extraStatus = "<a href='18_f2r_extra.php?auto_storageID=$auto_storageID&BIN_CODE=$BIN_CODE' class='list-group-item list-group-item-danger btnInvestigate' style='padding:5px;text-decoration:none'>Investigate</a>";
        }else{
            $finalResultDisp = $finalResult;
            if($finalResult=='nstr'){
                $finalResultDisp = "No finding";
            }
            $extraStatus = "<a href='18_f2r_extra.php?auto_storageID=$auto_storageID&BIN_CODE=$BIN_CODE' class='list-group-item list-group-item-success btnInvestigate' style='padding:5px;text-decoration:none'>$finalResultDisp</a>";
        }
        $binExtra .= "<tr><td>$extraSTOCK_CODE</td><td>$extraITEM_NAME</td><td align='right'>$extraSOH</td><td>$extraStatus</td></tr>";

        $arrSample['extras'][] = $row;
}}


$arrSample = json_encode($arrSample);

$btnDelete=$btnAdd='';
if(!empty($findingID)){
    $btnDelete = "<div class='text-center'><div class='dropdown'><button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button><div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'><a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_clear_f2r&BIN_CODE=".$BIN_CODE."'>I'm sure</a></div></div></div>";

    if($findingID>100){
        $btnAdd = "<br><br><br><button type='button' class='btn btn-outline-dark' data-toggle='modal' data-target='#modal_add_extra' v-if='ar.first_found_flag==1'>Register extra stockcode</button>";
    }


}
?>






<script type="text/javascript">
let arS = '<?=$arrSample?>'
    arS = JSON.parse(arS);
    
//Declare other global variables
let hideInitialMenu, findingName;

$(document).ready(function() {
    
    //Copy the menu to the other side of the page
    let menuright = $('#menuleft').html();
    $('#menuright').html(menuright);

//     //Initialise the page
    setPage()

    function setPage(){
        findingName     = "&nbsp;";
        hideInitialMenu        = false;
        $("#resultSelection").removeClass('list-group-item-success');
        $("#resultSelection").removeClass('list-group-item-warning');
        $("#resultSelection").removeClass('list-group-item-danger');
        if (arS[0]['findingID']==100){
            hideInitialMenu        = true;
            findingName     = "No additional stockcodes were found";
            $("#resultSelection").addClass('list-group-item-success');
        }else if(arS[0]['findingID']==101){
            hideInitialMenu        = true;
            findingName     = "You've found some additional stockcodes but havn't investigated them";
            $("#resultSelection").addClass('list-group-item-danger');
        }else if(arS[0]['findingID']=102){
            hideInitialMenu        = true;
            findingName     = "You've found some additional stockcodes and have investigated them all";
            $("#resultSelection").addClass('list-group-item-warning');
        }
        $('.hideInitialMenu').toggle(!hideInitialMenu);
        $("#resultSelection").html("<b>"+findingName+"</b>");
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
        <ul class="list-group list-group-flush text-center">
            <?=$btnDelete?>
            <?=$btnAdd?>

            <li class="list-group-item hideInitialMenu q1"><b>Are there any stockcodes in addition to this list?</b></li>
            <a class="list-group-item list-group-item-action list-group-item-success hideInitialMenu q1" href='05_action.php?act=save_f2r_nstr&BIN_CODE=<?=$BIN_CODE?>'>No</a>

            <a class="list-group-item list-group-item-action list-group-item-danger hideInitialMenu q1" href='05_action.php?act=save_f2r_extras&BIN_CODE=<?=$BIN_CODE?>'>Yes</a>

        </ul>
    </div>

    <div class='col-6 lead'>

    <form action='05_action.php' method='POST'>
        <table class='table table-sm'>
            <tr><td colspan='4' id='resultSelection'>&nbsp;</td></tr>
            <tr><td><b>District</b></td><td colspan='2' ><?=$DSTRCT_CODE?></td><td></td></tr>
            <tr><td><b>Warehouse</b></td><td colspan='2' ><?=$WHOUSE_ID?></td><td></td></tr>
            <tr><td><b>SCA</b></td><td colspan='2' ><?=$SUPPLY_CUST_ID?></td><td></td></tr>
            <tr><td><b>Bin</b></td><td colspan='2' ><?=$BIN_CODE?></td><td></td></tr>
            <tr><td colspan='4'><b>Bin contents</b></td></tr>
            <tr><td><b>Stockcode</b></td><td><b>Name</b></td><td align='right'><b>SOH</b></td><td><b>Status</b></td></tr>
            <?=$binC?>

            <tr><td colspan='4' class='text-center'><b><br>Extras</b></td></tr
            <?=$binExtra?>

            <tr><td colspan='3' class='hideInitialMenu'><b>Comments</b><textarea class='form-control' rows='5' name='res_comment' id='res_comment'><?=$res_comment?></textarea></td></tr>
        </table>
        </form>
    </div>

    
    <div class='col-3 lead' id='menuright'></div>

</div>










<form action='05_action.php' method='post' id='formAddExtra'>
    <div class="modal fade" id="modal_add_extra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Register extra stockcode</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">  
            <p class="lead">
                So you've found a stockcode in a bin, which is additional to the list you expected. Lets get some details:
                <br>
                <br>
                NSN/Stockcode
                <input type="text" name="extraStockcode" class="form-control">
                Stockcode description
                <input type="text" name="extraName" class="form-control">
                SOH
                <input type="text" name="extraSOH" class="form-control">

                <input type="hidden" name="BIN_CODE" value="<?=$BIN_CODE?>">
                <input type="hidden" name="act" value="save_f2r_add_extra">
            </p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
            <input type="submit" class="btn btn-primary" value='Add'>
            </div>
        </div>
        </div>
    </div>
</form>


<?php include "04_footer.php"; ?>