<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php include "components/forminput.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-4">
            Asset:{{ assd.res_asset_id }} 
            <span class='text-danger' v-if='assd.delete_date'>DELETED</span>
            <span class='' v-if="assd.genesis_cat=='ga_template'">TEMPLATE</span>
        </h1>
        <div class='row' v-if="!subselector">
            <div class='col-12 col-md-1 col-xl-1 bd-sidebar'  >
                <nav class='nav flex-column'>
                    <span v-if='!assd.delete_date'>
                        <div class="list-group list-group-flush">
                            <button v-on:click="subselector='SAVON'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning" 
                                    type="button" >Change<br>reason code</button>
                            <button v-on:click="select_rc('ND10')"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>Edit</button>
                            <button v-on:click="select_rc('ND10',true)"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>No Edit</button>
                            <button v-on:click="subselector='SAVOFF'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning mt-3" 
                                    type="button" >Not<br>found</button>
                            <button v-on:click="subselector='ND'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-primary mt-3" 
                                    type="button" >Found<br>other</button>
                            <button v-on:click="subselector='RFC'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Remove<br>from count</button>
                            <button v-on:click="subselector='PRERESOLVE'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Preresolve</button>

                            <button v-on:click="show_clear_rc_options=true"
                                    v-if="assd.res_reason_code&&!show_clear_rc_options&&assd.genesis_cat!='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Clear<br>RC</button>
                            <button v-on:click="show_clear_rc_options=false"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel</button>
                            <button v-on:click="select_rc('')"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure<br>Clear</button>

                            <button v-on:click="show_delete_options=true"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Delete</button>
                            <button v-on:click="delete_stk_asset('delete')"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure</button>
                            <button v-on:click="show_delete_options=false"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel delete</button>
                            <a      :href="'13_camera.php?ass_id='+ass_id"
                                    v-if="assd.res_reason_code"
                                    class="text-center list-group-item list-group-item-action list-group-item-dark mt-3" 
                                    type="button"><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
                        </div>
                    </span>
                    
                    <button v-on:click="delete_stk_asset('undelete')"
                        v-if="assd.delete_date"
                        class="text-center list-group-item list-group-item-action list-group-item-danger" 
                        type="button" >Undelete</button>
                </nav>

                    


            </div>
            <div class='col-10'>

                <span id='areaInputs'>
                    <div class='row'>
                        <div class='col-6'>
                            <div class="form-group"><label>Reason Code</label>
                                <input class='form-control' type='text' v-model='assd.res_reason_code' disabled>
                            </div>
                            <div class="form-group"><label>Asset Description</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assetdesc1' 
                                :bound_value='assd.res_assetdesc1'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Asset Description 2</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assetdesc2' 
                                :bound_value='assd.res_assetdesc2'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Asset Main No Text</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assettext' 
                                :bound_value='assd.res_assettext'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Inventory</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_inventory' 
                                :bound_value='assd.res_inventory'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>InventNo</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_inventno' 
                                :bound_value='assd.res_inventno'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Serial No</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_serialno' 
                                :bound_value='assd.res_serialno'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                        </div>
                        <div class='col-3'>
                            <div class="form-group"><label>Location</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_location' 
                                :bound_value='assd.res_loc_location'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Level/Room</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_room' 
                                :bound_value='assd.res_loc_room'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Latitude</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_latitude' 
                                :bound_value='assd.res_latitude'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Longitude</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_longitude' 
                                :bound_value='assd.res_longitude'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>GrpCustod</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_grpcustod' 
                                :bound_value='assd.res_grpcustod'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Quantity</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_quantity' 
                                :bound_value='assd.res_quantity'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                        </div>
                        <div class='col-3'>
                            <div class="form-group"><label>Class</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_class' 
                                :bound_value='assd.res_class'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>TypeName</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_class_ga_cat' 
                                :bound_value='assd.res_class_ga_cat'
                                :disabled='true'
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>CapDate {{assd.res_date_cap_clean}}</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_date_cap' 
                                :bound_value='assd.res_date_cap_clean'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                inputtype='date'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>LastInv {{assd.res_date_lastinv_clean}}</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_date_lastinv' 
                                :bound_value='assd.res_date_lastinv_clean'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                inputtype='date'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>CostCtr</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_ccc' 
                                :bound_value='assd.res_ccc'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>CurrentNBV</label>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_val_nbv' 
                                :bound_value='assd.res_val_nbv'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <br><button type='button' class='btn btn-outline-dark float-right' v-on:click='save_create_template'
                            v-if='!template_ass_id'>Copy as a template</button>
                            <a type='button' class='btn btn-outline-dark float-right' :href="'11_ass.php?ass_id='+template_ass_id" v-if='template_ass_id'>Template asset link</a>
                        </div>
                        <!-- <div class='col-3'>
                            <div class="form-group"><label>Manufacturer</label>
                            </div>
                            <div class="form-group"><label>DeactDate</label>
                            </div>
                            <div class="form-group"><label>PlRetDate</label>
                            </div>
                            <div class="form-group"><label>State</label>
                            </div>
                            <div class="form-group"><label>AcqValue</label>
                            </div>
                            <div class="form-group"><label>OrigValue</label>
                            </div>
                            <div class="form-group"><label>ScrapVal</label>
                            </div>
                            <div class="form-group"><label>ValMethod</label>
                            </div>
                            <div class="form-group"><label>WBSElem</label>
                            </div>
                            <div class="form-group"><label>Fund</label>
                            </div>
                            <div class="form-group"><label>RspCCtr</label>
                            </div>
                            <div class="form-group"><label>RevOdep</label>
                            </div>
                        </div> -->
                    </div>

                    
                    <div class='row'>
                        <div class='col-12'>
                            <div class='form-group'>
                                <h2>Images</h2>
                                <div id='areaImgGallery'></div>
                                <span v-for='(img, imgidx) in imgsd'>
                                    <!-- <img :src="'images/'+img"> -->
                                    <button type='button' class='btn thumb_photo' 
                                            data-toggle='modal' data-target='#modal_show_pic'
                                            v-on:click='zoom_pic=img'>
                                            <img :src="'images/'+img+'?<?=time()?>'" width='200px'>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>



                    <div class='row'>
                        <div class='col-12'>
                            <div class="form-group"><h2>Comments</h2>
                                <textinput :primary_key='ass_id' db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_comment' 
                                :bound_value='assd.res_comment'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                inputtype='textarea'
                                maxlen='255'
                                ></textinput>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            
            <div class='col-12 col-md-1 col-xl-1 bd-sidebar'  >
                <nav class='nav flex-column'>
                    <span v-if='!assd.delete_date'>
                        
                    <div class="list-group list-group-flush">
                            <button v-on:click="subselector='SAVON'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning" 
                                    type="button" >Change<br>reason code</button>
                            <button v-on:click="select_rc('ND10')"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>Edit</button>
                            <button v-on:click="select_rc('ND10',true)"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>No Edit</button>
                            <button v-on:click="subselector='SAVOFF'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning mt-3" 
                                    type="button" >Not<br>found</button>
                            <button v-on:click="subselector='ND'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-primary mt-3" 
                                    type="button" >Found<br>other</button>
                            <button v-on:click="subselector='RFC'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Remove<br>from count</button>
                            <button v-on:click="subselector='PRERESOLVE'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Preresolve</button>

                            <button v-on:click="show_clear_rc_options=true"
                                    v-if="assd.res_reason_code&&!show_clear_rc_options&&assd.genesis_cat!='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Clear<br>RC</button>
                            <button v-on:click="show_clear_rc_options=false"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel</button>
                            <button v-on:click="select_rc('')"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure<br>Clear</button>

                            <button v-on:click="show_delete_options=true"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Delete</button>
                            <button v-on:click="delete_stk_asset('delete')"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure</button>
                            <button v-on:click="show_delete_options=false"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel delete</button>
                            <a      :href="'13_camera.php?ass_id='+ass_id"
                                    v-if="assd.res_reason_code"
                                    class="text-center list-group-item list-group-item-action list-group-item-dark mt-3" 
                                    type="button"><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
                        </div>
                    </span>
                    
                    <button v-on:click="delete_stk_asset('undelete')"
                        v-if="assd.delete_date"
                        class="text-center list-group-item list-group-item-action list-group-item-danger" 
                        type="button" >Undelete</button>
                </nav>

                    


            </div>
	    </div>

        <span  v-if="subselector">
            <div class='row'>
                <div class='col'>
                    <button v-on:click="subselector=false" class="btn btn-outline-dark float-right" type="button" >Cancel</button>
                </div>
            </div>
            <hr>
            <table class='table table-sm'>
                <tr v-for="(rc, rcidx) in rcsd" v-if="assd.genesis_cat==rc.rc_origin&&subselector==rc.rc_action">
                    <td>
                        <button v-on:click="select_rc(rc.res_reason_code)"
                            v-if="assd.genesis_cat==rc.rc_origin"
                            class="text-center list-group-item list-group-item-action list-group-item-info" 
                            type="button" >{{ rc.res_reason_code }}</button>
                    </td>
                    <td>
                    <strong>{{ rc.rc_desc }}</strong>
                    {{ rc.rc_example }}
                    {{ rc.rc_long_desc }}
                </td>
                    <td>
                        <button v-on:click="select_rc(rc.res_reason_code)"
                            v-if="assd.genesis_cat==rc.rc_origin"
                            class="text-center list-group-item list-group-item-action list-group-item-info" 
                            type="button" >{{ rc.res_reason_code }}</button>
                    </td>
                </tr>
            </table>
        <span>

    </div>





    <div class="modal" id="modal_show_pic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">  
                    <img :src="'images/'+zoom_pic" width='100%'>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                </div>
            </div>
        </div>
    </div>











</div>

<script>
let vm = new Vue({
    el: '#app',
    data: {
        ass_id: '<?=$_GET["ass_id"]?>',
        subselector: '',
        zoom_pic: '',
        template_ass_id: '',
        assd:{},
        rcsd:{},
        imgsd:{},
        show_clear_rc_options:false,
        show_delete_options:false,
    },
    created() {
        this.get_stk_asset()
        this.get_stk_rcs()
        this.get_images()
    },
    methods:{
        get_stk_asset(){
            payload     = {'act':'get_stk_asset', 'ass_id':this.ass_id}
            json        = fnapi(payload)
            this.assd   = json[0]
            this.show_clear_rc_options = false
            this.show_delete_options = false
            console.log(this.assd)
            this.subselector = ''
        }, 
        get_stk_rcs(){
            payload     = {'act':'get_stk_rcs'}
            this.rcsd   = fnapi(payload)
            // console.log(this.rcsd)
        }, 
        select_rc(res_reason_code, and_leave){
            payload     = {'act':'save_stk_ass_rc', 'ass_id':this.ass_id, res_reason_code}
            json        = fnapi(payload)
            // console.log(json)
            if (and_leave){
                window.location.replace("10_stk.php")
            }else{
                this.get_stk_asset()
            }            
        }, 
        get_images(){
            payload     = {'act':'get_images', 'ass_id':this.ass_id}
            this.imgsd    = fnapi(payload)
            // console.log(this.imgsd)
        }, 
        delete_stk_asset(direction){
            payload     = {'act':'save_stk_delete_no_ass', 'ass_id':this.ass_id, direction}
            json        = fnapi(payload)
            // console.log(json)
            this.get_stk_asset()
        }, 
        save_create_template(direction){
            payload     = {'act':'save_create_template', 'ass_id':this.ass_id}
            json        = fnapi(payload)
            console.log(json[0]['ass_id'])
            this.template_ass_id = json[0]['ass_id']
            // this.get_stk_asset()
        }, 
    }
})
</script>



<script>
// $( function() {
//     let tempData = [];

//     let colGreen= "#78e090";
//     let colRed  = "#FFCDD2";
//     let colAmber= "#FFE0B2";

//     tempData["lockSettings"] = [];
//     tempData["lockSettings"] = {
//         "FF": "00000 000000 000000 000000 00000",
//         "NF": "11111 111111 111111 111111 11111",
//         "ND": "00100 111111 000000 000000 00000",
//         "AF": "00000 000000 000000 000000 00000",
//     }

//     tempData["valdStgs"] = [];
//     tempData["valdStgs"] = {
//         "default": {
//             "type":"string",
//             "maxlen":"250"
//         },
//         "CurrentNBV": {
//             "type":"number",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "AcqValue": {
//             "type":"number",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "OrigValue": {
//             "type":"number",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "ScrapVal": {
//             "type":"number",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "CapDate": {
//             "type":"date",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "LastInv": {
//             "type":"date",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "DeactDate": {
//             "type":"date",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "PlRetDate": {
//             "type":"date",
//             "maxlen":"250",
//             "maxnum":"100000000000"
//         },
//         "res_comment": {
//             "type":"text",
//             "maxlen":"2000"
//         },
//     }

//     console.log(data)
//     console.log(tempData)
//     tempData["arrRC"]=[];
//     for (let rc in data["reasoncodes"]){
//         let res_reason_code = data["reasoncodes"][rc]["res_reason_code"];
//         let rc_desc         = data["reasoncodes"][rc]["rc_desc"];
//         let rc_long_desc    = data["reasoncodes"][rc]["rc_long_desc"];
//         let rc_examples     = data["reasoncodes"][rc]["rc_examples"];
//         let rc_section      = data["reasoncodes"][rc]["rc_section"];
//         let btnRCL = "<div class='col-2'><button class='btn btn-info rc_select' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
//         let btnRCR = "<div class='col-2'><button class='btn btn-info rc_select float-right' value='"+res_reason_code+"'>"+res_reason_code+"</button></div>"
//         let rowRC  = "<div class='row rc_option rc_section"+rc_section+"'>"+btnRCL+"<div class='col-8'><b>"+rc_desc+"</b> "+rc_long_desc+" <br>Example: "+rc_examples+"</div>"+btnRCR+"</div>"
        
//         tempData["arrRC"][res_reason_code]=rc_desc;
//         $("#areaRCs").append(rowRC)
//     }
    

//     $(document).on('click', '.thumb_photo', function(){
//     // $(".thumb_photo").click(function(){
//         let filename = $(this).val();

//         let btnPD = "	<div class='dropdown'> "
//             btnPD+= "	    <button class='nav-link btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Delete</button>"
//             btnPD+= "	    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>"
//             btnPD+= "	        <button class='dropdown-item btn_delete_photo' value='"+filename+"' data-dismiss='modal' >I'm sure</a>"
//             btnPD+= "	    </div>"
//             btnPD+= "	</div>"

//         $("#imageFrame").html("<img src='images/"+filename+"' width='100%'>"+btnPD);
//     });

//     $(document).on('click', '.btn_delete_photo', function(){
//         let filename = $(this).val();
//         $.post("05_action.php",{
//             act: "save_delete_photo",
//             filename:  filename
//         },
//         function(res, status){
//             fnGetImgGallery()
//         });
//     });


//     $(".rcCat").click(function(){
//         catSelection = $(this).val();
//         noedit      = $(this).data("noedit");
//         console.log("noedit")
//         console.log(noedit)
//         if(catSelection=="ND10"){
//             data["asset"]["res_reason_code"] = "ND10"
//             fnSaveReasonCode("ND10", noedit)
//         }else{
//             tempData["tempReasonCat"] = catSelection
//             setPage()
//         }
        
//     });

//     $(".btnCancel").click(function(){
//         tempData["tempReasonCat"] = null
//         setPage(data)
//     });

//     $("#btnTemplate").click(function(){
//         $(this).hide();
//         $.post("05_action.php",{
//             act:    "save_CreateTemplateAsset",
//             ass_id: data["asset"]["ass_id"]

//         },
//         function(res, status){
//             console.log(res)
//             fnDo("get_templates","LoadTemplates",1)
//             $("#menuAdd").effect( "bounce", {times:4}, 500 );
//         }); 
        
//     });

//     $(".btnClearSure").click(function(){
//         data["asset"]["res_reason_code"]= null
//         tempData["tempReasonCat"]       = null
//         $.post("05_action.php",{
//             act:    "save_ResetAssetResults",
//             ass_id: data["asset"]["ass_id"]
//         },
//         function(res, status){
//             data = JSON.parse(res)
//             console.log(data)
//             fnInitialSetup()
//             setPage()
//             $(".txy").css("background-color","#e9ecef")
//         });        
//     });

//     $(".rc_select").click(function(){
//         rcSelection = $(this).val();
//         fnSaveReasonCode(rcSelection)
//     });

//     function fnSaveReasonCode(new_reason_code, noedit){
//         $.post("05_action.php",{
//             act:        "save_AssetFieldValue",
//             ass_id:     data["asset"]["ass_id"],
//             fieldName:  "res_reason_code",
//             fieldValue: new_reason_code
//         },
//         function(confirmedFV, status){
//             // console.log("new_reason_code:"+new_reason_code)
//             console.log("confirmedFV:"+confirmedFV)
//             if(new_reason_code==confirmedFV){
//                 // console.log("Saved successfully")
//                 data["asset"]["res_reason_code"] = new_reason_code
//                 $(".txy").css("background-color","white")
//                 if (noedit){
//                     window.location.href = "10_stk.php";
//                 }else{
//                     setPage()
//                 }
//             }
//         });
//     }

//     function setPage(){
//         $(".rcCat").hide();
//         $(".btnCancel").hide();
//         $(".btnClear").hide();
//         $("#areaRCs").hide();
//         $("#areaInputs").hide();
//         $(".rc_option").hide();
//         $("#btnTemplate").hide();
//         $(".btnDeleteFF").hide();
//         $(".btnCamera").hide();
//         $("#res_reason_code").text("");
//         let res_reason_code = data["asset"]["res_reason_code"];
//         let rc_details;
//         for (let rc_no in data["reasoncodes"]){
//             rc_details =  data["reasoncodes"][rc_no]["res_reason_code"]==res_reason_code ? data["reasoncodes"][rc_no]: rc_details;
//         }
//         if(res_reason_code){// Asset is finished!
//             $("#res_reason_code").text(res_reason_code+" - "+tempData["arrRC"][res_reason_code]);
//             $(".btnClear").show();
//             $("#areaInputs").show();
//             $(".txy").prop('disabled', false);
//             $(".btnCamera").show();
//             // if(res_reason_code.substring(0,2)=="FF"){
//             if(rc_details["rc_section"]=="FF"){
//                 $("#btnTemplate").show();
//                 $(".btnClear").hide();
//                 $(".btnDeleteFF").show();
//             }else if(res_reason_code=="AF20"&&data["asset"]["genesis_cat"]=="Added from RR"){
//                 $(".btnClear").hide();
//                 $(".btnDeleteFF").show();
//                 $("#tags").focus();

//             }else if(res_reason_code=="ND10"){
//                 $("#tags").focus();
//             }
//         }else if(tempData["tempReasonCat"]=="notfound"){//Select a not found reason code
//             $(".btnCancel").show();
//             $("#areaRCs").show();
//             $(".rc_sectionNF").show();
//         }else if(tempData["tempReasonCat"]=="error"){//Select an error reason code
//             $(".btnCancel").show();
//             $("#areaRCs").show();
//             $(".rc_sectionERR").show();
//         }else if(tempData["tempReasonCat"]=="rfc"){//Select an error reason code
//             $(".btnCancel").show();
//             $("#areaRCs").show();
//             $(".rc_sectionRFC").show();
//         }else{//his asset has not been assessed
//             $(".txy").prop('disabled', true);
//             $("#ta_comment").prop('disabled', false);
//             // $(".txy").css("background-color","#e9ecef")
//             $(".rcCat").show();
//             $("#areaInputs").show();
//         }
//     }


//     fnInitialSetup()
//     setPage(data)
//     fnGetImgGallery()
// });
</script>


<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php include "04_footer.php"; ?>




