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
        $res_update_user    = $row['res_update_user'];
        $res_findings       = $row['res_findings'];
        $res_comment        = $row['res_comment'];
        $res_unserv_date    = $row['res_unserv_date'];
        $res_create_date    = $row['res_create_date'];
        $res_create_date    = $row['res_create_date'];
        $res_create_date    = $row['res_create_date'];

        $complete = 'false';
        if(!empty($res_create_date)){
            $complete = 'true';
        }


}}

if(empty($res_unserv_date)){
    $res_unserv_date = "";
}


// $complete = 'true';
?>


<script type="text/javascript">
let TRACKING_IND    = "<?=$TRACKING_IND?>";
let complete        = <?=$complete?>;
let SOH             = "<?=$SOH?>";
console.log(SOH)
SOH = Number(SOH)
console.log(typeof SOH)
let resultSelection=false;

let dispQtrack  = TRACKING_IND=="Q";
let dispStrack  = TRACKING_IND=="S";
dispQtrack      = complete ? false : dispQtrack;
dispStrack      = complete ? false : dispStrack;


resultOptions = {
    1: {
        'name':'Serial tracked - Item sighted - Serviceable',
        'color':'success',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    2: {
        'name':'Serial tracked - Item sighted - Unserviceable - with date',
        'color':'success',
        'reqDate':true,
        'reqSplit':false,
        'reqComment':false
    },
    3: {
        'name':'Serial tracked - Item sighted - Unserviceable - no date',
        'color':'success',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    4: {
        'name':'Serial tracked - Item not sighted - Serviceable',
        'color':'warning',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    5: {
        'name':'Serial tracked - Item not sighted - Unserviceable - with date',
        'color':'warning',
        'reqDate':true,
        'reqSplit':false,
        'reqComment':false
    },
    6: {
        'name':'Serial tracked - Item not sighted - Unserviceable - no date',
        'color':'warning',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    7: {
        'name':'Serial tracked - Item not found, no evidence provided',
        'color':'danger',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },

    8: {
        'name':'Quantity tracked - Sighted or found evidence of all items - All serviceable',
        'color':'success',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    9: {
        'name':'Quantity tracked - Sighted or found evidence of all items - None serviceable - with date',
        'color':'success',
        'reqDate':true,
        'reqSplit':false,
        'reqComment':false
    },
    10:{
        'name':'Quantity tracked - Sighted or found evidence of all items - None serviceable - no date',
        'color':'success',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':false
    },
    11:{
        'name':'Quantity tracked - Split category - One, some or all of the following:<br>+ Not all items were found<br>+ Items were in different categories<br>+ Found more than original quantity',
        'color':'warning',
        'reqDate':false,
        'reqSplit':true,
        'reqComment':true
    },
    12:{
        'name':'Quantity tracked - No items found, no evidence provided',
        'color':'danger',
        'reqDate':false,
        'reqSplit':false,
        'reqComment':true
    }
}







$(document).ready(function() {
    
    let menuright = $('#menuleft').html();
    $('#menuright').html(menuright);

    setPage()


    $('.dispStrack').click(function(){
        complete = true;
        resultSelection = $(this).val();
        setPage()
    });

    $('.dispQtrack').click(function(){
        complete = true;
        resultSelection = $(this).val();
        setPage()
    });

    
    $(document).on('keyup', "#res_comment", function(){
        setPage()
    });


    $('body').on('click', '#btnClear', function() {
        complete = false;
        resultSelection = false;
        setPage()
        $("#resultSelection").html('&nbsp;');
    });


    function setPage(){     
        dispQtrack = complete ? false : TRACKING_IND=="Q"
        dispStrack = complete ? false : TRACKING_IND=="S"
        $('.dispQtrack').toggle(dispQtrack);
        $('.dispStrack').toggle(dispStrack);
        $('.complete').toggle(complete);

        if(resultSelection){
            $('#res_findings').val(resultSelection);   
            $('#areaDate').toggle(resultOptions[resultSelection]['reqDate']);
            $('#areaSplit').toggle(resultOptions[resultSelection]['reqSplit']);
            $("#resultSelection").html("<b>"+resultOptions[resultSelection]['name']+"</b>");
            $("#resultSelection").addClass('list-group-item-'+resultOptions[resultSelection]['color']);

            $('#btnSubmit').show();

            let res_unserv_date = $('#res_unserv_date').val();
            if(resultOptions[resultSelection]['reqDate']&& res_unserv_date.length<=0){
                $('#btnSubmit').hide();
            }

            let res_comment = $('#res_comment').val();
            if(resultOptions[resultSelection]['reqComment']&& res_comment.length<=5){
                $('#btnSubmit').hide();
            }

            if(resultSelection==11){//Split category
                checkSplityAllGood()
            }

        }else{
            $('#areaDate').toggle(false);
            $('#areaSplit').toggle(false);
            $("#resultSelection").removeClass('list-group-item-success');
            $("#resultSelection").removeClass('list-group-item-warning');
            $("#resultSelection").removeClass('list-group-item-danger');
        }
        

    }




    $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".datepicker").change(function(){
        setPage()
    })






    //SPLITY SECTION

    validateSplity()

    let splityCats = [1,2,3,4,5,6,7];
    let splityOptions = "";
    for (let key in splityCats) {
        splityOptions += "<option value='"+splityCats[key]+"' class='list-group-item-"+resultOptions[splityCats[key]]['color']+"'>"+resultOptions[splityCats[key]]['name']+"</option>";
    }
    $('#splityResult').html(splityOptions);

    $(document).on('change', ".splity", function(){
        validateSplity()
    });

    $(document).on('keyup', ".splity", function(){
        validateSplity()
    });

    function validateSplity(){
        let splityCount     = $('#splityCount').val();
        let splityResult    = $('#splityResult').val();
        let splityDate      = $('#splityDate').val();
        $("#addSplity").prop('disabled', false);

        if(isNaN(splityCount)){
            $("#addSplity").prop('disabled', true);
        }
        if(splityCount<=0){
            $("#addSplity").prop('disabled', true);
        }

        if(splityResult){
            if(resultOptions[splityResult]['reqDate']&& splityDate.length<=0){
                $("#addSplity").prop('disabled', true);
            }
        }
    }

    $("#addSplity").click(function(){
        addSplity();
    })

    let splityTotal=0;
    let splityId=0;

    function addSplity(){
        splityId++;
        let splityCount     = $('#splityCount').val();
        let splityResult    = $('#splityResult').val();
        let splityDate      = $('#splityDate').val();

        let btnRemoveSplity = "<button type='button' class='btn btn-outline-dark btnRemoveSplity' value='"+splityId+"'><i class='fas fa-minus'></i></button>"

        $('#splityTable tr:last').before("<tr id='splityRow"+splityId+"'><td>"+splityCount+"</td><td>"+resultOptions[splityResult]['name']+"</td><td>"+splityDate+"</td><td>"+btnRemoveSplity+"</td></tr>")

// $('#splityLanding').append(`<input type="hidden" id="splityRecord`+splityId+`" name="splityRecord`+splityId+` value="{'splityCount':'`+splityCount+`','splityResult':'`+splityResult+`','splityDate':'`+splityDate+`' }">`)

$('#splityLanding').append("<input type='hidden' name='splityRecord[]' value='"+splityId+"'>")

$('#splityLanding').append("<input type='hidden' name='splityCount["+splityId+"]' value='"+splityCount+"'>")
$('#splityLanding').append("<input type='hidden' name='splityResult["+splityId+"]' value='"+splityResult+"'>")
$('#splityLanding').append("<input type='hidden' name='splityDate["+splityId+"]' value='"+splityDate+"'>")


        splityTotal += Number(splityCount);

        console.log("splityTotal:"+splityTotal)
        console.log(typeof splityTotal)
        console.log("SOH:"+SOH)
        console.log(typeof SOH)
        console.log(splityTotal<SOH)
        checkSplityAllGood()

        $('#splityTotal').text(splityTotal);
        $('#splityCount').val('');
        $('#splityResult').val(1);
        $('#splityDate').val('');
        $("#addSplity").prop('disabled', true);
    }


    $(document).on('click', ".btnRemoveSplity", function(){
        let splityId = $(this).val()
        $("#splityRow"+splityId).remove()

        splityTotal -= Number(splityCount);
        $('#splityTotal').text(splityTotal);

        checkSplityAllGood()

    });


    function checkSplityAllGood(){
        $('#btnSubmit').show()

        if(splityTotal<SOH){
            $('#btnSubmit').hide();
        }
        if(isNaN(splityTotal)){
            $('#btnSubmit').hide();
            splityTotal = 0
        }
        let res_comment = $('#res_comment').val();
        if(resultOptions[resultSelection]['reqComment']&& res_comment.length<=5){
            $('#btnSubmit').hide();
        }

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
        
        <div class='text-center'><div class="dropdown"><button class="btn btn-outline-danger complete dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id='dispBtnClear'>Clear</button><div class="dropdown-menu bg-danger" aria-labelledby="dropdownMenuButton"><button class='dropdown-item bg-danger text-light' id='btnClear'>I'm sure</button></div></div></div>


        <ul class="list-group list-group-flush text-center">

            <li class="list-group-item dispStrack"><b>Item sighted</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='1'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='2'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispStrack" value='3'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispStrack"><b>Item not sighted, evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='4'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='5'>Unserviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-warning dispStrack" value='6'>Unserviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispStrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispStrack" value='7'>Not sighted - No evidence</button>




            <li class="list-group-item dispQtrack"><b>Sighted&nbsp;or&nbsp;found&nbsp;evidence&nbsp;of&nbsp;all&nbsp;items</b></li>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='8'>Serviceable</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='9'>None&nbsp;serviceable&nbsp;-&nbsp;with&nbsp;date</button>
            <button class="list-group-item list-group-item-action list-group-item-success dispQtrack" value='10'>None&nbsp;serviceable&nbsp;-&nbsp;no&nbsp;date</button>

            <li class="list-group-item dispQtrack"><b>Split&nbsp;category</b></li>
            <button class="list-group-item list-group-item-action list-group-item-warning dispQtrack" value='11'>One, some or all of the following:
                <br>-Not all items were found 
                <br>-Items were in different categories 
                <br>-Found more than original quantity
            </button>

            <li class="list-group-item dispQtrack"><b>No items found, no evidence provided</b></li>
            <button class="list-group-item list-group-item-action list-group-item-danger dispQtrack" value='12'>Not sighted - No evidence</button>
        </ul>
    </div>

    <div class='col-6 lead'>

    <form action='05_action.php' method='POST'>
        <table class='table table-sm'>
            <tr><td colspan='2' id='resultSelection'>&nbsp;</td></tr>
            <tr><td><b>District</b></td><td><?=$DSTRCT_CODE?></td></tr>
            <tr><td><b>Warehouse</b></td><td><?=$WHOUSE_ID?></td></tr>
            <tr><td><b>SCA</b></td><td><?=$SUPPLY_CUST_ID?></td></tr>
            <tr><td><b>Bin</b></td><td><?=$BIN_CODE?></td></tr>
            <tr><td><b>SOH</b></td><td><?=$SOH?></td></tr>
            <tr><td nowrap><b>SC Account type</b></td><td><?=$SC_ACCOUNT_TYPE?></td></tr>
            <tr><td nowrap><b>Tracking indicator</b></td><td><?=$TRACKING_IND?></td></tr>
            <tr><td colspan='2'><b>Comments</b><textarea class='form-control' rows='5' name='res_comment' id='res_comment'><?=$res_comment?></textarea></td></tr>
            <tr id='areaDate'><td><b>Date</b></td><td><input type='text' class='form-control datepicker' name='res_unserv_date' id='res_unserv_date' value='<?=$res_unserv_date?>' readonly></td></tr>
            <tr id='areaSplit'><td colspan='2'>
                <b>Split area</b><br>
                <table class='table' id='splityTable'>
                    <tr>
                        <td width='20%'>Count</td>
                        <td>Sighted</td>
                        <td>Date</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type='text' class='form-control splity' name='splityCount' id='splityCount'></td>
                        <td>
                            <select class='form-control splity' name='splityResult' id='splityResult'>
                                <option value='1'></option>
                            </select>
                        </td>
                        <td><input type='text' class='form-control datepicker splity' name='splityDate' id='splityDate' readonly></td>
                        <td><button type='button' class='btn btn-outline-dark float-right' id='addSplity'><i class='fas fa-plus'></i></button></td>
                    </tr>
                    <tr><td id='splityTotal'></td><td>Total</td><td></td><td></td></tr>
                </table>
            </td></tr>
            <tr><td colspan='2'>

                    <span id='splityLanding'></span>

                    <input type='hidden' name='act' value='save_msi_bin_stk'>
                    <input type='hidden' name='res_findings' id='res_findings' value='<?=$res_findings?>'>
                    <input type='hidden' name='auto_storageID' value='<?=$auto_storageID?>'>
                    <input type='hidden' name='storageID' id='storageID' value='<?=$storageID?>'>
                    <input type='submit' id='btnSubmit' value='Save' class='btn btn-outline-dark float-right complete' >
            </td></tr>
        </table>
        </form>
    </div>

    
    <div class='col-3 lead' id='menuright'></div>

</div>


<?php include "04_footer.php"; ?>