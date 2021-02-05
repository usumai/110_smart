<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>



<script src="includes/standalone.js"></script>
<script src="app/ui/component/vue/fileupload.js"></script>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-6">Activities</h1>

        <div v-if="message !== ''" class="container">
            <div class="alert alert-danger"><strong>Error!</strong>{{message}}</div>     
        </div>
        

        <div class="table-responsive-sm">
            <table id="tbl_stk" class="table table-sm table-striped table-hover" >
                <caption>
                	<fileupload :completed="get_activities" class="float-right"></fileupload>
                </caption>
                <thead class="table-dark">
                    <tr>
                        <th style="width: 120px">SMARTM#</th>
                        <th style="width: 120px">Type</th>
                        <th style="width: 70px">ID</th>
                        <th >Name</th>
                        <th style="width: 70px">Orig</th>
                        <th style="width: 120px">Completed</th>
                        <th style="width: 70px">Extra</th>
                        <th style="width: 70px">Status</th>
                        <th style="width: 12px">Included</th>
                        <th style="width: 12px">Deleted</th>
                        <th style="width: 12px">Excel</th>
                        <th style="width: 12px">Export</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for='(actv, actvidx) in actvd'  v-if='!actv.smm_delete_date||actv.smm_delete_date&&show_deleted'>

                
                    <td>{{ actv.stkm_id }}</td>
                    <td>{{ actv.isCat}}</td>
                    <td>{{ actv.stk_id }}</td>
                    <td>{{ actv.stk_name }}</td>
                    <td >{{ actv.rc_orig }}</td>
                    <td >{{ actv.rc_orig_complete }}</td>
                    <td >{{ actv.rc_extras }}</td>
                    <td >{{ ((actv.rc_orig_complete/actv.rc_orig)*100).toFixed(1) +'%' }}</td>
                    <td>
                        <button v-if='(actv.stk_include==1) && (!actv.smm_delete_date)' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,0,actv.stk_type)' 
                            class='btn btn-dark float-left'>
                            <i class="fa fa-folder-minus ml-2"></i>
                        </button>
                        <button v-if='(actv.stk_include!=1) && (!actv.smm_delete_date)' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,1,actv.stk_type)' 
                            class='btn btn-outline-dark float-left'>
                            <i class="fa fa-folder-plus ml-2"></i>
                        </button>
                    </td>
                    <td>
                        <button v-if='!actv.smm_delete_date'  
                            v-on:click='save_activity_toggle_delete(actv.stkm_id,actv.stk_id,1)' 
                            class='btn btn-outline-danger float-left'>
                            <i class="fas fa-trash-alt ml-2"></i>
                        </button>
                        <button v-if='actv.smm_delete_date' 
                        	v-on:click='save_activity_toggle_delete(actv.stkm_id,actv.stk_id,0)' 
                        	class='btn btn-outline-secondary float-left'>
                        	<i class="fas fa-check ml-2"></i>
                        </button>
                    </td>
                    <td>
                        <button class='btn btn-outline-dark float-left' v-on:click="export_activity(actvidx,'xslx')">Excel</button>
                    </td>
                    <td>
                        <button class='btn btn-outline-dark float-left' v-on:click="export_activity(actvidx,'json')">Export</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <button class='btn btn-primary float-right' 
           @click='show_deleted=!show_deleted'>Show deleted</button>
    </div>
</div>

<script>


function makeFileAndDL(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

let vm = new Vue({
    el: '#app',
    data: {
        message: '',
        actvd:{},
        show_deleted:false
    },
    created() {

    },
    mounted () {
    	 this.get_activities();
    } ,
    methods:{
        get_activities(){
            axios.get( API_ENDPOINT, {params: {act:'get_activities'}})
            .then(response => {
            	console.log(response);
            	processResponse(response,
            		data=>{
            			this.actvd   = data;
            		}, 
            		errors=>{
            			this.message = errors[0].info; 
            		});                
                
            });
        }, 
        isInclude(act_type){
            var includeAct=null;
            for(var act in this.actvd){
                if((this.actvd[act].stk_include==1) && (!this.actvd[act].smm_delete_date)){
                    includeAct=this.actvd[act];
                    break;
                }
            }
            if(includeAct==null)
                return true;
            
            return includeAct.stk_type==act_type;
        },
        save_activity_toggle_include(stkm_id, stk_include, act_type){
            if((stk_include==1) && (! this.isInclude(act_type))){

            }else{
                payload     = {'act':'save_activity_toggle_include', stkm_id, stk_include}
                res         = fnapi(payload)
                this.get_activities();
                vm_menu.refresh_sys();
            }
        }, 
        save_activity_toggle_delete(stkm_id,stk_id, delete_status){
            if((delete_status==0)&&(this.isActivityActive(stk_id))){
                return;
            }
            payload     = {'act':'save_activity_toggle_delete', stkm_id, delete_status};
            res         = fnapi(payload);
            this.get_activities();
        }, 
		isActivityActive(stk_id){
    		for (i in this.actvd){
        		if((this.actvd[i].stk_id == stk_id) && (!this.actvd[i].smm_delete_date)){
					return true;
            	}
        	}
        	return false;
    	},
        export_activity(activity_id, exportFormat){
            actv = this.actvd[activity_id]
            console.log(actv);
            header_obj                      = {};
            header_obj['type']              = actv.stk_type;
            header_obj['file_version']      = 12;
            header_obj['stk_name']          = actv.stk_name;
            header_obj['dpn_extract_date']  = actv.dpn_extract_date;
            header_obj['dpn_extract_user']  = actv.dpn_extract_user;
            header_obj['smm_extract_date']  = new Date().toISOString().substring(0,19);
            header_obj['smm_extract_user']  = null;
            header_obj['unique_file_id']    = "TBA";
            name_suffix = "";
            
            if (actv.stk_type=="ga_stk"){
                name_suffix                     = actv.stk_name;
                header_obj['stkm_id']           = actv.stkm_id;
                header_obj['stk_id']            = actv.stk_id;
                header_obj['stk_name']          = actv.stk_name;

                header_obj['rc_orig']           = actv.rc_orig;
                header_obj['rc_orig_complete']  = actv.rc_orig_complete;
                header_obj['rc_extras']         = actv.rc_extras;
                header_obj['rc_totalsent']      = actv.rc_totalsent;
                header_obj['asset_lock_date']   = ''

                payload                         = {'act':	'export_ga', 
                								   'stkm_id': actv.stkm_id 
                								};
                
                header_obj['assetlist']         = fnapi(payload);
                
                
            }else if (actv.stk_type=="is_audit"){
                name_suffix                     = actv.stk_name;

                payload 						= {	'act': 'export_is', 
                									'stkm_id': actv.stkm_id 
                								};
                header_obj= fnapi(payload);
                header_obj['type']              = actv.stk_type;
                header_obj['file_version']      = 12;
                header_obj['smm_extract_date']  = new Date().toISOString().substring(0,19);
                header_obj['smm_extract_user']  = null;
                header_obj['unique_file_id']    = "TBA";		
            }
            
            date_name_short = new Date().toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,8);
            name_suffix     = name_suffix!='' ? '_'+name_suffix : name_suffix;
            file_name       = date_name_short   + "_SMARTM" + name_suffix;
                        
            if(exportFormat=='json'){
            	file_name       = file_name + ".json";
                json_string     = JSON.stringify(header_obj);
                makeFileAndDL(file_name, json_string);
            }else if(exportFormat=='xslx'){
                data = [
                	XSLX_ACTIVITY_COL_HEADER,
	                [],
                    [
                    	{
	                        value:  header_obj['type'],
	                        type: 'string'
	                    },{
	                        value: header_obj['file_version'],
	                        type: 'string'
	                    },{
	                    	value: header_obj['stk_name'],
	                    	type: 'string'
	                    },{
	                    	value: header_obj['dpn_extract_date'],
	                    	type: 'string'
	                    },{
	                    	value: header_obj['dpn_extract_user'],
	                    	type: 'string'
	                    },{
	                    	value: header_obj['smm_extract_date'],
	                    	type: 'string'
	                    },{
	                    	value: header_obj['smm_extract_user'],
	                    	type: 'string'
	                    },{
	                    	value:  header_obj['unique_file_id'] ,
	                    	type: 'string'
	                    }
                    ],
                    []
                ];
                n=3;
                if(actv.stk_type=="ga_stk"){
                    
                	data[++n]=XSLX_GA_ASSET_COL_HEADER;  
                	data[++n]=[];                
	                for(i in header_obj.assetlist){
	                	var rec = header_obj.assetlist[i];
	                	data[++n]= createExcelRow(XSLX_GA_ASSET_COL_HEADER, rec);
	                }   	
                }else if(actv.stk_type=="is_audit"){

                	data[++n]=XSLX_IS_IMPAIRMENT_COL_HEADER;  
                	data[++n]=[];   	
	                for(i in header_obj.impairments) {
	                	var rec = header_obj.impairments[i];
	                	rec.storage_id=(rec.data_source!='extra' ? rec.storage_id : rec.res_parent_storage_id);
	                	rec.findingID = rec.resAbbr;               	
	                	data[++n]= createExcelRow(XSLX_IS_IMPAIRMENT_COL_HEADER, rec);    	
	                }
                }
                
                const config = {
                	filename: file_name, 
                	sheet: {data}
                };
                zipcelx(config);
            }

        }, 
    }
})
</script>
<?php include "04_footer.php"; ?>