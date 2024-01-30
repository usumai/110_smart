<?php include "02_header.php"; ?>

	
<script src="includes/standalone.js"></script>
<script src="includes/jszip/3.6.0.2/jszip.min.js"></script>

<div id="app" @dragover="fileDrag" @drop="fileDrop">
    <div class='container-fluid'>
        <h1 class="mt-4 display-4">Activities</h1>

        <div v-if="message !== ''" class="container">
            <div class="alert alert-danger"><strong>System Error </strong><br/><br/>{{message}}</div>     
        </div>
        

        <div class="table-responsive-sm" >
            <table id="tbl_stk" class="table table-sm table-striped table-hover caption-top"  >
                <caption>
                    <button type="button" class='btn btn-primary float-right' @click="openUploadDlg">Upload<i class="fa fa-upload ml-2"></i></button>
                </caption>
                <thead class="table-dark sticky-top">
                    <tr>
                        <th style="width: 40px">ID</th>
                        <th style="width: 80px">Type</th>
                        <th >Name</th>
                        <th style="width: 70px">Original</th>
                        <th style="width: 80px">Completed</th>
                        <th style="width: 70px">Extra</th>
                        <th style="width: 70px">Status</th>
                        <th style="width: 12px">Included</th>
                        <th style="width: 12px">Deleted</th>
                        <th style="width: 215px">Data Export</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for='(actv, actvidx) in actvd'  v-if='!actv.delete_date||actv.delete_date&&show_deleted'>
                    <td>{{ actv.stk_id }}</td>
                    <td>{{ actv.isCat}}</td>
                    <td>{{ actv.stk_name }}</td>
                    <td >{{ actv.rc_orig }}</td>
                    <td >{{ actv.rc_orig_complete }}</td>
                    <td >{{ actv.rc_extras }}</td>
                    <td >{{ ((actv.rc_orig_complete/actv.rc_orig)*100).toFixed(1) +'%' }}</td>
                    <td>
                        <button v-if='(actv.stk_include==1) && (!actv.delete_date)' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,0,actv.stk_type)' 
                            class='btn btn-dark float-left'>
                            <i class="fa fa-folder-minus ml-2"></i>
                        </button>
                        <button v-if='(actv.stk_include!=1) && (!actv.delete_date)' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,1,actv.stk_type)' 
                            class='btn btn-outline-dark float-left'>
                            <i class="fa fa-folder-plus ml-2"></i>
                        </button>
                    </td>
                    <td>
                        <button v-if='!actv.delete_date'  
                            v-on:click='save_activity_toggle_delete(actv.stkm_id,actv.stk_id,1)' 
                            class='btn btn-outline-danger float-left'>
                            <i class="fas fa-trash-alt ml-2"></i>
                        </button>
                        <button v-if='actv.delete_date' 
                        	v-on:click='save_activity_toggle_delete(actv.stkm_id,actv.stk_id,0)' 
                        	class='btn btn-outline-secondary float-left'>
                        	<i class="fas fa-check ml-2"></i>
                        </button>
                    </td>
                    <td>
                        <button class='btn btn-outline-dark ' @click="export_activity(actvidx,'xslx')">Excel</button>
                        <button class='btn btn-outline-dark ' @click="exportValidation(actvidx,'json')">Json</button>
                        <button v-if='actv.stk_type=="ga_stk"' class='btn btn-outline-dark ' v-on:click="export_activity(actvidx,'images')">Photos</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <button class='btn btn-primary float-right' 
            @click='show_deleted=!show_deleted'>Show deleted</button>
    </div>


<!-- upload progress dialog  -->
	<div class="container">
	    <button ref="btn_open_progress" type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#dlg_progress" style="visibility:hidden;">Open Progress Dlg</button>
	
	    <!-- Modal -->
	    <div class="modal fade" id="dlg_progress" role="dialog"  data-backdrop="static" >
	        <div class="modal-dialog">
	            <!-- Modal content-->
	            <div class="modal-content">
	                <div class="modal-header" style="background-color: #5a95ca;">                       
	                    <h5 class="modal-title" style="color: whitesmoke">File Upload</h5>
	                    <button type="button" class="close" data-dismiss="modal">&times;</button>
	                </div>
	                <div class="modal-body">
	                    <div class="container" style="width:100%">
	
	                        <div v-if="upload.status == 'Processing'" class="alert alert-info"><strong>{{upload.status}}!</strong> {{upload.message}}</div>     
	                        <div v-if="upload.status == 'Completed'" class="alert alert-success"><strong>{{upload.status}}!</strong> {{upload.message}}</div>     
	                        <div v-if="upload.status == 'Error'" class="alert alert-danger"><strong>{{upload.status}}!</strong> {{upload.message}}</div>     
	                        <div class="progress">
	                            <div id="progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
	                                <span id="progress_value">0%</span>
	                            </div>
	                        </div>
	                        <div>{{upload.taskDescription}}</div>
	                        <div style="width: 100%; padding-top: 10px; display: flex;">
	                            <span style="width: 50%">Current: {{upload.current}}</span>
	                            <span style="width: 50%">Total: {{upload.total}}</span>
	                        </div>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class='btn btn-outline-dark' data-dismiss="modal">Close</button>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
						
      
    <div hidden>
        <input hidden type="file" ref="upload_file" v-on:change="uploadData" />
        <button ref="isExportWarnDlgRef" hidden type='button' data-toggle='modal' data-target='#modal_is_export_warning'></button>
    </div>
    
    
	<!-- IS data export warning -->
	<div class="modal fade" id="modal_is_export_warning" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header" style="background-color: #5a95ca;">
				<h5 class="modal-title" style="color: whitesmoke">Warning</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p class="lead">There are {{isExportWarningDlg.notYetCompleteItems}} not yet complete items. Do you want to continue?</p>     
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal" @click="export_activity(isExportWarningDlg.activityId, isExportWarningDlg.format)">Continue</button>				
			</div>
			</div>
		</div>
	</div>    
</div>

<script>

let vm = new Vue({
    el: '#app',
    data: {
        message: '',
        actvd:{},
        show_deleted:false,
        isExportWarningDlg: {
			notYetCompleteItems: 0,
			activityId: -1,
			format: 'json'
		},
        upload: {
            current: 0,
            taskDescription: 'testing',
            total: 0,
            status: '',
            message: ''
        }
    },
    created() {

    },
    mounted () {
    	 this.get_activities();
    } ,
    methods:{
        fileDrag(event){
        	var isJSON = (event.dataTransfer.items.length>0) && ((event.dataTransfer.items[0].type=='application/json')||(event.dataTransfer.items[0].type=='application/x-zip-compressed')) ;
	      	if (isJSON) {
	      		event.preventDefault();
	            event.dataTransfer.dropEffect='copy'; 
	            event.dataTransfer.effectAllowed='copy';
	      	}
            
        },
        fileDrop(event){
        	event.preventDefault();
            var items=event.dataTransfer.items;
            for(var i in items){
            	console.log(items[i]);
                if(items[i].kind=='file'){
                    this.uploadData(items[i].getAsFile());                    
                }
            }

            
        },
        openUploadDlg(){

        	this.$refs.upload_file.value='';
            this.$refs.upload_file.click();

        },
        uploadData(uploadFile){
            var type=type_of(uploadFile);
            var file=null;
            if(!uploadFile){
            	file=this.$refs.upload_file.files[0];
            }else if(type=='Blob' || type=='File'){
				file=uploadFile;
			}else{
				file=uploadFile.target.files[0];
			}
			
            this.$refs.btn_open_progress.click();


			if(file.type == 'application/zip' || file.type == 'application/x-zip-compressed'){
				importGaData(file, this.onUploadProgress,
	                    (result)=>{
	                        this.get_activities();
					    },
	                    (errors)=>{
	                        this.upload.status='Error';
	                        for( i in errors) {                            
	                            this.upload.message=errors[i].code + ' - ' + errors[i].info;
	                        }
	                        console.log(this.message);
	                    }						
				);
				return;
			}
			
            let reader = new FileReader();
            reader.onprogress = event => {
            };
            reader.onload = event => {
				try{
	                var uploadData=JSON.parse(event.target.result);
	                uploadData.file_name=file.name;
	                uploadData.file_type="json";
	                uploadData.file_desc="smartm data import";
	                uploadData.file_ref=uploadData.unique_file_id;
	                uploadData.format_version=uploadData.file_version;
	                uploadData.import_date=new Date();
	                
	                upload( uploadData, this.onUploadProgress,				
	                    (result)=>{
	                        this.get_activities();
					    },
	                    (errors)=>{
	                        this.upload.status='Error';
	                        for( i in errors) {                            
	                            this.upload.message=errors[i].code + ' - ' + errors[i].info;
	                        }
	                        console.log(this.message);
	                    }
	                );
				}catch(ex){		
					this.upload.status='Error';
					this.upload.message="Invalid file format. Make sure only uploading SMARTM JSON file created by DPN SMART";
					console.log(ex);
				}
            };
            reader.readAsText(file);
        },
        onUploadProgress (current, total, status, message, currentTaskDescription){
            this.upload.current=current;
            this.upload.taskDescription=currentTaskDescription;
            this.upload.total=total;
            this.upload.status=status;
            this.upload.message=message;
            var percentage=(current/total)*100;
            $('#progress_value')
            .text(percentage.toFixed(1)+'%');                     
            
            $('#progress_bar')
            .width(percentage.toFixed(1)+'%')
            .attr('aria-valuenow',percentage.toFixed(1)); 
        },
        get_activities(){
            axios.get('api.php', {params: {act:'get_activities'}})
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
                if((this.actvd[act].stk_include==1) && (!this.actvd[act].delete_date)){
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
        		if((this.actvd[i].stk_id == stk_id) && (!this.actvd[i].delete_date)){
					return true;
            	}
        	}
        	return false;
    	},

    	exportValidation(activityIndex, exportFormat){
    		var actv = this.actvd[activityIndex];
        	if(actv.stk_type=='is_audit'){    		
	    		countIsNotYetCompleteItems(actv.stkm_id).then(
	    	    	count=>{
			        	if(count>0){  
			        		
				        	this.isExportWarningDlg.notYetCompleteItems=count;
				        	this.isExportWarningDlg.activityId=activityIndex;
				        	this.isExportWarningDlg.format=exportFormat;      		
			        		this.$refs.isExportWarnDlgRef.click();
			        	}else{
			        		this.export_activity(activityIndex, exportFormat);
				        }
	    	    	}
	    	    );
    		}else{
        		this.export_activity(activityIndex, exportFormat);
        	}
        },
        export_activity(activity_id, exportFormat){
 
            actv = this.actvd[activity_id]
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

                header_obj['asset_lock_date']   = '';

                payload                         = {'act':	'export_ga_data', 
                								   'stkm_id': actv.stkm_id 
                								};
                list = fnapi(payload);
                header_obj['rc_totalsent'] =  list.length;
                header_obj['assetlist']         = list;
                
            }else if (actv.stk_type=="is_audit"){
                name_suffix                     = actv.stk_name;

                payload 						= {	'act': 'export_is', 
                									'stkm_id': actv.stkm_id 
                								};
                header_obj= fnapi(payload);
//                header_obj['smm_extract_date']  = new Date().toISOString().substring(0,19);
            }
            
            date_name_short = new Date().toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,8);
            name_suffix     = name_suffix!='' ? '-'+name_suffix : name_suffix;
            file_name       = 'SMARTM-'+date_name_short +'-'+actv.stk_id+name_suffix;
                        
            if(exportFormat=='json'){
                var fileBlob=createFileBlob(file_name + ".json", JSON.stringify(header_obj));
                uploadFileBlob('backup_export_json', fileBlob,
                	ok=>{
                		downloadFileBlob(fileBlob);
                	},
                	errors=>{
                    	if(errors && errors[0]){
                    		this.message=errors[0].info;
                    	}else{
                        	this.message=errors;
                        }
                    }
                );
            }else if(exportFormat=='images'){
            	exportGaData(actv.stkm_id, file_name + ".zip",null,
                	url => {
                		downloadFileBlob(url);
				    },
                    error => {
                        if(error && error.length > 0)
                        	this.message=error[0].info;
                    });    
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