<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-4">General Assets Stocktake</h1>

        <table id="tbl_stk" class="table table-sm">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>DIST~WHSE</th>
                    <th>SCA</th>
                    <th>BIN_CODE</th>
                    <th>Stockcde</th>
                    <th>Name</th>
                    <th>Cat</th>
                    <th>SOH</th>
                    <th>TrkInd</th>
                    <th>TrkRef</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class='text-right'>Action</th>
                </tr>
            </thead>
            <tbody>
            <tr v-for='(rec, recidx) in json_records'>
                <td>
                    <a :href="'16_imp.php?auto_storageID='+rec.auto_storageID" class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                </td>
                <td>{{ rec.DSTRCT_CODE }}-{{ rec.WHOUSE_ID }}</td>
                <td>{{ rec.SUPPLY_CUST_ID }}</td>
                <td>{{ rec.BIN_CODE }}</td>
                <td>{{ rec.STOCK_CODE }}</td>
                <td>{{ rec.ITEM_NAME }}</td>
                <td>{{ rec.INVENT_CAT }}</td>
                <td>{{ rec.SOH }}</td>
                <td>{{ rec.TRACKING_IND }}</td>
                <td>{{ rec.TRACKING_REFERENCE }}</td>
                <td>{{ rec.isType }}</td>
                <td>
                    <h4 v-if="rec.findingID"><span :class="'badge badge-'+json_is_settings[rec.findingID].fCol">FIN~{{ json_is_settings[rec.findingID].fAbr }}</span></h4>
                    <h4 v-if="!rec.findingID"><span class='badge badge-secondary'>NYC~</span></h4>
                </td>
                <td>
                    <a :href="'16_imp.php?auto_storageID='+rec.auto_storageID" class='btn btn-primary'><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                </td>
            </tr>

                <!-- { "auto_storageID": "751", "stkm_id": "5", "storageID": null, "DSTRCT_CODE": "7018", "WHOUSE_ID": "RSER",
                
                 "SUPPLY_CUST_ID": "", "SC_ACCOUNT_TYPE": "", "STOCK_CODE": "015264783", 
                 "ITEM_NAME": "NAVIGATION SET, SATELLITE SIGNALS", "STK_DESC": "DAGR, COMPLETE W/BATTERY PACK", "BIN_CODE": "", "INVENT_CAT": "RE", 
                 "INVENT_CAT_DESC": "Repair Pool", "TRACKING_IND": "E", 
                 "SOH": "1", "TRACKING_REFERENCE": "", "LAST_MOD_DATE": null, "sampleFlag": null, "serviceableFlag": "1", "isBackup": "1", "isType": "imps", "targetID": "335", "delete_date": "0000-00-00 00:00:00", "delete_user": "", "res_create_date": "0000-00-00 00:00:00", "res_update_user": "", "findingID": "", "res_comment": "", "res_evidence_desc": "", "res_unserv_date": "0000-00-00 00:00:00", "isChild": null, "res_parent_storageID": null, "finalResult": "", "finalResultPath": "", "fingerprint": "", "checkFlag": null } -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tbl_stk').DataTable({
        stateSave: true
    });
});

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

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

function fnratr(nosub, notot){
    res = nosub / notot
    return res;
}





let vm = new Vue({
    el: '#app',
    data: {
        json_records:{},
        json_is_settings:{},
    },
    created() {
        this.get_is_records()
        this.get_is_settings()
    },
    methods:{
        get_is_records(){
            payload                 = {'act':'get_is_records'}
            this.json_records       = fnapi(payload)
            console.log(this.json_records)
        }, 
        get_is_settings(){
            payload                 = {'act':'get_is_settings'}
            json   = fnapi(payload)
            this.json_is_settings = []
            for(let idx in json){
                setting = json[idx]
                this.json_is_settings[setting.findingID] = setting
            }
            console.log(this.json_is_settings)
        }, 
    }
})
</script>

<?php include "04_footer.php"; ?>