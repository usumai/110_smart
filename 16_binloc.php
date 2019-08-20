<?php include "header.php"; ?>
<?php
$storageID = $_GET["storageID"];
//$isID = $_GET["isID"];                      //Adam Added this line for breadcrumb

$tableRows = "";
$sql = "SELECT *, CASE WHEN res_findings IS NOT NULL THEN 1 ELSE 0 END AS rowStatus FROM 102_storage WHERE storageID =".$storageID;
$result = $conn->query($sql);
$rowMaker = "";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $dataID          	= $row['dataID'];
        $storageID          = $row['storageID'];
        $isID          		= $row['isID'];
        $targetID           = $row['targetID'];          //added this line
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
        $serviceableFlag    = $row['serviceableFlag'];
        $INVENT_CAT_DESC    = $row['INVENT_CAT_DESC'];

        $res_findings       = $row['res_findings'];
        $res_comment        = $row['res_comment'];
        $res_evidence_desc  = $row['res_evidence_desc'];
        $res_unserv_date    = $row['res_unserv_date'];
        $res_createDate     = $row['res_createDate'];
        $childrenCount      = $row['childrenCount'];
        $parentStorageID    = $row['parentStorageID'];
        $deleteDate         = $row['deleteDate'];
        $rowStatus         = $row['rowStatus'];

        if ($rowStatus==1) {
            $rowStatus = "<span class='bg-success btn'>Complete</span>";
        }else{
            $rowStatus = "<span class='bg-danger btn'>Incomplete</span>"; 
            $res_findings = 0;

        }

        $sampleMessage = "";
        if ($sampleFlag==1) {
            $rowColor="";
        }elseif ($sampleFlag==2) {
            $rowStatus = "<span class='bg-success btn btn-sm'>Backup</span>"; 
            $sampleMessage = "<div class='alert alert-danger' role='alert'>If you save this page, this item will be changed from a [Backup] item to a [Primary] item. This cannot be reveresed. Click back to exit.</div>";

        }

        if ($res_unserv_date==null) {
            $res_unserv_date=Date('Y-m-d', strtotime("-6 months"));
        }else{
            $res_unserv_date=date_create($res_unserv_date);
            $res_unserv_date = date_format($res_unserv_date,"Y-m-d"); 
        };

        if (strlen($DSTRCT_CODE)==0&&strlen($WHOUSE_ID)==0) {
            $rowType = "SCA";
            $rowIdent =  $SUPPLY_CUST_ID."(".$SC_ACCOUNT_TYPE.")";
        }else{
            $rowType = "WHS";
            $rowIdent =   $DSTRCT_CODE.$WHOUSE_ID;
        }

}}

$INVENT_CAT=substr($INVENT_CAT,0,2);


$splitRows = "";
$splitCounter = 0;
$split_total = 0;
$sql = "SELECT * FROM 102_storage WHERE parentStorageID =".$storageID." AND deleteDate IS NULL; ";
$result = $conn->query($sql);
$rowMaker = "";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dataID                 = $row['dataID'];
        $split_SOH              = $row['SOH'];
        $split_res_findings     = $row['res_findings'];
        $split_res_unserv_date  = $row['res_unserv_date'];
        $split_total = $split_total + $split_SOH;

        if ($split_res_unserv_date==null) {
            $split_res_unserv_date=Date('Y-m-d', strtotime("-6 months"));
        }else{
            $split_res_unserv_date=date_create($split_res_unserv_date);
            $split_res_unserv_date = date_format($split_res_unserv_date,"Y-m-d");
        }

        $splitRows .="<tr><td>".$split_SOH."</td><td>".$split_res_findings."</td><td>".$split_res_unserv_date."</td><td><button type='button' class='split_delete btn btn-dark btn-sm float-right' value='".$dataID."'>X</button><input type='hidden' name='splitrow_count_".$splitCounter."' value='".$split_SOH."'><input type='hidden' name='splitrow_evidence_".$splitCounter."' value='".$split_res_findings."'><input type='hidden' name='splitrow_date_".$splitCounter."' value='".$split_res_unserv_date."'></td></tr>";
}}

















//Delete this once we import the new data
if ($serviceableFlag==1) {
    $serviceableFlagName="<h4 class='text-success'><b>SERVICEABLE</b></h4>";
}else{
    $serviceableFlagName="<h4 class='text-danger'><b>NOT-SERVICEABLE</b></h4>";
}




if ($TRACKING_IND=="Q"||$TRACKING_IND=="N"){
    $qtrackStatus=1;//Q or N in Tracking ind   
}else{
    $qtrackStatus=0;
}

if ($serviceableFlag==1){
    $preServStatus=1;  
}else{
    $preServStatus=0;
}








?>















<script type="text/javascript" class="init">
$(document).ready(function() {
    $(".datepicker").datepicker( {
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        defaultDate: 7,
        maxDate: "+0D"
    });
    var splitCounter = <?=$splitCounter?>;
    var split_total = <?=$split_total?>;

    $('#rowComment').keyup(function(){
        // alert("hello");
        var rowComment = $('#rowComment').val();
        var commentLength = rowComment.length;
        if (commentLength>0) {
            $("#rowComment").css("background-color", "#FFF");
        }        
    });

    $("form").submit(function(){
        var rowComment = $('#rowComment').val();
        var commentLength = rowComment.length;
        if (<?=$qtrackStatus?>==1) {
            var result_qtrack = $('input[name=result_qtrack]:checked').val();
            // alert("Quantity tracked and not sighted"+result_qtrack); 
            if (result_qtrack==4||result_notqtrack==5) {
                if (commentLength==0) {
                    $("#rowComment").css("background-color", "#E57373");
                    return false;
                }
            }
        }else{
            var result_notqtrack = $('input[name=result_notqtrack]:checked').val();
            // alert("Not quantity tracked and not sighted"+result_notqtrack);
            if (result_notqtrack==7) {
                if (commentLength==0) {
                    $("#rowComment").css("background-color", "#E57373");
                    return false;
                }
            }
        }
    });
        // alert(rowComment.length);

    function change_notqtrack() {
        var result_notqtrack = $('input[name=result_notqtrack]:checked').val();
        if (result_notqtrack==2||result_notqtrack==5){
            $("#tree06").show();
        }else if(result_notqtrack==0){
            $("#tree06").hide();
        }else{
            $("#tree06").hide();
        }
    };

    function change_qtrack() {
        $("#saveButton").prop('disabled', false);
        $("#saveButtonError").html("");
        var result_qtrack = $('input[name=result_qtrack]:checked').val();
        if (result_qtrack==1||result_qtrack==3||result_qtrack==5){
            $("#tree05").hide();
            $("#tree06").hide();
        }else if (result_qtrack==2){
            $("#tree05").hide();
            $("#tree06").show();
        }else if (result_qtrack==4){
            $("#tree05").show();
            $("#tree06").hide();
            $("#saveButton").prop('disabled', true);
            $("#saveButtonError").html("<span style='color:red'>You must account for each SOH</span>");
        }else if (result_qtrack==0){
            $("#tree05").hide();
            $("#tree06").hide();
        }
    };



    function change_usDate() {
        var usDate      = $("#usDate").val();
        var usDateExists= usDate.length;
    };

    $('input[type=radio][name=result_notqtrack]').change(change_notqtrack);
    $('input[type=radio][name=result_qtrack]').change(change_qtrack);
    $("#usDate").change(change_usDate);

    function stateMaker() {
        $("#tree03").hide();
        $("#tree04").hide();
        $("#tree05").hide();
        $("#tree06").hide();
        $("#tree07").hide();
        $("#tree08").hide();
        $("#tree09").hide();
        $("#tree10").hide();

        if (<?=$qtrackStatus?>==1) {
            $("#tree04").show();
            change_qtrack()
        }else{
            $("#tree03").show();
            change_notqtrack()
        }   
    };


    function refreshTable() {
        $("#splitTable").find("tr:gt(1)").remove();
        $.post("005_action.php",
        {
            actionType: "get_split_table",
            storageID: <?=$storageID?>,
            isID: <?=$isID?>                           //Adam Added this line for breadcrumb
        },
        function(data, status){
            $("#splitTable").append(data)
        });

        $.post("005_action.php",
        {
            actionType: "get_split_total",
            storageID: <?=$storageID?>,
            isID: <?=$isID?>                            //Adam Added this line for breadcrumb
        },
        function(data, status){
            var result_qtrack = $('input[name=result_qtrack]:checked').val();
            split_total = parseInt(data, 10);
            $("#split_total").text(split_total);
            // alert(split_total)
            // alert(parseInt(<?=$SOH?>, 10))
            // alert(result_qtrack)

            if (split_total<parseInt(<?=$SOH?>, 10)&&result_qtrack==4&&<?=$qtrackStatus?>==1) {
                // alert("disabled");
                $("#saveButton").prop('disabled', true);
                $("#saveButtonError").html("<span style='color:red'>You must account for each SOH before you can save</span>");
            }else{
                // alert("enabled");
                $("#saveButton").prop('disabled', false);
                $("#saveButtonError").html("");
            }

        });
    };




    $("#addSplit").click(function(){ 
        var split_count         = $("#split_count").val();
        var split_evidence      = $("#split_evidence").val();
        var split_evidence_desc = $("#split_evidence option:selected").text();
        var split_date          = $("#split_date").val();
        var dateExists          = split_date.length;
        var update_user         = "<?=$user_name?>";

         $("#split_count").css({'background-color':'white'});
         $("#split_evidence").css({'background-color':'white'}); 
         $("#split_date").css({'background-color':'light-grey'});

        if (split_evidence==1) {
            split_evidence_name = "Sighted - Serviceable";
        }else if (split_evidence==2) {
            split_evidence_name = "Sighted - Unserviceable - With date";
        }else if (split_evidence==3) {
            split_evidence_name = "Sighted - Unserviceable - No date";
        }else if (split_evidence==8) {                                      //Adam Added
            split_evidence_name = "Sighted - Unserviceable - No date (>6 months)";      //Adam Added
        }else if (split_evidence==4) {
            split_evidence_name = "Not Sighted, Evidence provided - Serviceable";
        }else if (split_evidence==5) {
            split_evidence_name = "Not Sighted, Evidence provided - Unserviceable - With Date";
        }else if (split_evidence==6) {
            split_evidence_name = "Not Sighted, Evidence provided - Unserviceable - No Date";
        }else if (split_evidence==9) {                              //Adam Added
            split_evidence_name = "Not Sighted, Evidence provided - Unserviceable - No Date (>6 months)"; //Adam Added
        }else if (split_evidence==7) {
            split_evidence_name = "Not sighted - No evidence";
        }


        var intRegex = /^\d+$/;
        if(intRegex.test(split_count)) {


            if (split_count>=<?=$SOH?>) {
                digitFlag = 1;
            }else{

                digitFlag = 1;
            }

            
        }else{
        // alert('I am not a number');
            $("#split_count").css({'background-color':'red'});
            digitFlag = 0;
        }


        if (split_evidence==0) {
            // alert('I am blank');
             $("#split_evidence").css({'background-color':'red'});
            evidenceFlag = 0;
        }else{
            // alert('I am not blank');
            evidenceFlag = 1;
        }

        if (split_evidence==2||split_evidence==5){
            if (dateExists==0) {
                $("#split_date").css({'background-color':'red'});
                dateFlag = 0;
            }else{
                $("#split_date").css({'background-color':'#EEE'});
                dateFlag = 1;
            }
        }else{
            $("#split_date").css({'background-color':'#EEE'});
            dateFlag = 1;
        }

                

        var allGoodFlag = digitFlag+dateFlag+evidenceFlag;

        if (allGoodFlag==3) {
            $.post("005_action.php",
            {
                actionType: "save_splitrow",
                isID: "<?=$isID?>",
                storageID: "<?=$storageID?>",
                targetID: "<?=$targetID?>",                         // Adam Added targetID
                DSTRCT_CODE: "<?=$DSTRCT_CODE?>",                   // Adam Added DSTRCT_CODE
                WHOUSE_ID: "<?=$WHOUSE_ID?>",                       // Adam Added WHOUSE_ID
                SUPPLY_CUST_ID: "<?=$SUPPLY_CUST_ID?>",             // Adam Added SUPPLY_CUST_ID
                res_comment: "<?=$res_comment?>",                   // Adam Added res_comment
                isID: "<?=$isID?>",                                 // Adam Added isID
                split_count: split_count,
                split_evidence: split_evidence,
                split_evidence_desc: split_evidence_desc,
                split_date: split_date,
                update_user: update_user
            },
            function(data, status){
                // alert("Data: " + data + "\nStatus: " + status);
                refreshTable()
            });

            $("#split_total").text(split_total); 
            $("#split_count").val("");
            $("#split_evidence").val(0);
            $("#split_date").val(""); 
            $("#split_date").prop( "disabled", true ); 


        }
    });

    $('body').on('click', 'button.split_delete', function() {
        var dataID = $(this).val();
        $.post("005_action.php",
        {
            actionType: "save_delete_splitrow",
            dataID: dataID
        },
        function(data, status){
            // alert("Data: " + data + "\nStatus: " + status);
            refreshTable()
        });

    });


    $("#split_count").keyup(function(){
        var split_count     = $("#split_count").val();
        var intRegex = /^\d+$/;
        if(intRegex.test(split_count)) {
            $("#split_count").css({'background-color':'white'});
        }else{
            $("#split_count").css({'background-color':'red'});
        }
    });


    $("#split_evidence").change(function(){
        var split_evidence = $("#split_evidence").val();
        if (split_evidence!=0){
            $("#split_evidence").css({'background-color':'white'});
        }
        if (split_evidence==2||split_evidence==5){
            $("#split_date").css({'background-color':'white'});
            $("#split_date").prop( "disabled", false );
        }else{
            $("#split_date").val("");
            $("#split_date").css({'background-color':'#EEE'});
            $("#split_date").prop( "disabled", true );
        }
    });

    $("#split_date").change(function(){
        var split_date      = $("#split_date").val();
        var dateExists      = split_date.length;
        if (dateExists==0) {
            $("#split_date").css({'background-color':'red'});
        }else{
            $("#split_date").css({'background-color':'white'});
        }
    });



    $("input[name=result_notqtrack][value=<?=$res_findings?>]").attr('checked', 'checked');
    $("input[name=result_qtrack][value=<?=$res_findings?>]").attr('checked', 'checked');
    $("input[type=radio]").change(stateMaker);
    $("#dateDetermine").change(stateMaker);
    $("#evidenceReason").change(stateMaker);
    $("#result_qtrack").val(<?=$res_findings?>);
    stateMaker()
    refreshTable()
    change_usDate()
});



$(document).ready(function(){
  $('.isInfobtn').click(function(){
    var btnid=$(this).attr('value');

    switch(btnid) {
        case "1": //All Serviceable
            var isInfo="Also includes items currently in use.";
            break;
        case "2": //None Serviceable - With Date
            var isInfo="Date to be cross-checked with MILIS.";
            break;
        case "3": //None Serviceable - No Date
            var isInfo="This option must only be used if a date cannot be verified within MILIS.  Comments are mandatory with this option.";
            break;
        case "4": //Split Category
            var isInfo="Only use for QTY tracked items.  The sum of all entries must be equal to or greater than total listed stock on hand.";
            break;
        case "5": //No Items Found
            var isInfo="Ensure site Point of Contact is aware of environmental requirements.  Comments are mandatory with this option.";
            break;
        default:
    }   

    $('#isInfoText',$('#isInformationModal')).val(isInfo);
  });
});


</script>


  


<style>
    /*table{
        font-size: 12px;
    }*/
td{
    padding: 0px!important;
}


.radioStyle{
    width:2em;
    height:2em;
}
</style>









<nav id="breadcrumb">
    <a href="index.php">Home</a>
    <a href="target_select.php?isID=<?=$isID?>"> / Target Select</a>
    <a href="activity.php?isID=<?=$isID?>&targetID=<?=$targetID?>"> / Activity</a>
    <a href="" style="pointer-events: none; cursor: default;"> / Data Entry</a>
</nav>



<!-- Create a Modal when button is pushed -->
<div class="modal fade" id="isInformationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">IS Category Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <textarea class="form-control" id="isInfoText" style="resize: none; border-style: none"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="border-top: none">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="container">

<div class="card card-inverse" style="border-color: #000; border-width: 3px; padding:10px">
    <div class="card-block">


	<div class="row">
		<div class="col-lg">
            <h3>
                <span class="btn btn-dark"><?=$rowNo?></span><?=$rowStatus?>
                <b><?=$STOCK_CODE?> - <?=$ITEM_NAME?></b>
                <a href="activity.php?isID=<?=$isID?>&targetID=<?=$targetID?>" class="btn btn-light float-right">Back</a>
            </h3>
			<h4><?=$STK_DESC?></h4>
                <?=$sampleMessage?>
		</div>
	</div>

<form method="post" action="005_action.php">
    <div class="row">
        <div class="col-lg">
            <table class="table table-sm">
                <tr><td><b>TRACKING_REFERENCE:</b></td><td><h4><b><?=$TRACKING_REFERENCE?></b></h4></td></tr>
                <!-- <tr><td><b>SERVICEABILITY:</b></td><td><?=$serviceableFlagName?></h4></td></tr> removed for ANAO -->
                <tr><td><b>INVENT_CAT:</b></td><td><h4><?=$INVENT_CAT;?></h4></td></tr>
                <tr><td><b>DSTRCT_CODE:</b></td><td><h4><?=$DSTRCT_CODE?></h4></td></tr>
                <tr><td><b>WHOUSE_ID:</b></td><td><h4><?=$WHOUSE_ID?></h4></td></tr>
                <tr><td><b>SUPPLY_CUST_ID:</b></td><td><h4><?=$SUPPLY_CUST_ID?></h4></td></tr>
                <tr><td><b>BIN_CODE:</b></td><td><h4><?=$BIN_CODE?></h4></td></tr>
                <tr><td><b>SOH:</b></td><td><h4><?=$SOH?></h4></td></tr>
                <tr><td><b>SC_ACCOUNT_TYPE:</b></td><td><h4><?=$SC_ACCOUNT_TYPE?></h4></td></tr>
                <tr><td><b>TRACKING_IND:</b></td><td><h4><?=$TRACKING_IND?></h4></td></tr>
            </table>
            <b>Comment:</b><i>(Optional)</i>
            <textarea class="form-control" rows="6" name="rowComment" id="rowComment" style="resize: none"><?=$res_comment?></textarea>
        </div>

        <div class="col-lg">
            <div id="tree03">


                <table class="table">
                    <tr><td colspan="3"><h4><b>Incomplete</b></h4></td></tr>
                    <tr>
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="0" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Incomplete (Clear all)</h4></td>
                        <td style="vertical-align: middle;"></td>
                    </tr>
                    <tr bgcolor="#A5D6A7"><td colspan="3"><h4><b>Item sighted</b></h4></td></tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="1" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Serviceable</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn1" value="1">?</button></td>
                    </tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="2" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - With Date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn2" value="2">?</button></td>
                    </tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="3" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - No Date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn3" value="3">?</button></td>
                    </tr>
                        <!--Adam Added-->
                   <!--  <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="8" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - Date >6 months</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn8" value="8">?</button></td>
                    </tr> Removed for ANAO-->
                        <!--Adam Added-->
                    <tr bgcolor="#FFCC80"><td colspan="3"><h4><b>Item not sighted, evidence provided</b></h4></td></tr>
                    <tr bgcolor="#FFCC80">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="4" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Serviceable</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn4" value="1">?</button></td>
                    </tr>
                    <tr bgcolor="#FFCC80">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="5" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - With Date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn5" value="2">?</button></td>
                    </tr>
                    <tr bgcolor="#FFCC80">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="6" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - No Date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn6" value="3">?</button></td>
                    </tr>
                        <!--Adam Added-->
<!--                     <tr bgcolor="#FFCC80">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="9" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Unserviceable - Date >6 months</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn9" value="9">?</button></td>
                    </tr> removed for ANAO-->
                        <!--Adam Added-->
                    <tr bgcolor="#E57373"><td colspan="3"><h4><b>Item not found, no evidence provided</b></h4></td></tr>
                    <tr bgcolor="#E57373">
                        <td width="5%"> <input type="radio" name="result_notqtrack" value="7" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Not sighted - No evidence</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn7" value="5">?</button></td>
                    </tr>
                </table>
            </div>

            <div id="tree04">
                <table class="table">
                    <tr><td colspan="3"><h4><b>Incomplete</b></h4></td></tr>
                    <tr>
                        <td width="5%"> <input type="radio" name="result_qtrack" value="0" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Incomplete</h4></td>
                        <td style="vertical-align: middle;"></td>
                    </tr>
                    <tr bgcolor="#A5D6A7"><td colspan="3"><h4><b>Sighted or found evidence of all items</b></h4></td></tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="1" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>All serviceable</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn11" value="1">?</button></td>
                    </tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="2" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>None serviceable - With date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn12" value="2">?</button></td>
                    </tr>
                    <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="3" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>None serviceable - No date</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn13" value="3">?</button></td>
                    </tr>
                    <!--Adam Added-->
<!--                     <tr bgcolor="#A5D6A7">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="8" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>None serviceable - Date >6 months</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn14" value="14">?</button></td>
                    </tr> Removed for ANAO-->
                        <!--Adam Added-->
                    <tr bgcolor="#FFCC80"><td colspan="3"><h4><b>Split category</b></h4></td></tr>
                    <tr bgcolor="#FFCC80">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="4" class="radioStyle"></td>
                        <td style="vertical-align: middle;">
                            <h4>
                                One, some or all of the following:
                                <br>-Not all items were found
                                <br>-Items were in different categories
                                <br>-Found more than original quantity
                            </h4>
                        </td>
                        <td style="vertical-align: top;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn15" value="4">?</button></td>
                    </tr>
                    <tr bgcolor="#E57373"><td colspan="3"><h4><b>No items found, no evidence provided</b></h4></td></tr>
                    <tr bgcolor="#E57373">
                        <td width="5%"> <input type="radio" name="result_qtrack" value="5" class="radioStyle"></td>
                        <td style="vertical-align: middle;"><h4>Not sighted - No evidence</h4></td>
                        <td style="vertical-align: middle;"><button type="button" class="btn btn-basic isInfobtn" data-toggle="modal" data-target="#isInformationModal" id="isInfobtn16" value="5">?</button></td>
                    </tr>
                </table>



            </div>

            <div id="tree06">
                Unserviceable date
                <input type="text" class="form-control datepicker" id="usDate"  name="usDate"  value="<?=$res_unserv_date?>" readonly>
            </div>

            <div class="row well" id="outcome01">
                <div class="col-lg-2"></div>
                <div class="col-lg-10">
                    <input type="hidden" name="splitCounter" value="<?=$splitCounter?>" id="splitCounter">
                    <input type="hidden" name="storageID" value="<?=$storageID?>">
                    <input type="hidden" name="qtrackStatus" value="<?=$qtrackStatus?>">
                    <input type="hidden" name="actionType" value="save_row_details">
                    <span id="saveButtonError" class="pull-right"></span>
                    <button class="btn btn-dark float-right" type="submit" id="saveButton">Save</button>
                </div>
            </div>

        </div>
    </div>


        <div class="row well" id="tree05">
            <div class="col-lg-2">
                Split
                <br><b>Total:</b><span id="split_total"><?=$split_total?></span>
            </div>
            <div class="col-lg-10">
                <table class="table" id="splitTable">
                    <tr>
                        <td width="130px">Count</td>
                        <td>Sighted</td>
                        <td>Action</td>
                    </tr>
                    <tr>
                        <td><input type="text" name="split_count" class="form-control" id="split_count"></td>
                        <td>
                            <select class="form-control" id="split_evidence">
                                <option value="0">Please select an option</option>
                                <option value="1" style="background: #A5D6A7;">Sighted - Serviceable</option>
                                <option value="2" style="background: #A5D6A7;">Sighted - Unserviceable - With Date</option>
                                <option value="3" style="background: #A5D6A7;">Sighted - Unserviceable - No Date</option>
                                <!--Adam Added-->
                                <!-- <option value="8" style="background: #A5D6A7;">Sighted - Unserviceable - Date >6 months</option>Removed for ANAO -->
                                <!--Adam Added-->
                                <option value="4" style="background: #FFCC80;">Not Sighted, Evidence provided - Serviceable</option>
                                <option value="5" style="background: #FFCC80;">Not Sighted, Evidence provided - Unserviceable - With Date</option>
                                <option value="6" style="background: #FFCC80;">Not Sighted, Evidence provided - Unserviceable - No Date</option>
                                <!--Adam Added-->
                                <!-- <option value="9" style="background: #FFCC80;">Not Sighted, Evidence provided - Unserviceable - Date >6 months</option> Removed for ANAO-->
                                <!--Adam Added-->
                                <option value="7" style="background: #E57373;">Not sighted - No evidence</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="" class="form-control datepicker" id="split_date" name="split_date" disabled readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-dark btn-sm" id="addSplit">Add</button>
                        </td>
                    </tr>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>



        


    </div>
</div>

</form>





</head>
<body>
 






































<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
















</div>
<?php include "footer.php"; ?>

