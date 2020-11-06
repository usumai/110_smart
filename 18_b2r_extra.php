<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php include "components/forminput.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <div class='row'>
            <div class='col'>
                <h1 class="display-4">
                    Extra investigation: {{ json_b2r_record.STOCK_CODE }}
                    <a  class='btn btn-outline-dark float-right' 
                        :href="'17_b2r.php?stkm_id='+json_b2r_record.stkm_id+'&BIN_CODE='+json_b2r_record.BIN_CODE" >Back</a>
                </h1>
            </div>
        </div>

        <div class='row'>
            <div class='col lead'>
                
                <ul class="list-group list-group-flush text-center">
                    <div>
                        <div v-for="(trailval, trailidx) in json_path">
                            <li class='list-group-item'><b>{{ json_questions[trailidx]['name'] }}</b></li>
                            <li class='list-group-item'
                                style="padding-top:3px;padding-bottom:3px" 
                                :class="{'list-group-item-success':trailval=='Yes','list-group-item-danger':trailval=='No'}">
                                <b>
                                    {{ trailval }}
                                    <button class="btn btn-outline-dark" v-on:click="select_repeal(trailidx)">X</button>
                                </b></li>
                        </div>
                    </div>
                    <div v-if="qres!='nstr'&&qres!='LE'&&qres!='FF'">
                        <li class='list-group-item'><b>{{ json_questions[qres]['name'] }}</b></li>
                        <button class='list-group-item list-group-item-action list-group-item-success'
                                v-on:click="select_answer('Yes')">Yes</button>
                        <button class='list-group-item list-group-item-action list-group-item-danger'
                                v-on:click="select_answer('No')">No</button>
                    </div>
                    <div>
                        <h1 v-if="qres=='nstr'||qres=='LE'||qres=='FF'" class="display-4">Final result: {{qres == 'nstr' ? 'No Further Investigation Required' : qres }}</h1>
                        <span>
                            <button class="btn btn-danger" 
                                v-on:click="save_final_b2r_extra_result('clear')">
                                Clear result
                            </button>
                            <button class="btn btn-dark" 
                                v-if="qres=='nstr'||qres=='LE'||qres=='FF'"
                                v-on:click="save_final_b2r_extra_result()">
                                Save
                            </button>
                        </span>
                    </div>
                </ul>  
            </div>
        </div>

        <div v-if="dev"><hr>
            <h1 class="display-4">Developer data</h1>
            <div class="row">
                <div class="col-3">json_questions<pre>{{ json_questions }}</pre></div>
                <div class="col-3">json_path<pre>{{ json_path }}</pre></div>
                <div class="col-3">qres<pre>{{ qres }}</pre></div>
                <!-- <div class="col-3">current_question_or_res_idx<pre>{{ current_question_or_res_idx }}</pre></div> -->
                <div class="col-3">json_b2r_record<pre>{{ json_b2r_record }}</pre></div>
            </div>
        </div>
    </div>

</div>

<script>
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

let vm = new Vue({
    el: '#app',
    data: {
        dev:false,
        auto_storageID:"<?=$_GET["auto_storageID"]?>",
        json_path:{},
        // current_question_or_res_idx:false,
        json_b2r_record:{},
        qres:false,
        finalResult:false,
        json_questions: {
            1:{
                name:   "Is the item a commonwealth asset?",
                Yes:"2",
                No: "nstr",
            },
            2:{
                name:   "Does the item belong to a SCA?",
                Yes:"nstr",
                No: "3",
            },
            3:{
                name:   "Check dues in/out status. Check if the item was reciepted 72 hours pre-NAIS stocktake.",
                Yes:"nstr",
                No: "4",
            },
            4:{
                name:   "Verify inventory category. Does it fall under an exclusion list?",
                Yes:"nstr",
                No: "5",
            },
            5:{
                name:   "Is the item serial tracked?",
                Yes:"6",
                No: "7",
            },
            6:{
                name:   "Is the serial tracked item(s) CURRENTLY held anywhere in the warehouse? Check the warehouse against the district.",
                Yes:"LE",
                No: "FF",
            },
            7:{
                name:   "Is the quantity tracked item(s) CURRENTLY held anywhere in the warehouse? Check the warehouse against the district.",
                Yes:"8",
                No: "FF",
            },
            8:{
                name:   "Is the physical SOH different to 1RB or 1RS? Conduct muster where applicable",
                Yes:"9",
                No: "FF",
            },
            9:{
                name:   "Is the physical SOH different the same as the 1RB or 1RS?",
                Yes:"LE",
                No: "FF",
            },
        }
    },
    created() {
        this.get_b2r_bin()
    },
    methods:{
        
        get_b2r_bin(){
            payload             = {'act':'get_b2r_bin', 'auto_storageID':this.auto_storageID}
            json                = fnapi(payload)
            this.json_b2r_record= json[0]
            console.log("json_b2r_record")
            console.log(this.json_b2r_record)
            if (this.json_b2r_record.finalResultPath){
                this.json_path = JSON.parse(this.json_b2r_record.finalResultPath)
                this.build_path()
            }else{
                this.json_path  = {}
                this.qres       = 1
            }            
        }, 
        build_path(){
            end_loop = false
            trailidx = 1
            counter=0
            do {
                counter++; //To stop infinite loops

                answer = this.json_path[trailidx]
                console.log("Trail step:"+trailidx);console.log("answer:"+answer);console.log("qres:"+this.qres)
                if (answer){//An answer at this level has been provided
                    next_step = this.json_questions[trailidx][answer]
                    this.qres = next_step
                    if (next_step=='nstr'||next_step=='LE'||next_step=='FF'){//This answer is a result and does not lead to another step
                        this.finalResult = next_step
                        end_loop = true
                    }else{//This answer is not a final result, but leads to another question
                        trailidx = next_step
                    }
                }else{// An answer has not been provided
                    // Present current question
                    // this.current_question_or_res_idx = trailidx
                    // end the loop
                    end_loop = true
                }
     
            }
            while (end_loop==false&&counter<20);
        }, 

        select_answer(answerPath){
            this.json_path[String(this.qres)] = answerPath
            this.build_path()
        }, 
        select_repeal(repeal_to_idx){
            console.log("repeal to:"+repeal_to_idx)
            if(repeal_to_idx==1){
                this.json_path  = {}
                this.qres       = 1
            }
            for(idx in this.json_path){
                if (idx>=repeal_to_idx) {
                    idx=String(idx)
                    console.log("Deleteing:"+idx)
                    delete this.json_path[idx];
                }
            }
            this.build_path()
        }, 
        save_final_b2r_extra_result(clearopt){
            if(clearopt=="clear"){
                payload = {'act':'save_final_b2r_extra_result', 'auto_storageID':this.auto_storageID, 'finalResult':clearopt, 'finalResultPath':JSON.stringify(this.json_path)}
                json    = fnapi(payload)
                this.get_b2r_bin()

            }else{
                payload = {'act':'save_final_b2r_extra_result', 'auto_storageID':this.auto_storageID, 'finalResult':this.qres, 'finalResultPath':JSON.stringify(this.json_path)}
                json    = fnapi(payload)
                window.location.replace("17_b2r.php?stkm_id="+this.json_b2r_record.stkm_id+"&BIN_CODE="+this.json_b2r_record.BIN_CODE);
            }
        }, 
    }
})
</script>

<?php include "04_footer.php"; ?>
