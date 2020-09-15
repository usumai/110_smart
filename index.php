<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-4">SMART Mobile {{ message }}</h1>

        <div class='row'>
            <div class='col-2'>
                <form action='05_upload.php' method='post' enctype="multipart/form-data" id="form_upload">
                    <input type="file" name='file_upload' class="form-control-file">
                    <button type='submit' class='btn btn-outline-dark'>Upload</button>
                </form>
            </div>
        </div>

        <table id="tbl_stk" class="table lead" >
            <thead>
                <tr>
                    <td>SMARTM#</td>
                    <td>Type</td>
                    <td>ID</td>
                    <td>Name</td>
                    <td align='right'>Orig</td>
                    <td align='right'>Completed</td>
                    <td align='right'>Extra</td>
                    <td align='right'>Status</td>
                    <td align='right'>Included</td>
                    <td align='right'>Archive</td>
                    <td align='right'>Excel</td>
                    <td align='right'>Export</td>
                </tr>
            </thead>
            <tbody>
            <tr v-for='(actv, actvidx) in actvd'  v-if='!actv.smm_delete_date||actv.smm_delete_date&&show_deleted'>

            
                <td>{{ actv.stkm_id }}</td>
                <td>{{ actv.stk_type }}</td>
                <td>{{ actv.stk_id }}</td>
                <td>{{ actv.stk_name }}</td>
                <td align='right'>{{ actv.rc_orig }}</td>
                <td align='right'>{{ actv.rc_orig_complete }}</td>
                <td align='right'>{{ actv.rc_extras }}</td>
                <td align='right'>{{ actv.rc_orig_complete/actv.rc_orig }}</td>
                <td>
                    <button v-if='actv.stk_included==1' v-on:click='save_activity_toggle_include(actv.stkm_id,0)' class='btn btn-dark float-right'>Included</button>
                    <button v-if='actv.stk_included!=1' v-on:click='save_activity_toggle_include(actv.stkm_id,1)' class='btn btn-outline-dark float-right'>Include</button>
                </td>
                <td>
                    <button v-if='!actv.smm_delete_date'  v-on:click='save_activity_toggle_delete(actv.stkm_id,1)' class='btn btn-outline-danger float-right'>Delete</button>
                    <button v-if='actv.smm_delete_date' v-on:click='save_activity_toggle_delete(actv.stkm_id,0)' class='btn btn-outline-secondary float-right'>Restore</button>
                </td>
                <td>
                    <button class='btn btn-outline-dark float-right'>Excel</button>
                </td>
                <td>
                    <button class='btn btn-outline-dark float-right' v-on:click='export_activity(actvidx)'>Export</button>
                </td>
            </tr>
            </tbody>
        </table>
        <button class='btn btn-outline-dark float-right' v-on:click='show_deleted=!show_deleted'>Show deleted</button>

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
        message: 'System is working',
        actvd:{},
        show_deleted:false,
    },
    created() {
        this.get_activities()
    },
    methods:{
        get_activities(){
            payload     = {'act':'get_activities'}
            this.actvd   = fnapi(payload)
            console.log(this.actvd)
        }, 
        save_activity_toggle_include(stkm_id, stk_include){
            payload     = {'act':'save_activity_toggle_include', stkm_id, stk_include}
            res         = fnapi(payload)
            this.get_activities()
            // console.log(this.actvd)
            vm_menu.refresh_sys();
        }, 
        save_activity_toggle_delete(stkm_id, delete_status){
            payload     = {'act':'save_activity_toggle_delete', stkm_id, delete_status}
            res         = fnapi(payload)
            this.get_activities()
            // console.log(this.actvd)
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
                name_suffix                     = actv.stk_name
                header_obj['stkm_id']           = actv.stkm_id
                header_obj['stk_id']            = actv.stk_id
                header_obj['stk_name']          = actv.stk_name

                header_obj['rc_orig']           = actv.rc_orig
                header_obj['rc_orig_complete']  = actv.rc_orig_complete
                header_obj['rc_extras']         = actv.rc_extras
                header_obj['rc_totalsent']      = actv.rc_totalsent
                header_obj['asset_lock_date']   = ''

                payload                         = {'act':'get_stk_assets_export', 'stkm_id':actv.stkm_id }
                header_obj['assetlist']         = fnapi(payload)
                
            }else if (export_type=="ga_rr"){

            }

            // date_name = new Date().toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,15)
            date_name_short = new Date().toISOString().replace(/-/g,'').replace(/:/g,'').substring(2,8)
            name_suffix     = name_suffix!='' ? '_'+name_suffix : name_suffix;
            file_name       = date_name_short   + "_SMARTM" + name_suffix + ".json"
            json_string     = JSON.stringify(header_obj)
            makeFileAndDL(file_name, json_string)
            // return header_obj
        }, 
    }
})
</script>

<?php include "04_footer.php"; ?>