<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>



<script src="includes/standalone.js"></script>

<script>
const config = {
  filename: 'general-ledger-Q1',
  sheet: {
    data: [
      [{
        value: 'Income - Webshop',
        type: 'string'
      }, {
        value: 1000,
        type: 'number'
      }]
    ]
  }
};

// zipcelx(config);
</script>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-6">Activities</h1>

        <div v-if="message !== ''" class="container">
            <div class="alert alert-danger"><strong>Error!</strong>{{message}}</div>     
        </div>
        

        <div class="table-responsive-sm">
            <table id="tbl_stk" class="table table-sm table-striped table-hover lead" >
                <caption>
                    <button type="button" class='btn btn-primary float-right' v-on:click="openUploadDlg">Upload<i class="fa fa-upload ml-2"></i></button>
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
                        <th style="width: 12px">Archive</th>
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
                        <button v-if='actv.stk_include==1' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,0,actv.stk_type)' 
                            class='btn btn-dark float-left'>
                            <i class="fa fa-folder-minus ml-2"></i>
                        </button>
                        <button v-if='actv.stk_include!=1' 
                            v-on:click='save_activity_toggle_include(actv.stkm_id,1,actv.stk_type)' 
                            class='btn btn-outline-dark float-left'>
                            <i class="fa fa-folder-plus ml-2"></i>
                        </button>
                    </td>
                    <td>
                        <button v-if='!actv.smm_delete_date'  
                            v-on:click='save_activity_toggle_delete(actv.stkm_id,1)' 
                            class='btn btn-outline-danger float-left'>
                            <i class="fas fa-trash-alt ml-2"></i>
                        </button>
                        <button v-if='actv.smm_delete_date' v-on:click='save_activity_toggle_delete(actv.stkm_id,0)' class='btn btn-outline-secondary float-left'>Restore</button>
                    </td>
                    <td>
                        <button class='btn btn-outline-dark float-left' v-on:click="export_to_xls(actv.stkm_id)" v-if='false'>Excel</button>
                    </td>
                    <td>
                        <button class='btn btn-outline-dark float-left' v-on:click='export_activity(actvidx)'>Export</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <button class='btn btn-primary float-right' 
            v-on:click='show_deleted=!show_deleted'>Show deleted</button>
    </div>


<!-- upload progress dialog  -->
<div class="container">
    <button ref="btn_open_progress" type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#dlg_progress" style="visibility:hidden;">Open Progress Dlg</button>

    <!-- Modal -->
    <div class="modal fade" id="dlg_progress" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">                       
                    <h4 class="modal-title">File Upload</h4>
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
                        <div></div>
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
						
    
    
<!--     
    <div class='row'>
        <div class='col'>
            <a href='00_status.html' class='btn btn-danger'>Fix me</a>
        </div>
    </div>    
-->    
    <div hidden>
        <input hidden type="file" ref="upload_file" v-on:change="uploadData" class="form-control-file">
    </div>
</div>

<script>


function makeFileAndDL(filename, text) {
    // makeFileAndDL("lucas.txt", "All your base are belong to us") //Simple usage
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
        show_deleted:false,

        upload: {
            current: 0,
            total: 0,
            status: '',
            message: ''
        }
    },
    created() {
        this.get_activities()
    },
    mounted () {

    } ,
    methods:{
        openUploadDlg(){
            this.$refs.upload_file.click();
        },
        uploadData(){
            this.$refs.btn_open_progress.click();
            console.log("Upload file ...");
            let file=this.$refs.upload_file.files[0]

            console.log("Select file: "+file.name+"("+file.size+")");

            let reader = new FileReader();
            reader.onprogress = event => {
                //console.log("onprogress event:");
                //console.log(event);
                //$('#progress_value').text(event.loaded+'%');
                //$('#progress_bar').width(event.loaded+'%').attr('aria-valuenow',event.loaded).attr('aria-valuemax',event.total);
            };
            reader.onload = event => {
                let uploadData=JSON.parse(event.target.result);
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
                )
            };
            reader.readAsText(file);

        },
        onUploadProgress (current, total, status, message){
            this.upload.current=current;
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
            .then(response=> {
                this.actvd   = response.data;
                console.log(this.actvd)
            })
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
            console.log('stkm_id: '+stkm_id+', stk_include: '+stk_include+', stk_type: '+act_type);
            if((stk_include==1) && (! this.isInclude(act_type))){

            }else{
                payload     = {'act':'save_activity_toggle_include', stkm_id, stk_include}
                res         = fnapi(payload)
                this.get_activities();
                vm_menu.refresh_sys();
            }
        }, 
        save_activity_toggle_delete(stkm_id, delete_status){
            payload     = {'act':'save_activity_toggle_delete', stkm_id, delete_status}
            res         = fnapi(payload)
            this.get_activities()
            // console.log(this.actvd)
        }, 
        export_to_xls(activity_id){
            payload     = {'act':'get_stk_assets_to_export', "stkm_id":activity_id}
            res         = fnapi(payload)

            data=[]
            tbl = "<table>"
            tbl+="<tr>"
            for(let colidx in res[0]){
                tbl+="<td>"+colidx+"</td>"
                data
            }
            tbl+="</tr>"
            for(let rowidx in res){
                rowval = res[rowidx]
                tbl+="<tr>"
                for(let colidx in rowval){
                    colval = rowval[colidx]
                    tbl+="<td>"+colval+"</td>"
                }
                tbl+="</tr>"
            }
            tbl+="</table>"

            data = [
                [{
                    value: 'Income - Webshop',
                    type: 'string'
                }, {
                    value: 1000,
                    type: 'number'
                }]
            ]

            const config2 = {filename: 'general-ledger-Q1',sheet: {data}};
            zipcelx(config2);
            makeFileAndDL("test.xls", tbl)
        },
        export_activity(activity_id){
            actv = this.actvd[activity_id]
            console.log(actv)
            header_obj                      = {}
            header_obj['type']              = actv.stk_type
            header_obj['file_version']      = 12
            header_obj['stk_name']          = actv.stk_name
            header_obj['dpn_extract_date']  = actv.dpn_extract_date
            header_obj['dpn_extract_user']  = actv.dpn_extract_user
            header_obj['smm_extract_date']  = new Date().toISOString().substring(0,19)
            header_obj['smm_extract_user']  = null
            header_obj['unique_file_id']    = "TBA"
            name_suffix = ""
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

                payload                         = {'act':'get_stk_assets_export', 'stkm_id':actv.stkm_id };
                header_obj['assetlist']         = fnapi(payload);
                
            }else if (actv.stk_type=="is_audit"){
                name_suffix                     = actv.stk_name;

                payload = {'act':'export_is', 'stkm_id':actv.stkm_id };
                header_obj= fnapi(payload);
                header_obj['type']              = actv.stk_type;
                header_obj['file_version']      = 12;
                header_obj['smm_extract_date']  = new Date().toISOString().substring(0,19);
                header_obj['smm_extract_user']  = null;
                header_obj['unique_file_id']    = "TBA"		
            }

            date_name_short = new Date().toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,8)
            name_suffix     = name_suffix!='' ? '_'+name_suffix : name_suffix;
            file_name       = date_name_short   + "_SMARTM" + name_suffix + ".json"
            json_string     = JSON.stringify(header_obj)
            makeFileAndDL(file_name, json_string)
        }, 
    }
})
</script>
<?php include "04_footer.php"; ?>