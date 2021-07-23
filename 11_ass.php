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
                            <button @click="subselector='SAVON'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning" 
                                    type="button" >Change<br>reason code</button>
                                    
                            <button @click="select_rc('RE20')"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>Edit</button>
                            <button @click="select_rc('ND10',true)"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>No Edit</button>
                            <button @click="subselector='SAVOFF'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning mt-3" 
                                    type="button" >Not<br>found</button>
                            <button @click="subselector='ND'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-primary mt-3" 
                                    type="button" >Found<br>other</button>
                            <button @click="subselector='RFC'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Remove<br>from count</button>
                            <button @click="subselector='PRERESOLVE'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Preresolve</button>

                            <button @click="show_clear_rc_options=true"
                                    v-if="assd.res_reason_code&&!show_clear_rc_options&&assd.genesis_cat!='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Clear<br>RC</button>
                            <button @click="show_clear_rc_options=false"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel</button>
                            <button @click="select_rc('')"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure<br>Clear</button>

                            <button @click="show_delete_options=true"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Delete</button>
                            <button @click="delete_stk_asset('delete')"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure</button>
                            <button @click="show_delete_options=false"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel delete</button>
                            <a      :href="'13_camera.php?ass_id='+ass_id"
                                    v-if="assd.res_reason_code"
                                    class="text-center list-group-item list-group-item-action list-group-item-dark mt-3" 
                                    type="button"><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
                        </div>
                    </span>
                    
                    <button @click="delete_stk_asset('undelete')"
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
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assetdesc1' 
                                :bound_value='assd.res_assetdesc1'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Asset Description 2</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assetdesc2' 
                                :bound_value='assd.res_assetdesc2'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Asset Main No Text</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_assettext' 
                                :bound_value='assd.res_assettext'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Inventory</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_inventory' 
                                :bound_value='assd.res_inventory'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Inventory No.</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_inventno' 
                                :bound_value='assd.res_inventno'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Serial No</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_serialno' 
                                :bound_value='assd.res_serialno'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                        </div>
                        <div class='col-3'>
                            <div class="form-group"><label>Location</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_location' 
                                :bound_value='assd.res_loc_location'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Level/Room</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_room' 
                                :bound_value='assd.res_loc_room'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Latitude</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_latitude' 
                                :bound_value='assd.res_loc_latitude'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Longitude</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_loc_longitude' 
                                :bound_value='assd.res_loc_longitude'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Group Custodian</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_grpcustod' 
                                :bound_value='assd.res_grpcustod'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Quantity</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_quantity' 
                                :bound_value='assd.res_quantity'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                        </div>
                        <div class='col-3'>
                            <div class="form-group"><label>Class</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_class' 
                                :bound_value='assd.res_class'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Type Name</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_class_ga_cat' 
                                :bound_value='assd.res_class_ga_cat'
                                :disabled='true'
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Cap Date {{assd.res_date_cap_clean}}</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_date_cap' 
                                :bound_value='assd.res_date_cap_clean'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                inputtype='date'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Last Inv {{assd.res_date_lastinv_clean}}</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_date_lastinv' 
                                :bound_value='assd.res_date_lastinv_clean'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                inputtype='date'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Cost Centre</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_ccc' 
                                :bound_value='assd.res_ccc'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <div class="form-group"><label>Current NBV</label>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
                                column_name='res_val_nbv' 
                                :bound_value='assd.res_val_nbv'
                                :disabled="assd.delete_date||assd.res_reason_code==''"
                                maxlen='255'
                                ></textinput>
                            </div>
                            <br><button type='button' class='btn btn-outline-dark float-right' @click='ga_create_template'
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
                                
                                <span v-for='(img, imgidx) in imgsd'>
                                   
                                    
                                    <button type='button' class='btn thumb_photo' 
                                            data-toggle='modal' data-target='#modal_show_pic'
                                            @click="zoom_pic = 'images/' + img;">
                                            <img :src="'images/'+img" width='200px'>
                                    </button>
                                    
                                </span>
                            
                            </div>
                        </div>
                    </div>



                    <div class='row'>
                        <div class='col-12'>
                            <div class="form-group"><h2>Comments</h2>
                                <textinput :primary_key='ass_id' primary_key_name="ass_id" db_name='smartdb' table_name='sm14_ass' 
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
                            <button @click="subselector='SAVON'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning" 
                                    type="button" >Change<br>reason code</button>
                            <button @click="select_rc('RE20')"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>Edit</button>
                            <button @click="select_rc('ND10',true)"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Sighted<br>No Edit</button>
                            <button @click="subselector='SAVOFF'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-warning mt-3" 
                                    type="button" >Not<br>found</button>
                            <button @click="subselector='ND'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-primary mt-3" 
                                    type="button" >Found<br>other</button>
                            <button @click="subselector='RFC'"
                                    v-if="!assd.res_reason_code&&assd.genesis_cat=='original'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Remove<br>from count</button>
                            <button @click="subselector='PRERESOLVE'"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-info mt-3" 
                                    type="button" >Preresolve</button>

                            <button @click="show_clear_rc_options=true"
                                    v-if="assd.res_reason_code&&!show_clear_rc_options&&assd.genesis_cat!='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Clear<br>RC</button>
                            <button @click="show_clear_rc_options=false"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel</button>
                            <button @click="select_rc('')"
                                    v-if="show_clear_rc_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure<br>Clear</button>

                            <button @click="show_delete_options=true"
                                    v-if="!show_delete_options&&assd.genesis_cat=='nonoriginal'"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >Delete</button>
                            <button @click="delete_stk_asset('delete')"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-danger mt-3" 
                                    type="button" >I'm sure</button>
                            <button @click="show_delete_options=false"
                                    v-if="show_delete_options"
                                    class="text-center list-group-item list-group-item-action list-group-item-success mt-3" 
                                    type="button" >Cancel delete</button>
                            <a      :href="'13_camera.php?ass_id='+ass_id"
                                    v-if="assd.res_reason_code"
                                    class="text-center list-group-item list-group-item-action list-group-item-dark mt-3" 
                                    type="button"><span class='octicon octicon-device-camera' style='font-size:30px'></span></a>
                        </div>
                    </span>
                    
                    <button @click="delete_stk_asset('undelete')"
                        v-if="assd.delete_date"
                        class="text-center list-group-item list-group-item-action list-group-item-danger" 
                        type="button" >Undelete</button>
                </nav>

                    


            </div>
	    </div>

        <span  v-if="subselector">
            <div class='row'>
                <div class='col'>
                    <button @click="subselector=false" class="btn btn-outline-dark float-right" type="button" >Cancel</button>
                </div>
            </div>
            <hr>
            <table class='table table-sm'>
                <tr v-for="(rc, rcidx) in rcsd" v-if="assd.genesis_cat==rc.rc_origin&&subselector==rc.rc_action">
                    <td>
                        <button @click="select_rc(rc.res_reason_code)"
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
                        <button @click="select_rc(rc.res_reason_code)"
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
                <h5 class="modal-title" id="exampleModalLabel">{{zoom_pic}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">  
                    <img :src="zoom_pic" width='100%'>
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
        show_delete_options:false
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
        ga_create_template(direction){
            payload     = {'act':'ga_create_template', 'ass_id':this.ass_id}
            json        = fnapi(payload)
          
            this.template_ass_id = json.ass_id;
            window.location.reload();
        }, 
    }
})
</script>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php include "04_footer.php"; ?>




