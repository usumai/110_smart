<?php 
include "02_header.php"; 
include "components/forminput.php";
?>

<div id="app" class='container-fluid '>

    <h1 class='display-4'>Bin to Register: {{ BIN_CODE }}</h1>


    <div class='row'>
          <div class='col-2 lead' id='menuleft'>
              <ul class="list-group list-group-flush">
                  <div class='text-center' v-if="json_skeleton.findingID">
                      <div class='dropdown'>
                          <button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button>
                          <div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'>
                              <button class='dropdown-item bg-danger text-light' @click="save_b2r_result(0)">I'm sure</button>
                          </div>
                      </div>
                      <br><br><br>
                  </div>
                  <button 
                  	class="list-group-item list-group-item-action list-group-item-secondary" 
                  	@click="save_b2r_extra()" 
                  	v-if="json_skeleton.findingID&&json_skeleton.findingID!=14"
                  	data-toggle="modal" 
                  	data-target="#create_extra_dlg">Register extra stockcode</button>                   	
                  <div class="tx-info"  v-if="!json_skeleton.findingID">Are there any stockcodes in addition to this list?</div>
                  <button class="list-group-item list-group-item-action list-group-item-success text-center" @click="save_b2r_result(14)" v-if="!json_skeleton.findingID">No</button>
                  <button class="list-group-item list-group-item-action list-group-item-danger text-center" @click="save_b2r_result(15)" v-if="!json_skeleton.findingID">Yes</button>
              </ul>
          </div>

          <div class='col-8 lead'>

              <form action='05_action.php' method='POST'>
                  <table class="table">
                      <tr>
                          <td><strong>Status</strong></td>
                          <td colspan="4">
                              <div v-if="json_skeleton.findingID">
                                  {{ json_skeleton.findingID }}:
                                  <span class="badge" :class="{'badge-success':json_result_cats[json_skeleton.findingID].color=='success'}">{{ json_result_cats[json_skeleton.findingID].resAbbr }}</span>
                                  {{ json_result_cats[json_skeleton.findingID].findingName }}
                              </div>
                          </td>
                      </tr>
                      <tr><td><b>District</b></td><td colspan='2' >{{ json_skeleton.DSTRCT_CODE }}</td><td></td></tr>
                      <tr><td><b>Warehouse</b></td><td colspan='2' >{{ json_skeleton.WHOUSE_ID }}</td><td></td></tr>
                      <tr><td><b>Bin</b></td><td colspan='2' >{{ BIN_CODE }}</td><td></td></tr>
                      <tr><td colspan='4' >&nbsp;</td></tr>
	
	                  <tr>
		                   <td colspan="4">
		                        <b>Bin contents</b><br/>
		                        <div class="tx-note">Not all items listed must be sighted, but all additional stockcodes found must be registered.<br></div>		                        
		                   	
				             	<ul class="nav nav-tabs">
				             		<li class="nav-item">
				             			<a class="nav-link active" data-toggle="tab" href="#tab0">Unsighted <i style="font-size: 0.8em">({{this.countIncomplete()}})</i></a>
				             		</li>
				             		<li class="nav-item">
				             			<a class="nav-link" data-toggle="tab" href="#tab1">Sighted <i style="font-size: 0.8em">({{this.countComplete()}})</i></a>
				             		</li>
		
				             	</ul>         	
					            <div class="tab-content">         
			                    	<div class="tab-pane fade active show" id="tab0">
					                	<table id="bin_contents" class="table table-striped">
					                        <thead class="table-dark sticky-top">
		
						                            <th style="width: 10%">Stockcode</th>
						                            <th style="width: 80%">Name</th>
			 		                            	<th style="width: 10%">Sighted</th> 
		
					                        </thead>
					                        <tbody>
						                        <tr v-for="asset in json_bins_orig" v-if="(asset.isType=='b2r' || asset.isType=='b2r_exc') &&  asset.SIGHTED!=1" :style="asset.isType=='b2r_exc'? 'color: red': '' ">
						                            <td style="width: 10%">{{ asset.STOCK_CODE }}</td>
						                            <td style="width: 80%">{{ asset.ITEM_NAME }}</td>
						                        	<td style="width: 10%;text-align: center">
						                        		<a 
						                        			:href="'05_action.php?act=save_is_toggle_check&toggle='+(asset.SIGHTED==1?0:1)+'&STOCK_CODE='+asset.STOCK_CODE+'&BIN_CODE='+BIN_CODE.replace('&','%26')+'&stkm_id='+stkm_id"	                        	
						                        			class="btn btn-outline">
						                        			<i :class="'fa' + (asset.SIGHTED==1 ? ' fa-check text-success':' fa-times text-danger')">{{(asset.SIGHTED==1 ? ' ':'')}}</i>
						                        		</a>
						                        	</td> 
						                        </tr>
					                        </tbody>
										</table>
		                        	</div>
			                    	<div class="tab-pane" id="tab1">
					                	<table id="bin_contents1" class="table table-striped">		                	
					                        <thead class="table-dark sticky-top">
		
						                            <th style="width: 10%">Stockcode</th>
						                            <th style="width: 80%">Name</th>
			 		                            	<th style="width: 10%">Sighted</th> 
		
					                        </thead>
					                        <tbody>
						                        <tr v-for="asset in json_bins_orig" v-if="(asset.isType=='b2r' || asset.isType=='b2r_exc') && asset.SIGHTED==1" :style="asset.isType=='b2r_exc'? 'color: red': ''">
						                            <td style="width: 10%">{{ asset.STOCK_CODE }}</td>
						                            <td style="width: 80%">{{ asset.ITEM_NAME }}</td>
						                        	<td style="width: 10%;text-align: center">
						                        		<a 
						                        			:href="'05_action.php?act=save_is_toggle_check&toggle='+(asset.SIGHTED==1?0:1)+'&STOCK_CODE='+asset.STOCK_CODE+'&BIN_CODE='+BIN_CODE.replace('&','%26')+'&stkm_id='+stkm_id"	                        	
						                        			class="btn btn-outline">
						                        			<i :class="'fa' + (asset.SIGHTED==1 ? ' fa-check text-success':' fa-times text-danger')">{{(asset.SIGHTED==1 ? ' ':'')}}</i>
						                        		</a>
						                        	</td> 
						                        </tr>
					                        </tbody>
										</table>
		                        	</div>   		                        		                        	
				                </div>
		                      	</td>
                      </tr>
                  </table>

              </form>
          </div>

          <div class='col-2 lead' id='menuleft'>
              <ul class="list-group list-group-flush">
                  <div class='text-center' v-if="json_skeleton.findingID">
                      <div class='dropdown'>
                          <button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button>
                          <div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'>
                              <button class='dropdown-item bg-danger text-light' @click="save_b2r_result(0)">I'm sure</button>
                          </div>
                      </div>
                      <br><br><br>
                  </div>

                  <button 
                  	class="list-group-item list-group-item-action list-group-item-secondary" 
                  	@click="save_b2r_extra()" 
                  	v-if="json_skeleton.findingID&&json_skeleton.findingID!=14"
                  	data-toggle="modal" 
                  	data-target="#create_extra_dlg">Register extra stockcode</button> 
                  <div class="tx-info"   v-if="!json_skeleton.findingID">Are there any stockcodes in addition to this list?</div>
                  <button class="list-group-item list-group-item-action list-group-item-success" @click="save_b2r_result(14)" v-if="!json_skeleton.findingID">No</button>
                  <button class="list-group-item list-group-item-action list-group-item-danger" @click="save_b2r_result(15)" v-if="!json_skeleton.findingID">Yes</button>
              </ul>
          </div>

      </div>
   
    <div class="row">
        
        <div class="col ">          
	           
	        <table class="table table-striped table-hover">
	            <caption style="caption-side: top"><h3><b>Extra</b></h3></caption>
	            <thead class="table-dark sticky-top">
	                <tr>
	                    <th>Stockcode</th>
	                    <th>Name</th>
	                    <th>SOH</th>
	                    <th>Comment</th>
	                    <th class='text-right'>Status</th>
	                    <th class='text-right'>Action</th>
	
	                </tr>
	            </thead>
	            <tbody>    
	                <tr v-for="bin in json_bins_extr">
	                    <td width='15%'>
	                        <textinput :primary_key='bin.auto_storageID' 
	                                    primary_key_name="auto_storageID" 
	                                    db_name='smartdb' 
	                                    table_name='sm18_impairment' 
	                                    column_name='STOCK_CODE' 
	                                    :bound_value='bin.STOCK_CODE'
	                                    :disabled='false'
	                                    maxlen='255'
	                                    ></textinput>
	                    </td>
	                    <td>
	                        <textinput :primary_key='bin.auto_storageID' 
	                                    primary_key_name="auto_storageID" 
	                                    db_name='smartdb' 
	                                    table_name='sm18_impairment' 
	                                    column_name='ITEM_NAME' 
	                                    :bound_value='bin.ITEM_NAME'
	                                    :disabled='false'
	                                    maxlen='255'
	                                    ></textinput>
	                    </td>
	                    <td width='10%'>
	                        <textinput :primary_key='bin.auto_storageID' 
	                                    primary_key_name="auto_storageID" 
	                                    db_name='smartdb' 
	                                    table_name='sm18_impairment' 
	                                    column_name='SOH' 
	                                    :bound_value='bin.SOH'
	                                    :disabled='false'
	                                    maxlen='255'
	                                    ></textinput>
	                    </td>
	                    <td>
	                        <textinput :primary_key='bin.auto_storageID' 
	                                    primary_key_name="auto_storageID" 
	                                    db_name='smartdb' 
	                                    table_name='sm18_impairment' 
	                                    column_name='res_comment' 
	                                    :bound_value='bin.res_comment'
	                                    :disabled='false'
	                                    maxlen='255'
	                                    inputtype='textarea'
	                                    ></textinput>
	                    </td>
	                    <td width='10%'>
	                        <a class='btn btn-outline-dark float-right' :href="'18_b2r_extra.php?current_row='+current_row+'&auto_storageID='+bin.auto_storageID" v-if="bin.finalResult">{{ bin.finalResult }}</a>
	                        <a class='btn btn-outline-danger float-right' :href="'18_b2r_extra.php?current_row='+current_row+'&auto_storageID='+bin.auto_storageID"  v-if="!bin.finalResult">Incomplete</a>
	                    </td>
	                    <td width='10%' class="text-right">
	                        <button class="btn btn-danger" @click="save_delete_b2r_extra(bin.auto_storageID)" >Delete</button>
	                    </td>
	
	                </tr>
	            </tbody>
	        </table>
	        
        </div>

        
    </div>

	
	<div class="modal fade" id="create_extra_dlg" role="dialog" aria-labelledby="modal_selectsiteLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title"><h4>New Extra Stockcode</h4></div>
				</div>
				<div class="modal-body table-responsive">				   
					<table class="table table-striped table-hover ">
						<thead class="table-dark">
							<tr>
								<th>Stockcode</th>
								<th>Name</th>
								<th>SOH</th>
								<th>Comment</th>
								<th class='text-right'>Status</th>			
							</tr>
						</thead>
						<tbody>    
							<tr>
								<td width='10%'>
									<textinput :primary_key='extra_item.auto_storageID' 
												primary_key_name="auto_storageID" 
												db_name='smartdb' 
												table_name='sm18_impairment' 
												column_name='STOCK_CODE' 
												:bound_value='extra_item.STOCK_CODE'
												:disabled='false'
												:validate='checkStockCode'
												validate_msg='Stock code already exist'
												validate_level='warning'
												maxlen='255'>
									</textinput>
									<span v-if="this.extra_item.warning">Stock code already exist</span>
								</td>
								<td>
									<textinput :primary_key='extra_item.auto_storageID' 
												primary_key_name="auto_storageID" 
												db_name='smartdb' 
												table_name='sm18_impairment' 
												column_name='ITEM_NAME' 
												:bound_value='extra_item.ITEM_NAME'
												:disabled='false'
												maxlen='255'>
									</textinput>
								</td>
								<td width='10%'>
									<textinput :primary_key='extra_item.auto_storageID' 
												primary_key_name="auto_storageID" 
												db_name='smartdb' t
												table_name='sm18_impairment' 
												column_name='SOH' 
												:bound_value='extra_item.SOH'
												:disabled='false'
												maxlen='255'>
									</textinput>
								</td>
								<td>
									<textinput :primary_key='extra_item.auto_storageID' 
												primary_key_name="auto_storageID" 
												db_name='smartdb' 
												table_name='sm18_impairment' 
												column_name='res_comment' 
												:bound_value='extra_item.res_comment'
												:disabled='false'
												maxlen='255'
												inputtype='textarea'>
									</textinput>
								</td>
								<td width='10%'>
									<a class='btn btn-outline-danger float-right' :href="'18_b2r_extra.php?current_row='+current_row+'&auto_storageID='+extra_item.auto_storageID">Incomplete</a>
								</td>
			
							</tr>
						</tbody>
					</table>
					

	


			                			
				</div>
				<div class="modal-footer">
					<button type="button" 
						class="btn btn-secondary foat-right" 
						@click="refresh_page()" 
						data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script src='includes/datatables/jquery.dataTables.min.js'></script>
<script>

 


function fnapi(data){
    payload_res = $.ajax({
        type: "POST",
        url: "api.php",
        dataType: "json",
        data,
        async:false,
    }).responseText;
    payload_res = IsJsonString(payload_res) ? JSON.parse(payload_res) : "Non-valid json was returned"+payload_res;
    return payload_res;
}

let vm = new Vue({
    el: '#app',
    data: {
        dev:true,
        BIN_CODE:"<?=$_GET["BIN_CODE"]?>",
        stkm_id:"<?=$_GET["stkm_id"]?>",
        current_row: <?=$current_row?>,
        json_bins_orig:{},
        json_bins_extr:{},
        json_skeleton:{},
        json_result_cats:{},
        extra_item: {
        	auto_storageID: '',
        	STOCK_CODE: '',
        	ITEM_NAME:'',
        	SOH: '',
        	res_comment:'',
        	warning: false
        }
        //updateList: 0
    },
    created() {
    	 
        this.refresh_page()
    },
    mounted(){
    	$('#bin_contents').DataTable({
    	    stateSave: true,
    	    paging: false,
    	    searching: false,
    	    info: false,
    	    order: [],
    	    columnDefs: [
        	    {targets: 0, orderable: true},
        	    {targets: 1, orderable: true},
        	    {targets: 2, orderable: true}
        	]
    	});   

    	$('#bin_contents1').DataTable({
    	    stateSave: true,
    	    paging: false,
    	    searching: false,
    	    info: false,
    	    order: [],
    	    columnDefs: [
        	    {targets: 0, orderable: true},
        	    {targets: 1, orderable: true},
        	    {targets: 2, orderable: true}
        	]
    	});        	
    },
    
    updated(){
    	/*
    	if(this.updateList==1){

	        $('#bin_contents').DataTable({
	            stateSave: true
	        });
	        this.updateList=0;
    	}
    	*/
    },
    methods:{
        refresh_page(){
            this.get_b2r_skeleton();
            this.get_b2r_contents();
            this.get_b2r_extras();
            this.get_result_cats();
        }, 
        
        get_b2r_skeleton(){
            payload             = {'act':'get_b2r_skeleton', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id}
            json                = fnapi(payload)
            this.json_skeleton  = json[0];        
        }, 
        get_b2r_contents(){
        	//this.updateList=1;
            payload             = {'act':'get_b2r_contents', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id}
            this.json_bins_orig = fnapi(payload)
        }, 
        get_b2r_extras(){
            payload             = {'act':'get_b2r_extras', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id}
            this.json_bins_extr = fnapi(payload)
        }, 
        get_result_cats(){
            payload              = {act:'get_result_cats', isType: 'b2r'}
            json                = fnapi(payload)
            for(let result_cat_idx in json){
                result_cat = json[result_cat_idx]
                this.json_result_cats[result_cat.findingID] = result_cat
            }
        }, 
        save_b2r_result(findingID){
            payload             = {'act':'save_b2r_result', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id, findingID};
            json = fnapi(payload);
            this.refresh_page();
        }, 
        save_b2r_extra(){
            payload             = {'act':'save_b2r_extra', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id};
            this.extra_item = fnapi(payload);            
        }, 
        save_delete_b2r_extra(auto_storageID){
            payload             = {'act':'save_delete_b2r_extra', auto_storageID};
            json = fnapi(payload);
            this.refresh_page();
        }, 
        countComplete(){
            var r=0;
            this.json_bins_orig.forEach((v,i)=>{v.isType=='b2r' && v.SIGHTED==1?r++:0});
            return r;
        },
        countIncomplete(){
            var r=0;
            this.json_bins_orig.forEach((v,i)=>{v.isType=='b2r' && v.SIGHTED!=1?r++:0});
            return r;            
        },
        countExclude(){
            var r=0;
            this.json_bins_orig.forEach((v,i)=>{v.isType=='b2r_exc'?r++:0});
            return r;            
        },
        checkStockCode(stockCode){
            for (var i in this.json_bins_orig){
                var item=this.json_bins_orig[i];
                if(item.STOCK_CODE==stockCode){
                    return false;
                }
            }
            return true;
        }        
    }
})
</script>




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

