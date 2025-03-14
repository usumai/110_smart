<?php include "02_header.php"; ?>
<div id="app" class="container-fluid">

    		<h1 class="mt-4 display-4">IS/B2R</h1> 		
        	<div class="table-responsive-sm">	    
        	<table id="tbl_stk" class="table table-sm table-striped" >
                <caption>            
                </caption>
                <thead class="table-dark sticky-top">
        			<tr>
        				<th class="align-top w-5">Action</th>
        				<th class="align-top ">DIST<br/>WHSE
        					<div class="dropdown"  style="display: inline">
        						  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        								<span class="fas fa-sort-amount-down"></span>
        						  </a>                                
        						  <div class="dropdown-menu" aria-labelledby="warehouseFilter">
        							<a class="dropdown-item"  v-for="(rec, i) in  filters.warehouse" @click="search(0,rec)">{{rec}}</a>
        						  </div>
        					</div>                                     
        				</th>
        				<th class="align-top ">SCA<br/>      					
        					<div class="dropdown"  style="display: inline">
        						  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        								<span class="fas fa-sort-amount-down"></span>
        						  </a>                                
        						  <div class="dropdown-menu" aria-labelledby="binFilter">
        							<a class="dropdown-item"  v-for="(rec, i) in  filters.sca" @click="search(1,rec)">{{rec}}</a>
        						  </div>
        					</div>           				
        				</th>
        				<th class="align-top ">Bin<br/>No.
        					<div class="dropdown"  style="display: inline">
        						  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        								<span class="fas fa-sort-amount-down"></span>
        						  </a>                                
        						  <div class="dropdown-menu" aria-labelledby="binFilter">
        							<a class="dropdown-item"  v-for="(rec, i) in  filters.bincode" @click="search(1,rec)">{{rec}}</a>
        						  </div>
        					</div>                              
        				</th>
        				<th class="align-top ">Stock<br/>Code
        					<div class="dropdown"  style="display: inline">
        						  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        								<span class="fas fa-sort-amount-down"></span>
        						  </a>                                
        						  <div class="dropdown-menu" aria-labelledby="stockFilter">
        							<a class="dropdown-item"  v-for="(rec, i) in  filters.stockcode" @click="search(2,rec)">{{rec}}</a>
        						  </div>
        					</div>                          
        				</th>
        				<th class="align-top ">Name</th>
        				<th class="align-top ">Cat</th>
        				<th class="align-top ">SOH</th>
        				<th class="align-top ">Tracking</th>
        				<th class="align-top">Reference <br/>No.</th>
        				<th class="align-top ">Type<br/>
        					<div class="dropdown"  style="display: inline">
        						  <a class="btn-outline" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        								<span class="fas fa-sort-amount-down"></span>
        						  </a>                                
        						  <div class="dropdown-menu" aria-labelledby="typeFilter">
        							<a class="dropdown-item"  v-for="(rec, i) in  filters.type" @click="search(3,rec.option)">{{rec.label}}</a>
        						  </div>
        					</div>                              
        				</th>
        				<th class="align-top ">Status</th>
        				<th class="align-top " style="width: 70px">Action</th>
        			</tr>
                </thead>
                <tbody>
                    <tr v-for='(rec, recidx) in json_records' :class="rec.auto_storageID==<?=$current_row ?>?'current-row':''" :ref="rec.auto_storageID == <?=$current_row ?> ? 'current_row' : ''" :id="rec.auto_storageID == <?=$current_row ?> ? 'current_row' : ''">
                        <td class="w-5">
                            <a  class='btn btn-primary' v-if="rec.isType!='b2r'"
                                :href="'16_imp.php?auto_storageID='+rec.auto_storageID" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                            <a  class='btn btn-primary' v-if="rec.isType=='b2r'"
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE.replace('&','%26')" ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
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
                            <h4 v-if="rec.isType=='b2r_exc'"><span class='badge badge-dark' style="color:orange">{{ rec.isType }}</span></h4>
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
                                :href="'17_b2r.php?stkm_id='+rec.stkm_id+'&BIN_CODE='+rec.BIN_CODE.replace('&','%26')+'&current_row='+rec.auto_storageID" >
                                <span class='octicon octicon-zap' style='font-size:30px'></span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
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
        filterColumns: [],
        filters:{
            warehouse:[],
            sca:[],
            stockcode:[],
            bincode: [],
            type: [
                	{option: 'All', label: 'All'},
                	{option: 'b2r', label: 'b2r'},
                	{option: 'impq', label:'impq'},
                	{option: 'imps', label: 'imps'},
                	{option: 'imp', label: 'impq+imps'}
                 ]
        }
    },
    created() {
		this.updateMilisEnableFindingIDs();
        this.get_is_records()
        this.get_is_settings()
    },
    mounted() {
      
     	if(this.$refs.current_row) {
    		this.$refs.current_row[0].scrollIntoView({
        		behavior: 'smooth',
        		block: 'end',
        		inline: 'start'
            });
    	}    
   	
    },
    beforeMount(){
    },
    beforeUpdate(){
    },    
    updated(){
        
    	if(this.updateList==1){
            $('#tbl_stk').DataTable({
                stateSave: true,
                ordering: true,
                paging: true,
                info: true,
                /*lengthMenu: [],*/
                orderMulti: true,
                order: [[1, 'asc']], 
                pagingType: 'simple_numbers',     
                columnDefs:[
                    { 
                        targets: [1], 
                    	orderData: [1,3,4] 
                	}
                ],   
                dom: '<"row"<"col"><"col"f>><"row"<"col"l><"col"p>><"row"<"col"t>><"row"<"col"i><"col"p>>'    
            });
            this.updateList=0;
    	}
     	if(this.$refs.current_row) {
    		this.$refs.current_row[0].scrollIntoView({
        		behavior: 'smooth',
        		block: 'end',
        		inline: 'start'
            });
    	}    
    }, 
    beforeDestroy(){

    }, 
    Destroyed(){
    },               
    methods:{

        search(col, filterText) {
            if(filterText=='All'){
            	this.filterColumns[col]='';
            }else{
                this.filterColumns[col]=filterText;
            }
            var filter="";
            this.filterColumns.forEach(val =>{ filter = filter+' '+val; });
            $('#tbl_stk').DataTable().search(filter);
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
                var scaMap=[];
                for(i in data){
                	warehouseMap[data[i].DSTRCT_CODE+'-'+data[i].WHOUSE_ID]=1;
                	scaMap[data[i].SUPPLY_CUST_ID]=1;
                    bincodeMap[data[i].BIN_CODE]=1;
                    stockcodeMap[data[i].STOCK_CODE]=1;
                }

                var sorter=function (v1,v2){
                    if(v1.length>v2.length){
                        v1=v1.substring(0,v2.length);
                    }else if(v1.length<v2.length){
						v2=v2.substring(0,v1.length);
                    }
                    if(v1.toUpperCase() > v2.toUpperCase()){
                        return 1;
                    }else if(v1.toUpperCase() < v2.toUpperCase()){
                        return -1;
                    }else{
                        return 0;
                    }
            	};               
                var i=0;
                for(var key in warehouseMap){
                    this.filters.warehouse[i++]=key;
                }
                this.filters.warehouse.sort(sorter);
                this.filters.warehouse.splice(0,0,'All');

                i=0;
                for(var key in scaMap){
                    this.filters.sca[i++]=key;
                }
                this.filters.sca.sort(sorter);
                this.filters.sca.splice(0,0,'All');
                
                i=0;
                for(var key in bincodeMap){
                    this.filters.bincode[i++]=key;
                }
                this.filters.bincode.sort(sorter);
                this.filters.bincode.splice(0,0,'All');
                
                i=0;
                
                for(var key in stockcodeMap){
                    this.filters.stockcode[i++]=key;
                }
                this.filters.stockcode.sort(sorter);
                this.filters.stockcode.splice(0,0,'All');
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
        }, 
    }
})
</script>

<?php include "04_footer.php"; ?>