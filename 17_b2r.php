<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$BIN_CODE = $_GET["BIN_CODE"];
$stkm_id = $_GET["stkm_id"];
$binC='';
$arrSample = array();
$sql = "SELECT storageID, STOCK_CODE, ITEM_NAME, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, res_comment, findingID, SUM(SOH) AS sumSOH, SUM(CASE WHEN checkFlag = 1 THEN 1 ELSE 0 END) AS checkFlag FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE'  AND isChild IS NULL AND isType ='b2r' AND stkm_id = $stkm_id 
GROUP BY STOCK_CODE, ITEM_NAME, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, res_comment, findingID
";
// $sql .= " LIMIT 500; ";   
// echo "<br><br><br>$sql";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $storageID          = $row['storageID'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $sumSOH             = $row['sumSOH'];
        $res_comment        = $row['res_comment'];
        $findingID          = $row['findingID'];
        $checkFlag          = $row['checkFlag'];

        if($checkFlag){
            $btn_status = "<a href='05_action.php?act=save_is_toggle_check&toggle=null&STOCK_CODE=$STOCK_CODE&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id' class='btn btn-outline-success'>Sighted</a>";
        }else{
            $btn_status = "<a href='05_action.php?act=save_is_toggle_check&toggle=1&STOCK_CODE=$STOCK_CODE&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id' class='btn btn-outline-dark'>Original</a>";
        }

        if($storageID){
            $binC .= "<tr><td colspan='5'>No stockcodes recorded</td></tr>";

        }else{
            $binC .= "<tr><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td></td><td></td><td align='right'>$btn_status</td></tr>";
        }
        // $binC .= "<tr><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td align='right'>$sumSOH</td><td>Original</td></tr>";

        $arrSample[] = $row;
}}

$binExtra = '';
$sql = "SELECT auto_storageID, STOCK_CODE, ITEM_NAME, SOH, finalResult FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE' AND isChild=1 AND stkm_id = $stkm_id AND delete_date IS NULL ";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $extraSTOCK_CODE    = $row['STOCK_CODE'];
        $extraITEM_NAME     = $row['ITEM_NAME'];
        $extraSOH           = $row['SOH'];
        $finalResult        = $row['finalResult'];
        // $res_comment        = $row['res_comment'];

        if(empty($finalResult)){
            $extraStatus = "<a href='18_b2r_extra.php?auto_storageID=$auto_storageID&BIN_CODE=$BIN_CODE&stkm_id=".$stkm_id."' class='list-group-item list-group-item-danger btnInvestigate' style='padding:5px;text-decoration:none'>Investigate</a>";
        }else{
            $finalResultDisp = $finalResult;
            if($finalResult=='nstr'){
                $finalResultDisp = "No finding";
            }
            $extraStatus = "<a href='18_b2r_extra.php?auto_storageID=$auto_storageID&BIN_CODE=$BIN_CODE&stkm_id=".$stkm_id."' class='list-group-item list-group-item-success btnInvestigate' style='padding:5px;text-decoration:none'>$finalResultDisp</a>";
        }
        // $btnEditExra = "<button type='button' class='btn btn-link btnEditExtra' data-toggle='modal' data-target='#modal_add_extra' data-asi='$auto_storageID' data-sc='$extraSTOCK_CODE' data-in='$extraITEM_NAME' data-soh='$extraSOH'>$extraITEM_NAME</button>";
        $btnEditExra = "<a href='18_b2r_edit.php?auto_storageID=$auto_storageID&BIN_CODE=$BIN_CODE&stkm_id=".$stkm_id."' class='btn btn-outline-dark'>Edit</a>";
        $binExtra .= "<tr><td>$extraSTOCK_CODE</td><td>$extraITEM_NAME</td><td>$extraSOH</td><td>$btnEditExra</td><td align='right'>$extraStatus</td></tr>";

        $arrSample['extras'][] = $row;
}}


$arrSample = json_encode($arrSample);

$btnDelete=$btnAdd='';
if(!empty($findingID)){
    $btnDelete = "<div class='text-center'><div class='dropdown'><button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button><div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'><a class='dropdown-item bg-danger text-light' href='05_action.php?act=save_clear_b2r&BIN_CODE=".$BIN_CODE."&stkm_id=".$stkm_id."'>I'm sure</a></div></div></div>";

    if($findingID!=14){
        $btnAdd = "<br><br><br><button type='button' class='btn btn-outline-dark addExtra' data-toggle='modal' data-target='#modal_add_extra' v-if='ar.first_found_flag==1'>Register extra stockcode</button>";
    }


}
?>






<script type="text/javascript">
let arS     = '<?=$arrSample?>'
    arS     = JSON.parse(arS);
let stkm_id = '<?=$stkm_id?>'
let BIN_CODE= '<?=$BIN_CODE?>'
    
//Declare other global variables
let hideInitialMenu, findingName;

$(document).ready(function() {
    
    //Copy the menu to the other side of the page
    let menuright = $('#menuleft').html();
    $('#menuright').html(menuright);

//     //Initialise the page
    setPage()
    
    $("#formAddExtra").validate({
			rules: {
				extraStockcode: {
                    digits: true,
					maxlength: 9
				},
				extraName: {
					maxlength: 255,
                    required: true
				},
				extraSOH: {
                    digits: true,
                    maxlength: 20
				},
				extraComment: {
					maxlength: 2000
				}
			},
			messages: {
				firstname: "Please enter your firstname"
			}
		});

;

   
    $(".addExtra").click(function(){
        $("#auto_storageID").val("0");
        let test = $("#auto_storageID").val();
        console.log(test)
        $('#areaDeleteExtra').html("");
    });


    $(".btnEditExtra").click(function(){
        let auto_storageID  = $(this).data("asi");
        let extraSTOCK_CODE = $(this).data("sc");
        let extraITEM_NAME  = $(this).data("in");
        let extraSOH        = $(this).data("soh");
        let extraComments   = $(this).data("com");
        // console.log("auto_storageID: "+auto_storageID+"\nextraSTOCK_CODE: "+extraSTOCK_CODE+"\nextraITEM_NAME: "+extraITEM_NAME+"\nextraSOH: "+extraSOH)
        $("#extraStockcode").val(extraSTOCK_CODE);
        $("#extraName").val(extraITEM_NAME);
        $("#extraSOH").val(extraSOH);
        $("#auto_storageID").val(auto_storageID);
        $("#extraComments").val(extraComments);
        // $('#areaDeleteExtra').html("Test");
        console.log("test")
    });
    
    function setPage(){
        console.log(arS[0]['findingID'])
        findingName     = "&nbsp;";
        hideInitialMenu = false;
        $("#resultSelection").removeClass('list-group-item-success');
        $("#resultSelection").removeClass('list-group-item-warning');
        $("#resultSelection").removeClass('list-group-item-danger');
        if (arS[0]['findingID']==14){
            hideInitialMenu        = true;
            findingName     = "No additional stockcodes were found";
            $("#resultSelection").addClass('list-group-item-success');
        }else if(arS[0]['findingID']==15){
            hideInitialMenu        = true;
            findingName     = "You've found some additional stockcodes but havn't investigated them";
            $("#resultSelection").addClass('list-group-item-danger');
        }else if(arS[0]['findingID']==16){
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
        <h1 class='display-4'>Bin to Register</h1>
    </div>
</div>



<div class='row'>

    <div class='col-3 lead' id='menuleft'>
        <ul class="list-group list-group-flush text-center">
            <?=$btnDelete?>
            <?=$btnAdd?>

            <li class="list-group-item hideInitialMenu q1"><b>Are there any stockcodes in addition to this list?</b></li>
            <a class="list-group-item list-group-item-action list-group-item-success hideInitialMenu q1" href='05_action.php?act=save_b2r_nstr&BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>'>No</a>

            <a class="list-group-item list-group-item-action list-group-item-danger hideInitialMenu q1" href='05_action.php?act=save_b2r_extras&BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>'>Yes</a>

        </ul>
    </div>

    <div class='col-6 lead'>

    <form action='05_action.php' method='POST'>
        <table class='table table-sm'>
            <tr><td colspan='4' align='center' id='resultSelection'>&nbsp;</td></tr>
            <tr><td><b>District</b></td><td colspan='2' ><?=$DSTRCT_CODE?></td><td></td></tr>
            <tr><td><b>Warehouse</b></td><td colspan='2' ><?=$WHOUSE_ID?></td><td></td></tr>
            <tr><td><b>SCA</b></td><td colspan='2' ><?=$SUPPLY_CUST_ID?></td><td></td></tr>
            <tr><td><b>Bin</b></td><td colspan='2' ><?=$BIN_CODE?></td><td></td></tr>
            <tr>
                <td colspan='4'>
                    <b>Bin contents</b>
                    <small>(Not all items listed must be sighted, but all additional stockcodes found must be registered.)</small>
                    <!-- <button type="button" class="btn btn-primary btn-sm helpBtn float-right" data-toggle="modal" data-target="#helpModal" data-helpwords="This is the first help modal">?</button> -->
                </td>
            </tr>
            <tr>
                <td><b>Stockcode</b></td>
                <td><b>Name</b></td>
                <td align='right'><b></b></td>
                <td></td>
                <td align='right'><b>Status</b></td>
            </tr>
            <?=$binC?>

            <tr><td colspan='4' class='text-center'><b><br>Extras</b></td></tr>
            <tr>
                <td><b>Stockcode</b></td>
                <td><b>Name</b></td>
                <td><b>SOH</b></td>
                <td></td>
                <td align='right'><b>Status</b></td>
            </tr>
            <?=$binExtra?>

            <!-- <tr><td colspan='3' class='hideInitialMenu'><b>Comments</b><textarea class='form-control' rows='5' name='res_comment' id='res_comment'><?=$res_comment?></textarea></td></tr> -->
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
                <br>NSN/Stockcode
                <br><small>(Where the NSN/stockcode is not available, enter in other identifying ID. Comments mandatory if this option used. Ensure MILIS and other systems are checked.)</small>
                <input type="text" name="extraStockcode" id="extraStockcode" class="form-control addSCFormInputs">
                <br>Stockcode description
                <input type="text" name="extraName" id="extraName" class="form-control addSCFormInputs">
                <br>SOH
                <input type="text" name="extraSOH" id="extraSOH" class="form-control addSCFormInputs">
                <!-- <br>SOH
                <input type="text" name="extraSOH" id="extraSOH" class="form-control addSCFormInputs"> -->
                <br>Comments
                <br><small>(Comments mandatory if NSN/stockcode cannot be identified. Include in comments other identifying information such as contractor part number, serial number. Ensure MILIS and other systems are checked.)</small>
                <textarea name="extraComments" id="extraComments" class="form-control addSCFormInputs" rows='5'></textarea>
                <input type="hidden" name="BIN_CODE" value="<?=$BIN_CODE?>">
                <input type="hidden" name="stkm_id" value="<?=$stkm_id?>">
                <input type="hidden" name="DSTRCT_CODE" value="<?=$DSTRCT_CODE?>">
                <input type="hidden" name="WHOUSE_ID" value="<?=$WHOUSE_ID?>">
                <input type="hidden" name="auto_storageID" id="auto_storageID">
                <input type="hidden" name="act" value="save_b2r_add_extra">
            </p>
            </div>
            <div class="modal-footer">
                <div id='areaDeleteExtra'></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                <input type="submit" class="btn btn-primary" value='Save' id='btnAddSC'>
            </div>
        </div>
        </div>
    </div>
</form>


<?php include "04_footer.php"; ?>