<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<div id="app">
    <div class="container-fluid">
        <h1 class="mt-5 display-6">General Assets Stocktake</h1>
        <div class="table-responsive-sm">
            <table id="tbl_stk" class="table table-sm table-striped table-hover" style="overflow-y: scroll">
                <caption>            
                </caption>
                <thead class="table-dark">
                    <tr style="">
                        <th>Action</th>
                        <th>DIST~WHSE</th>
                        <th>SCA</th>
                        <th>Bin<br/>No.</th>
                        <th>Stock<br/>Code</th>
                        <th>Name</th>
                        <th>Cat</th>
                        <th>SOH</th>
                        <th>Tracking</th>
                        <th>Reference <br/> No.</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class='text-right'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for='(rec, recidx) in json_records'>
                        <td>
                            <a  class='btn btn-primary' v-if="rec.isType!='b2r'"
                                :href="'16_imp.php?auto_storageID='+rec.auto_storageID" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                            <a  class='btn btn-primary' v-if="rec.isType=='b2r'"
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
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
                        <td>
                            <h4 v-if="rec.isType=='b2r'"><span class='badge badge-dark' style="color:#f7fcb9">{{ rec.isType }}</span></h4>
                            <h4 v-if="rec.isType=='impq'"><span class='badge badge-dark' style="color:#9ebcda">{{ rec.isType }}</span></h4>
                            <h4 v-if="rec.isType=='imps'"><span class='badge badge-dark' style="color:#fde0dd">{{ rec.isType }}</span></h4>
                        </td>
                        <td> 
                            <h4><span :class="'badge badge-'+getStatusColor(rec)">{{getStatusCode(rec)}}~{{rec.findingID ? json_is_settings[rec.findingID].fAbr : '' }}</span></h4>
                            <!-- <h4 v-if="!rec.findingID"><span class='badge badge-secondary'>NYC~</span></h4> -->
                        </td>
                        <td>
                            <a  class='btn btn-primary' v-if="rec.isType!='b2r'"
                                :href="'16_imp.php?auto_storageID='+rec.auto_storageID" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                            <a  class='btn btn-primary' v-if="rec.isType=='b2r'"
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
        milisEnabled:[2,3,5,6]
    },
    created() {
        this.get_is_records()
        this.get_is_settings()
    },
    methods:{
        getStatusColor(rec){
            if(rec.findingID){
                if((this.milisEnabled.findIndex(v=>{return v==rec.findingID;}) >= 0) && (rec.checked_to_milis==0)){
                    return 'warning';
                }else{
                    return 'success';
                }
            }else{
                return 'secondary';
            }
        },
        getStatusCode(rec){
            if(rec.findingID){
                if((this.milisEnabled.findIndex(v=>{return v==rec.findingID;}) >= 0) 
                    && (rec.checked_to_milis==0)){
                    return 'NYC';
                }else{
                    return 'FIN';
                }
            }else{
                return 'NYC';
            }
        },
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