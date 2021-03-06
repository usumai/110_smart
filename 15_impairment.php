<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<style type="text/css">

</style>
<div id="app">
    <div class="container-fluid">
        <h1 class="mt-5 display-6">IS/B2R</h1>
        <div class="table-responsive-sm">
            <table id="tbl_stk" class="table table-sm table-striped table-border" >
                <caption>            
                </caption>
                <thead class="table-dark">
                    <tr>
                        <th>Action</th>
                        <th >DIST<br/>WHSE
                        	<div class="dropdown"  style="display: inline">
                                  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 		<span class="fas fa-sort-amount-down"></span>
                                  </a>                                
                                  <div class="dropdown-menu" aria-labelledby="warehouseFilter">
                                    <a class="dropdown-item"  v-for="(rec, i) in  filters.warehouse" @click="search(rec)">{{rec}}</a>
                                  </div>
                        	</div>                                     
                        </th>
                        <th>SCA</th>
                        <th>Bin<br/>No.
                        	<div class="dropdown"  style="display: inline">
                                  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 		<span class="fas fa-sort-amount-down"></span>
                                  </a>                                
                                  <div class="dropdown-menu" aria-labelledby="binFilter">
                                    <a class="dropdown-item"  v-for="(rec, i) in  filters.bincode" @click="search(rec)">{{rec}}</a>
                                  </div>
                        	</div>                              
                        </th>
                        <th>Stock<br/>Code
                        	<div class="dropdown"  style="display: inline">
                                  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 		<span class="fas fa-sort-amount-down"></span>
                                  </a>                                
                                  <div class="dropdown-menu" aria-labelledby="stockFilter">
                                    <a class="dropdown-item"  v-for="(rec, i) in  filters.stockcode" @click="search(rec)">{{rec}}</a>
                                  </div>
                        	</div>                          
                        </th>
                        <th>Name</th>
                        <th>Cat</th>
                        <th>SOH</th>
                        <th>Tracking</th>
                        <th>Reference <br/>No.</th>
                        <th>Type
                        	<div class="dropdown"  style="display: inline">
                                  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 		<span class="fas fa-sort-amount-down"></span>
                                  </a>                                
                                  <div class="dropdown-menu" aria-labelledby="typeFilter">
                                    <a class="dropdown-item"  v-for="(rec, i) in  filters.type" @click="search(rec)">{{rec}}</a>
                                  </div>
                        	</div>                              
                        </th>
                        <th>Status</th>
                        <th style="width: 70px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for='(rec, recidx) in json_records' :ref="rec.auto_storageID == <?=$current_row ?> ? 'current_row' : ''" :id="rec.auto_storageID == <?=$current_row ?> ? 'current_row' : ''">
                        <td>
                            <a  class='btn btn-primary' v-if="rec.isType!='b2r'"
                                :href="'16_imp.php?auto_storageID='+rec.auto_storageID" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                            <a  class='btn btn-primary' v-if="rec.isType=='b2r'"
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                        </td>
                        <td>{{rec.DSTRCT_CODE}}-{{rec.WHOUSE_ID}}</td>
                        <td>{{rec.SUPPLY_CUST_ID}}</td>
                        <td>{{rec.BIN_CODE}}</td>
                        <td>{{rec.STOCK_CODE}}</td>
                        <td>{{rec.ITEM_NAME}}</td>
                        <td>{{rec.INVENT_CAT}}</td>
                        <td>{{rec.isType != 'b2r'?rec.SOH:''}}</td>
                        <td>{{rec.TRACKING_IND}}</td>
                        <td>{{rec.TRACKING_REFERENCE}}</td>
                        <td>
                            <h4 v-if="rec.isType=='b2r'"><span class='badge badge-dark' style="color:#f7fcb9">{{ rec.isType }}</span></h4>
                            <h4 v-if="rec.isType=='impq'"><span class='badge badge-dark' style="color:#9ebcda">{{ rec.isType }}</span></h4>
                            <h4 v-if="rec.isType=='imps'"><span class='badge badge-dark' style="color:#fde0dd">{{ rec.isType }}</span></h4>
                        </td>
                        <td> 
                            <h4><span :class="'badge badge-'+getStatusColor(rec)">{{getStatusCode(rec)}}~{{rec.findingID ? json_is_settings[rec.findingID].fAbr : '' }}</span></h4>
                            <!-- <h4 v-if="!rec.findingID"><span class='badge badge-secondary'>NYC~</span></h4> -->
                        </td>
                        <td class='float-right'>
                            <a  class='btn btn-primary' v-if="rec.isType!='b2r'"
                                :href="'16_imp.php?auto_storageID='+rec.auto_storageID+'&current_row='+rec.auto_storageID" >
                                <span class='octicon octicon-zap' style='font-size:30px'></span>
                            </a>
                            <a  class='btn btn-primary' v-if="rec.isType=='b2r'"
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE+'&current_row='+rec.auto_storageID" >
                                <span class='octicon octicon-zap' style='font-size:30px'></span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
         
    </div>
</div>

<script>


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
    	error:"",
        json_records:{},
        json_is_settings:{},
        milisEnabled:[],
        updateList: 0,
        filters:{
            warehouse:[],
            stockcode:[],
            bincode: [],
            type: ['All','b2r','imp','impq','imps']
        }
    },
    created() {
    	console.log("Event Created...");
		this.updateMilisEnableFindingIDs();
        this.get_is_records()
        this.get_is_settings()
    },
    mounted() {

        console.log("Event Mounted...");
 /*       
     	if(this.$refs.current_row) {
    		this.$refs.current_row[0].scrollIntoView();
    	}    
 */  	
    },
    beforeMount(){
    	console.log("Event BeforeMount...");
    },
    beforeUpdate(){
    	console.log("Event BeforeUpdate...");
    },    
    updated(){
    	console.log("Event Updated...");
    	if(this.updateList==1){
            $('#tbl_stk').DataTable({
                stateSave: true
            });
            this.updateList=0;
    	}
     	if(this.$refs.current_row) {
         	console.log("Scrolling into view: ");
         	console.log(this.$refs.current_row);
    		this.$refs.current_row[0].scrollIntoView();
    	}    
    }, 
    beforeDestroy(){
    	console.log("Event BeforeDestroy...");
    }, 
    Destroyed(){
    	console.log("Event BeforeDestroy...");
    },               
    methods:{

        search(filterText) {
            if(filterText=='All'){
            	$('#tbl_stk').DataTable().search('');
            }else{
            	$('#tbl_stk').DataTable().search(filterText);
            }
        	
        },
		updateMilisEnableFindingIDs(){
			getMilisEnableFindingIDs(
	    		data=>this.milisEnabled=data, 
	    		errors=>{
	    			this.error= (errors && errors.length>0) ? this.error=error[0].info : '';
	    		}
	    	);
		},
        getStatusColor(rec){
            if(rec.findingID){
            	if((rec.isType=='b2r') && (rec.data_source=='skeleton') 
                	&& (rec.findingID==15)){
                	return 'warning';
            	} else if((rec.findingID==11) && (rec.isComplete != 1)){
            		return 'warning';
                } else if((this.milisEnabled.findIndex(v=>{return v==rec.findingID;}) >= 0) && (rec.checked_to_milis==0)){
                    return 'warning';
                } else if(rec.findingID==13){
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
                if((rec.isType=='b2r') && (rec.data_source=='skeleton') 
                	&& (rec.findingID==15)){
        			return 'NYC'; 
            	} else if((rec.findingID==11) && (rec.isComplete != 1)){
            		return 'NYC';	
        		} else if((this.milisEnabled.findIndex(v=>{return v==rec.findingID;}) >= 0) 
                    && (rec.checked_to_milis==0)){
                    return 'NYC'; 
                } else if(rec.findingID==13){
                    return 'NYC';   
                }else{
                    return 'FIN';
                }
            }else{
                return 'NYC';
            }
        },
        get_is_records(){
        	this.updateList=1;
        	getIsRecords(data=>{

            	
                var warehouseMap=[];
                var bincodeMap=[];
                var stockcodeMap=[];
                for(i in data){
                	warehouseMap[data[i].DSTRCT_CODE+'-'+data[i].WHOUSE_ID]=1;
                    bincodeMap[data[i].BIN_CODE]=1;
                    stockcodeMap[data[i].STOCK_CODE]=1;
                }
                var i=0;
                this.filters.warehouse[i++]='All';
                for(var key in warehouseMap){
                    this.filters.warehouse[i++]=key;
                }
                i=0;
                this.filters.bincode[i++]='All';
                for(var key in bincodeMap){
                    this.filters.bincode[i++]=key;
                }
                i=0;
                this.filters.stockcode[i++]='All';
                for(var key in stockcodeMap){
                    this.filters.stockcode[i++]=key;
                }
            	this.json_records=data;


            	console.log(this.json_records);
            },errors=>{
                this.error=errors[0].info;
            });
            
        }, 
        get_is_settings(){
            payload                 = {'act':'get_is_settings'}
            json   = fnapi(payload)
            this.json_is_settings = [];
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