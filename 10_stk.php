<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<div id="app">
    <div class='container-fluid'>
        <h1 class="mt-5 display-4">General Assets Stocktake</h1>

        <h2>{{ stk_progressd.count_complete }}/ {{ stk_progressd.count_total }} total ( perc_complete )&nbsp;</h2>

        <button class='btn btn-outline-dark' v-on:click="stk_table_search('~FF')">First found</button>
        <button class='btn btn-outline-dark' v-on:click="stk_table_search('FIN~')">Completed</button>
        <button class='btn btn-outline-dark' v-on:click="stk_table_search('NYC~')">Incomplete</button>
        <button class='btn btn-warning' v-on:click="stk_table_search('clear')">Clear search terms</button>
        <span id="area_rr_count">Enter a search term greater than four characters to search the Raw Remainder dataset.</span>
        <div class="table-responsive-sm">
            <table id="tbl_stk" class="table table-sm table-striped table-hover">
                <thead class="table-dark">
                    <tr >
                        <th>Action<br>&nbsp;</th>
                        <th class="text-center">AssetID<br>Class</th>
                        <!-- <th>Inventory</th> -->
                        <th class="text-center">Location<br>Room</th>
                        <th>Description<br>&nbsp;</th>
                        <th class="text-center">InventNo<br>SerialNo</th>
                        <th class="text-right">$NBV<br>&nbsp;</th>
                        <th class="text-right">Custodian<br>&nbsp;</th>
                        <th class="text-center">Status<br>&nbsp;</th>
                        <th>Timestamp<br>&nbsp;</th>
                        <th>Action<br>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for='(ass, assidx) in assd'>
                    <td>
                        <a :href="'11_ass.php?ass_id='+ass.ass_id" 
                        class='btn btn-outline-dark'
                        :style="{'background-color':ass.ass_status!=1?'#ffb3b3':''}"
                        ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
                    </td>
                    <td nowrap>{{ ass.res_asset_id }}<br>c{{ ass.res_class }}</td>
                    <td>{{ ass.res_loc_location }}<br>{{ ass.res_loc_room }}</td>
                    <td>{{ ass.res_assetdesc1 }}<br><small>{{ ass.res_assetdesc2 }}</small></td>
                    <td>{{ ass.res_inventno }}<br><small>{{ ass.res_serialno }}</small></td>
                    <td class="text-right">{{ ass.res_val_nbv }}</td>
                    <td>{{ ass.res_plateno }}</td>
                    <td>
                        <!-- {{ ass.ass_status }} -->
                        <span v-if="ass.ass_status==1">FIN~</span>
                        <span v-if="ass.ass_status!=1">NYC~</span>
                        <br>{{ ass.res_reason_code }}
                    </td>
                    <td>
                        <span v-if="ass.res_create_date!='0000-00-00 00:00:00'">{{ ass.res_create_date }}</span>
                    
                    </td>
                    <td>
                        <a :href="'11_ass.php?ass_id='+ass.ass_id" 
                        class='btn btn-outline-dark float-right'
                        :style="{'background-color':ass.ass_status!=1?'#ffb3b3':''}"
                        ><span class='octicon octicon-zap' style='font-size:30px'></span></a>
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
    
    $('#tbl_stk').on('search.dt', function() {
        rr_search();
    }); 

    function rr_search() {
        var search_term = $('.dataTables_filter input').val();
        if (search_term.length>4) {
            $("#table_rr").html("");
            $.post("05_action.php",
            {
                act: "get_rawremainder_asset_count",
                search_term: search_term
            },
            function(data, status){
                $("#area_rr_count").html(data)
            });
        }else{
            $("#area_rr_count").html("Enter a search term greater than four characters to search the Raw Remainder dataset.")
        }
    }


} );

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
        message: 'System is working',
        assd:{},
        stk_progressd:{},
    },
    created() {
        this.get_ga_assets()
        this.get_stk_progress()
    },
    methods:{
        get_ga_assets(){
            payload             = {'act':'get_ga_assets'}
            this.assd           = fnapi(payload)
            console.log(this.assd)
        }, 
        get_stk_progress(){
            payload             = {'act':'get_stk_progress'}
            json                = fnapi(payload)
            this.stk_progressd  = json[0]
            completion_rate     = fnratr(this.stk_progressd.count_complete, this.stk_progressd.count_total)
            console.log(this.stk_progressd)
            console.log(completion_rate)
        }, 
        stk_table_search(search_term_new){
            let search_param = $('.dataTables_filter input').val()+" "+search_term_new;
            search_param = search_term_new=='clear' ? '' : search_param;
            $('#tbl_stk').DataTable().search(search_param).draw();
        }, 
    }
})
</script>


<script type="text/javascript">
// $(document).ready(function() {

//     var table = $('#table_assets').DataTable();
//     $('#table_assets').on('search.dt', function() {
//         rr_search();
//     }); 
//     $(".btn_search_term_clear").click(function(){
//         table.search(" ").draw();
//     });
//     rr_search();

// });
</script>

<?php include "04_footer.php"; ?>