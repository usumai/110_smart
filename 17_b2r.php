<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php include "components/forminput.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <div class='row'>
            <div class='col'>
                <h1 class='display-4'>Bin to Register: {{ BIN_CODE }}</h1>
            </div>
        </div>

        <div class='row'>
            <div class='col-3 lead' id='menuleft'>
                <ul class="list-group list-group-flush text-center">
                    <div class='text-center' v-if="json_skeleton.findingID">
                        <div class='dropdown'>
                            <button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button>
                            <div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'>
                                <button class='dropdown-item bg-danger text-light' v-on:click="save_b2r_result(0)">I'm sure</button>
                            </div>
                        </div>
                        <br><br><br>
                    </div>

                    <button class="list-group-item list-group-item-action list-group-item-secondary" v-on:click="save_b2r_extra()" v-if="json_skeleton.findingID&&json_skeleton.findingID!=14">Register extra stockcode</button>
                    <li class="list-group-item"  v-if="!json_skeleton.findingID"><b>Are there any stockcodes in addition to this list?</b></li>
                    <button class="list-group-item list-group-item-action list-group-item-success" v-on:click="save_b2r_result(14)" v-if="!json_skeleton.findingID">No</button>
                    <button class="list-group-item list-group-item-action list-group-item-danger" v-on:click="save_b2r_result(15)" v-if="!json_skeleton.findingID">Yes</button>
                </ul>
            </div>

            <div class='col-6 lead'>

                <form action='05_action.php' method='POST'>
                    <table class='table table-sm'>
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
		                	<table class="table table-sm table-striped">
		                        <caption>
	                                <b>Bin contents</b>
	                                <small>(Not all items listed must be sighted, but all additional stockcodes found must be registered.)</small>		                        
		                        </caption>
		                        <thead class="table-dark">
			                        <tr>
			                            <th>Stockcode</th>
			                            <th>Name</th>
<!-- 		                            <th>SOH</th> -->
			                        </tr>
		                        </thead>
		                        <tbody>
			                        <tr v-for="bin in json_bins_orig">
			                            <td>{{ bin.STOCK_CODE }}</td>
			                            <td>{{ bin.ITEM_NAME }}</td>
<!-- 			                        <td>{{ bin.SOH }}</td> 	-->
			                        </tr>
		                        </tbody>
							</table>
                        </tr>
                    </table>

                </form>
            </div>

            <div class='col-3 lead' id='menuleft'>
                <ul class="list-group list-group-flush text-center">
                    <div class='text-center' v-if="json_skeleton.findingID">
                        <div class='dropdown'>
                            <button class='btn btn-outline-danger dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='dispBtnClear'>Delete</button>
                            <div class='dropdown-menu bg-danger' aria-labelledby='dropdownMenuButton'>
                                <button class='dropdown-item bg-danger text-light' v-on:click="save_b2r_result(0)">I'm sure</button>
                            </div>
                        </div>
                        <br><br><br>
                    </div>

                    <button class="list-group-item list-group-item-action list-group-item-secondary" v-on:click="save_b2r_extra()" v-if="json_skeleton.findingID&&json_skeleton.findingID!=14">Register extra stockcode</button>
                    <li class="list-group-item"  v-if="!json_skeleton.findingID"><b>Are there any stockcodes in addition to this list?</b></li>
                    <button class="list-group-item list-group-item-action list-group-item-success" v-on:click="save_b2r_result(14)" v-if="!json_skeleton.findingID">No</button>
                    <button class="list-group-item list-group-item-action list-group-item-danger" v-on:click="save_b2r_result(15)" v-if="!json_skeleton.findingID">Yes</button>
                </ul>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col">
        <div class="col table-responsive-sm">             
        <table class="table table-sm table-striped table-hover ">
            <caption style="caption-side: top"><h3><b>Extra</b></h3></caption>
            <thead class="table-dark">
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
                    <td width='10%'>
                        <textinput :primary_key='bin.auto_storageID' 
                                    primary_key_name="auto_storageID" 
                                    db_name='smartdb' table_name='sm18_impairment' 
                                    column_name='STOCK_CODE' 
                                    :bound_value='bin.STOCK_CODE'
                                    :disabled='false'
                                    maxlen='255'
                                    ></textinput>
                    </td>
                    <td>
                        <textinput :primary_key='bin.auto_storageID' 
                                    primary_key_name="auto_storageID" 
                                    db_name='smartdb' table_name='sm18_impairment' 
                                    column_name='ITEM_NAME' 
                                    :bound_value='bin.ITEM_NAME'
                                    :disabled='false'
                                    maxlen='255'
                                    ></textinput>
                    </td>
                    <td width='10%'>
                        <textinput :primary_key='bin.auto_storageID' 
                                    primary_key_name="auto_storageID" 
                                    db_name='smartdb' table_name='sm18_impairment' 
                                    column_name='SOH' 
                                    :bound_value='bin.SOH'
                                    :disabled='false'
                                    maxlen='255'
                                    ></textinput>
                    </td>
                    <td>
                        <textinput :primary_key='bin.auto_storageID' 
                                    primary_key_name="auto_storageID" 
                                    db_name='smartdb' table_name='sm18_impairment' 
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
                        <button class="btn btn-danger" v-on:click="save_delete_b2r_extra(bin.auto_storageID)" >Delete</button>
                    </td>

                </tr>
            </tbody>
        </table>
        </div>

        </div>
    </div>
<!--
    <div v-if="dev"><hr>
        <h1 class="display-4">Developer data</h1>
        <div class="row">
            <div class="col-3">json_bins_orig<pre>{{ json_bins_orig }}</pre></div>
            <div class="col-3">json_skeleton<pre>{{ json_skeleton }}</pre></div>
            <div class="col-3">json_result_cats<pre>{{ json_result_cats }}</pre></div>
            <div class="col-3">json_bins_extr<pre>{{ json_bins_extr }}</pre></div>
        </div>
    </div>
-->
</div>

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
    },
    created() {
        this.refresh_page()
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
            payload             = {'act':'save_b2r_result', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id, findingID}
            json = fnapi(payload)
            this.refresh_page()
        }, 
        save_b2r_extra(){
            payload             = {'act':'save_b2r_extra', 'BIN_CODE':this.BIN_CODE, 'stkm_id':this.stkm_id}
            json = fnapi(payload)
            this.refresh_page()
        }, 
        save_delete_b2r_extra(auto_storageID){
            payload             = {'act':'save_delete_b2r_extra', auto_storageID}
            json = fnapi(payload)
            this.refresh_page()
        }, 
    }
})
</script>

<?php include "04_footer.php"; ?>


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