<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$ass_id	= $_GET["ass_id"];


$data = [];
$sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
    $data["asset"] = $r;
}}

$reasoncodes = [];
$sql = "SELECT * FROM smartdb.sm15_rc ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
    $data["reasoncodes"][] = $r;
}}
$data = json_encode($data);

?>


<style type="text/css">
	label{
		margin-bottom:0px;
		font-weight: bold;
	}
	.form-group{
		margin-bottom:5px;
	}
</style>



<script>
$( function() {
    let data = <?=$data?>;
    let tempData = [];

    let colGreen= "#78e090";
    let colRed  = "#FFCDD2";
    let colAmber= "#FFE0B2";


    console.log(data)
    tempData["arrRC"]=[];
    for (let rc in data["reasoncodes"]){
        let res_reason_code = data["reasoncodes"][rc]["res_reason_code"];
        let rc_desc         = data["reasoncodes"][rc]["rc_desc"];
        let rc_long_desc    = data["reasoncodes"][rc]["rc_long_desc"];
        let rc_examples     = data["reasoncodes"][rc]["rc_examples"];
        let rc_section      = data["reasoncodes"][rc]["rc_section"];
        let btnRCL = "<div class='col-2'><button class='btn btn-info rc_select' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
        let btnRCR = "<div class='col-2'><button class='btn btn-info rc_select float-right' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
        let rowRC  = "<div class='row rc_option rc_section"+rc_section+"'>"+btnRCL+"<div class='col-8'><b>"+rc_desc+"</b>"+rc_long_desc+" <br>Example: "+rc_examples+"</div>"+btnRCR+"</div>"
        
        tempData["arrRC"][res_reason_code]=rc_desc;
        $("#areaRCs").append(rowRC)
    }
    
	$( ".datepicker" ).datepicker({ 
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true 
	});


    function fnInitialSetup(){
        $(".tf").each(function(){
            let fieldName = $(this).data("name");
            let fieldValue = data["asset"][fieldName];
            $(this).html(fieldValue);
            // console.log("Publishing field: "+fieldName+" with value:"+fieldValue)
        })

        $(".txy").each(function(){// Initially populate the asset fields with the current db result data - highlight changes
            // 
            let fieldName   = $(this).data("name");
            let originalFV  = data["asset"][fieldName];
            let currentFV   = data["asset"]["res_"+fieldName];
            $(this).val(currentFV);

            if(originalFV!=currentFV){
                $(this).css("background-color",colGreen)
            }

            // if($(this).prop('disabled')){
            //     $(this).css("background-color","white")
            // }else{
            //     $(this).css("background-color","#e9ecef")
            // }



        })        
    }




    $(".txy").keyup(function(){// Event fires when field is edited
        console.log("Changed")
        let fieldName   = $(this).data("name");
        let validation  = $(this).data("vld");
        let originalFV  = data["asset"][fieldName];
        let currentFV   = data["asset"]["res_"+fieldName];
        let changedFV   = $(this).val();
        let thisElement = $(this);
        $(this).css("background-color","white")

        if (!tempData["validationNote"+fieldName]){
            tempData["validationNote"+fieldName] = true
            $(this).after("<p id='validationNote"+fieldName+"' class='text-danger'></p>");
        }
        $("#validationNote"+fieldName).hide();

        let validRes    = fnRunValidation(changedFV, validation)
        if(!validRes["test"]){//Failed validation
            $(this).css("background-color",colRed)
            $("#validationNote"+fieldName).text(validRes["note"]);
            $("#validationNote"+fieldName).show();
        }else{
            $(this).css("background-color",colAmber)
            fieldName = fieldName=="res_comment" ? fieldName : "res_"+fieldName;
            $.post("api.php",{
                act: "save_AssetFieldValue",
                ass_id:     data["asset"]["ass_id"],
                fieldName:  fieldName,
                fieldValue: changedFV
            },
            function(confirmedFV, status){
                if(changedFV==confirmedFV){//Saved successfully
                    data["asset"][fieldName] = confirmedFV
                    if(originalFV==confirmedFV){// Value hasn't changed from the very original
                        thisElement.css("background-color","white")
                    }else{// Value has saved and is different from original
                        thisElement.css("background-color",colGreen)
                    }
                }else{
                    $("#validationNote"+fieldName).show();
                    $("#validationNote"+fieldName).text("This value has not been saved to the database");
                }
            });
        }
    })

    $(".rcCat").click(function(){
        catSelection = $(this).val();
        if(catSelection=="ND10"){
            data["asset"]["res_reason_code"] = "ND10"
            fnSaveReasonCode("ND10")
        }else{
            tempData["tempReasonCat"] = catSelection
            setPage()
        }
        
    });

    $(".btnCancel").click(function(){
        tempData["tempReasonCat"] = null
        setPage(data)
    });


    $(".btnClearSure").click(function(){
        data["asset"]["res_reason_code"]= null
        tempData["tempReasonCat"]       = null
        // fnSaveReasonCode("##NULL##")
        //Needs to write all original details over the result set and set reasoncode to null
        $.post("api.php",{
            act:    "save_ResetAssetResults",
            ass_id: data["asset"]["ass_id"]
        },
        function(res, status){
            console.log("Before")
            console.log(data)
            data = JSON.parse(res)
            console.log("After")
            console.log(data)
            fnInitialSetup()
            setPage()
            $(".txy").css("background-color","#e9ecef")
        });        
    });

    $(".rc_select").click(function(){
        rcSelection = $(this).val();
        fnSaveReasonCode(rcSelection)
    });

    function fnSaveReasonCode(new_reason_code){
        $.post("api.php",{
            act:        "save_AssetFieldValue",
            ass_id:     data["asset"]["ass_id"],
            fieldName:  "res_reason_code",
            fieldValue: new_reason_code
        },
        function(confirmedFV, status){
            // console.log("new_reason_code:"+new_reason_code)
            // console.log("confirmedFV:"+confirmedFV)
            if(new_reason_code==confirmedFV){
                // console.log("Saved successfully")
                data["asset"]["res_reason_code"] = new_reason_code
                $(".txy").css("background-color","white")
                setPage()
            }
        });
    }

    function fnAssessColor(){

    }



    function setPage(){
        $(".rcCat").hide();
        $(".btnCancel").hide();
        $(".btnClear").hide();
        $("#areaRCs").hide();
        $("#areaInputs").hide();
        $(".rc_option").hide();
        $("#res_reason_code").text("");
        let res_reason_code = data["asset"]["res_reason_code"];
        console.log("Set page reason code:"+res_reason_code)
        if(res_reason_code){// Asset is finished!
            $("#res_reason_code").text(res_reason_code+" - "+tempData["arrRC"][res_reason_code]);
            $(".btnClear").show();
            $("#areaInputs").show();
            $(".txy").prop('disabled', false);
            fnAssessColor()
        }else if(tempData["tempReasonCat"]=="notfound"){console.log("Select a not found reason code")
            $(".btnCancel").show();
            $("#areaRCs").show();
            $(".rc_sectionNF").show();
        }else if(tempData["tempReasonCat"]=="error"){console.log("Select an error reason code")
            $(".btnCancel").show();
            $("#areaRCs").show();
            $(".rc_sectionERR").show();
        }else{console.log("This asset has not been assessed")
            $(".txy").prop('disabled', true);
            // $(".txy").css("background-color","#e9ecef")
            $(".rcCat").show();
            $("#areaInputs").show();
        }
    }

    function fnRunValidation(changedFV, validation){
        let res = [];
        if(validation=="string"){
            res["test"] = /([a-z0-9])$/.test(changedFV)
            res["note"] = "Must only contain alphanumeric characters up to 250 long";
        }else if(validation=="date"){
            res["test"] = /([a-z0-9])$/.test(changedFV)
            res["note"] = "Must only contain a date";
        }else if(validation=="number"){
            res["test"] = /([0-9])$/.test(changedFV)
            res["note"] = "Must only contain numeric characters";
        }else if(validation=="text"){
            res["test"] = /([a-z0-9])$/.test(changedFV)
            res["note"] = "Must only contain alphanumeric characters up to 3000 long";
        }
        console.log(res)
        return res;
    }

    fnInitialSetup()
    setPage(data)

});
</script>

<style>
.hdz{
    display:none
}
</style>


<br><br>

<div class='container-fluid' id="asset_page">
	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><span class='assStatus'></span></nav></div>
		<div class='col-10'>
            <h2>
                Asset:<span class='tf' data-name='Asset'></span>-<span class='tf' data-name='Subnumber'></span>: 
                <span class='tf' data-name='AssetDesc1'></span> (<span class='tf' data-name='AssetDesc2'></span>)
            </h2>
            <p><span class='tf' id='res_reason_code' data-name='res_reason_code'></p>
		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'><nav class='nav flex-column'><span class='assStatus'></span></nav></div>
	</div>

	<div class='row'>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar'  >
			<nav class='nav flex-column'>
                <span class='btnTrough'>
                    <button type='button' value='ND10'   class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>Edit</button><br>
                    <button type='button' value='notfound'  class='rcCat nav-link btn btn-warning hdz'>Not<br>found</button><br>
                    <button type='button' value='error'     class='rcCat nav-link btn btn-primary hdz'>Asset<br>Error</button><br>
                    <div class='dropdown btnClear hdz'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Clear</button>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <button type='button' class='dropdown-item bg-danger text-light btnClearSure'>I'm sure</button>
                        </div>
                    </div>
                    <button type='button' class='btn btn-danger btnCancel hdz'>Cancel</button>
                </span>
            </nav>
<!-- $btn_deleteff $btn_camera -->

                


		</div>
		<div class='col-10'>
            <div id="areaRCs"></div>

			<span id='areaInputs'>
				<div class='row'>
					<div class='col-4'>
                        <div class="form-group"><label>Asset Description</label>
                            <input type="text" class="form-control txy" data-name="AssetDesc1" data-vld="string">
                        </div>
                        <div class="form-group"><label>Asset Description 2</label>
                            <input type="text" class="form-control txy" data-name="AssetDesc2" data-vld="string">
                        </div>
						<div class="form-group"><label>Asset Main No Text</label>
                            <input type="text" class="form-control txy" data-name="AssetMainNoText" data-vld="string">
                        </div>
						<div class="form-group"><label>Inventory</label>
                            <input type="text" class="form-control txy" data-name="Inventory" data-vld="string">
                        </div>
						<div class="form-group"><label>InventNo</label>
                            <input type="text" class="form-control txy" data-name="InventNo" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Serial No</label>
                            <input type="text" class="form-control txy" data-name="SNo" data-vld="string">
                        </div>
						<div class="form-group"><label>Location</label>
                            <input type="text" class="form-control txy" data-name="Location" data-vld="string">
                        </div>
						<div class="form-group"><label>Level/Room</label>
                            <input type="text" class="form-control txy" data-name="Room" data-vld="string">
                        </div>
						<div class="form-group"><label>State</label>
                            <input type="text" class="form-control txy" data-name="State" data-vld="string">
                        </div>
						<div class="form-group"><label>latitude</label>
                            <input type="text" class="form-control txy" data-name="latitude" data-vld="string">
                        </div>
						<div class="form-group"><label>longitude</label>
                            <input type="text" class="form-control txy" data-name="longitude" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Class</label>
                            <input type="text" class="form-control txy" data-name="Class" data-vld="string">
                        </div>
						<div class="form-group"><label>accNo</label>
                            <input type="text" class="form-control txy" data-name="accNo" data-vld="string">
                        </div>
						<div class="form-group"><label>CapDate</label>
                            <input type="text" class="form-control txy" data-name="CapDate" data-vld="string">
                        </div>
						<div class="form-group"><label>LastInv (YYYY-MM-DD)</label>
                            <input type="text" class="form-control txy" data-name="LastInv" data-vld="string">
                        </div>
						<div class="form-group"><label>DeactDate</label>
                            <input type="text" class="form-control txy" data-name="DeactDate" data-vld="string">
                        </div>
						<div class="form-group"><label>PlRetDate</label>
                            <input type="text" class="form-control txy" data-name="PlRetDate" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>Quantity</label>
                            <input type="text" class="form-control txy" data-name="Quantity" data-vld="string">
                        </div>
						<div class="form-group"><label>CurrentNBV</label>
                            <input type="text" class="form-control txy" data-name="CurrentNBV" data-vld="string">
                        </div>
						<div class="form-group"><label>AcqValue</label>
                            <input type="text" class="form-control txy" data-name="AcqValue" data-vld="string">
                        </div>
						<div class="form-group"><label>OrigValue</label>
                            <input type="text" class="form-control txy" data-name="OrigValue" data-vld="string">
                        </div>
						<div class="form-group"><label>ScrapVal</label>
                            <input type="text" class="form-control txy" data-name="ScrapVal" data-vld="string">
                        </div>
						<div class="form-group"><label>ValMethod</label>
                            <input type="text" class="form-control txy" data-name="ValMethod" data-vld="string">
                        </div>
					</div>
					<div class='col-2'>
						<div class="form-group"><label>CostCtr</label>
                            <input type="text" class="form-control txy" data-name="CostCtr" data-vld="string">
                        </div>
						<div class="form-group"><label>WBSElem</label>
                            <input type="text" class="form-control txy" data-name="WBSElem" data-vld="string">
                        </div>
						<div class="form-group"><label>Fund</label>
                            <input type="text" class="form-control txy" data-name="Fund" data-vld="string">
                        </div>
						<div class="form-group"><label>RspCCtr</label>
                            <input type="text" class="form-control txy" data-name="RspCCtr" data-vld="string">
                        </div>
						<div class="form-group"><label>RevOdep</label>
                            <input type="text" class="form-control txy" data-name="RevOdep" data-vld="string">
                        </div>
						<?=$btn_copy?>
					</div>
				</div>

				<?=$img_list?>
				

				<div class='row'>
					<div class='col-12'>
						<div class="form-group"><h2>Comments</h2>
							<!-- <input type="text"> -->
							<textarea v-model="ar.res_comment" class= "form-control" v-on:keyup="sync_data('comment')" rows='5'></textarea>
						</div>
					</div>
				</div>
			</span>






			<div class="modal fade" id="modal_show_pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Photo</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">  
					<?=$images?> 
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
			      </div>
			    </div>
			  </div>
			</div>


			<form action='05_action.php' method='get'>
			<div class="modal fade" id="modal_copy" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Copy this asset</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">  
					<p class="lead">
						You can copy this asset to a brand new asset, it will copy every aspect of this asset as you've entered it. This is only available for first founds.
						<br>How many assets would you like to create?
						<br>

								<select name="stkm_id" class="form-control">
									<?=$listTemplates?>
								</select>
						      	<!-- <input type="text" name="duplicate_count" class="form-control"> -->
						      	<input type="hidden" name="ass_id" value="<?=$ass_id?>">
						      	<!-- <input type="hidden" name="act" value="save_copy_asset"> -->
						      	<input type="hidden" name="act" value="save_add_to_template">
					</p>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
	        		<input type="submit" class="btn btn-primary" value='Copy'>
			      </div>
			    </div>
			  </div>
			</div>
			</form>
<!-- 
DPN export is called json_update
Add history
Add fix me portal - inherent in the install file - what about delete db?
Add merge
Add user login
Auto answer impairment questions on asset error

The DPN upload process is working, but it isn't doing the supernumery things to clean up and finalise the upload
 -->



		</div>
		<div class='col-12 col-md-1 col-xl-1 bd-sidebar text-right'  >
			<nav class='nav flex-column'>
                <span class='btnTrough'>
                    <button type='button' value='ND10'   class='rcCat nav-link btn hdz' style='background-color:#78e090!important;display:none'>Sighted<br>Edit</button><br>
                    <button type='button' value='notfound'  class='rcCat nav-link btn btn-warning hdz'>Not<br>found</button><br>
                    <button type='button' value='error'     class='rcCat nav-link btn btn-primary hdz'>Asset<br>Error</button><br>
                    <div class='dropdown btnClear hdz'>
                        <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Clear</button>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <button type='button' class='dropdown-item bg-danger text-light btnClearSure'>I'm sure</button>
                        </div>
                    </div>
                    <button type='button' class='btn btn-danger btnCancel hdz'>Cancel</button>
                </span>
            </nav>
		</div>
	    











	</div><!-- End main page row -->



<br><br><br><br><br><br><br><br><br><br><br>



</div>

<br><br><br>
<script>
</script>





<?php include "04_footer.php"; ?>




