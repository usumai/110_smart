<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php



// Ascertain what type of stocktake is active stocktake or impairment
// THen add a filter to hide the ability to enable including the other kind


function fnClNum($fv){
    $fv = (empty($fv) ? 0 : $fv);
    $fv = (is_nan($fv) ? 0 : $fv);
    return $fv;
}

function fnPerc($tot,$sub){
    $tot = fnClNum($tot);
    $sub = fnClNum($sub);
    if($tot>0){
        $perc = $sub/$tot;
        $perc = round(($perc*100),2);
    }else{
        $perc = 0;
    }
    return $perc;
}

$page_stk_id        = 0;
$flag_merge_enabled = 1;
$merge_count        = 0;

$sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $system_stk_type = $row["stk_type"];
    }}
if(empty($system_stk_type)) {
    $system_stk_type = "any";
}


$rw_stk = "";
$sql = "SELECT * FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stkm_id            = $row["stkm_id"];
        $stk_id             = $row["stk_id"];
        $stk_name           = $row["stk_name"];
        $dpn_extract_date   = $row["dpn_extract_date"];
        $dpn_extract_user   = $row["dpn_extract_user"];
        $smm_extract_date   = $row["smm_extract_date"];
        $smm_extract_user   = $row["smm_extract_user"];
        $smm_delete_date    = $row["smm_delete_date"];
        $smm_delete_user    = $row["smm_delete_user"];
        $stk_include        = $row["stk_include"];
        $journal_text       = $row["journal_text"];
        $rowcount_original  = $row["rowcount_original"];
        $stk_type           = $row["stk_type"];
        $merge_lock         = $row["merge_lock"];


        $stk_name = substr($stk_name,0,50);

        if ($stk_include==1) {
            $flag_included  = $icon_spot_green;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Exclude this activity</a>";
            $btn_archive = "";
        }else{
            $flag_included  = $icon_spot_grey;
            $btn_toggle = "<a class='dropdown-item' href='05_action.php?act=save_stk_toggle&stkm_id=".$stkm_id."'>Include this activity</a>";
            $btn_archive = "<a class='dropdown-item' href='05_action.php?act=save_archive_stk&stkm_id=$stkm_id'>Archive</a>";
        }
        // echo "<br><br><br><br>Iterant type:".$stk_type." - System type:".$system_stk_type;
        if($system_stk_type==$stk_type){
            if ($stk_include==1) {
                $page_stk_id        = ($page_stk_id==0 ? $stk_id : $page_stk_id);//Set the first page stk
                $flag_merge_enabled = ($page_stk_id==$stk_id ? $flag_merge_enabled : 0);//If any of the enabled stocktakes are different to the first one, then disable merge
                $merge_count        = ($page_stk_id==$stk_id ? ++$merge_count : $merge_count);//Keep a count of how many stocktakes with the same number there are
            }
        }elseif($system_stk_type=="any"){
        }else{
            $btn_toggle = "<span class='dropdown-item'>Cannot include mixed activity types</span>";
        }


        if($merge_lock==1){
            $btn_toggle = "<a class='dropdown-item' href='20_merge.php?stkm_id=".$stkm_id."'>Complete merge activity</a>";
        }


        $stk_type = ucfirst($stk_type);

        $btn_excel = "<a class='dropdown-item' href='05_action.php?act=get_excel&stkm_id=$stkm_id'>Output to excel</a>";
        $btn_export = "<a class='dropdown-item' href='05_action.php?act=get_export_stk&stkm_id=$stkm_id'>Export Stocktake</a>";
        $btn_action     = " <div class='dropdown'>
                                <button class='btn btn-outline-dark dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>
                                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                    $btn_toggle $btn_export $btn_excel $btn_archive
                                </div>
                            </div>";




        if ($stk_type=="Impairment"){
            //Get impairment stats
            $sql = "SELECT 
                        count(*) AS rc_imp_total,
                        sum(CASE WHEN res_create_date IS NOT NULL	AND isBackup IS NULL 	THEN 1 ELSE 0 END) AS rc_imp_complete_primary,
                        sum(CASE WHEN res_create_date IS NOT NULL	AND isBackup = 1		THEN 1 ELSE 0 END) AS rc_imp_complete_backup,
                        sum(CASE WHEN res_create_date IS NULL		AND isBackup IS NULL 	THEN 1 ELSE 0 END) AS rc_imp_incomplete_primary,
                        sum(CASE WHEN res_create_date IS NULL 		AND isBackup = 1 		THEN 1 ELSE 0 END) AS rc_imp_incomplete_backup
                    FROM
                        smartdb.sm18_impairment
                    WHERE stkm_id=$stkm_id
                    AND isType='imp'";
            $result2 = $con->query($sql);
            if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $rc_imp_total               = $row2["rc_imp_total"];
                $rc_imp_complete_primary    = $row2["rc_imp_complete_primary"];
                $rc_imp_complete_backup     = $row2["rc_imp_complete_backup"];
                $rc_imp_incomplete_primary  = $row2["rc_imp_incomplete_primary"];
                $rc_imp_incomplete_backup   = $row2["rc_imp_incomplete_backup"];
            }}
            //Get b2r stats
            $sql = "SELECT 
                        COUNT(*) AS rc_b2r_total,
                        SUM(CASE WHEN findingID IS NOT NULL AND isBackup IS NULL THEN 1 ELSE 0 END) AS rc_b2r_complete_primary,
                        SUM(CASE WHEN findingID IS NOT NULL AND isBackup IS NULL THEN 1 ELSE 0 END) AS rc_b2r_complete_backup,
                        SUM(CASE WHEN findingID IS NULL AND isBackup IS NULL THEN 1 ELSE 0 END) AS rc_b2r_incomplete_primary,
                        SUM(CASE WHEN findingID IS NULL AND isBackup =1 THEN 1 ELSE 0 END) AS rc_b2r_incomplete_backup
                    FROM
                        (
                            SELECT BIN_CODE, findingID, isBackup
                            FROM smartdb.sm18_impairment
                            WHERE stkm_id=$stkm_id
                            AND isType='b2r'
                            GROUP BY BIN_CODE, findingID, isBackup
                        ) AS vtGroup";
            $result2 = $con->query($sql);
            if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $rc_b2r_total               = $row2["rc_b2r_total"];
                $rc_b2r_complete_primary    = $row2["rc_b2r_complete_primary"];
                $rc_b2r_complete_backup     = $row2["rc_b2r_complete_backup"];
                $rc_b2r_incomplete_primary  = $row2["rc_b2r_incomplete_primary"];
                $rc_b2r_incomplete_backup   = $row2["rc_b2r_incomplete_backup"];
            }}
            
            $sql = "SELECT COUNT(*) AS rc_b2r_inv_total
                    FROM smartdb.sm18_impairment
                    WHERE stkm_id=$stkm_id
                    AND isType='b2r'
                    AND isBackup IS NULL
                    AND isChild=1";
            $result2 = $con->query($sql);
            if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $rc_b2r_inv_total   = $row2["rc_b2r_inv_total"];
            }}

            // fnClNum($fv)
            $rowcount_firstfound = 0;

            $rc_imp_primary_total = $rc_imp_complete_primary + $rc_imp_incomplete_primary;
            $perc_complete_imp = fnPerc($rc_imp_primary_total,$rc_imp_complete_primary);


            $rc_b2r_primary_total = $rc_b2r_complete_primary + $rc_b2r_incomplete_primary;
            $perc_complete_b2r = fnPerc($rc_b2r_primary_total,$rc_b2r_complete_primary);

            $rw_stk .= " <tr>
                            <td>$stkm_id</td>
                            <td>$stk_type</td>
                            <td>$flag_included</td>
                            <td>$stk_id</td>
                            <td>$stk_name<span class='float-right'>Impairment:</span></td>
                            <td align='right'>$rc_imp_primary_total</td>
                            <td align='right'>$rc_imp_complete_primary</td>
                            <td align='right'>$perc_complete_imp%</td>
                            <td align='right'>NA</td>
                            <td align='right'>NA</td>
                            <td align='right'></td>
                        </tr>";
            $rw_stk .= " <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><span class='float-right'>B2R:</span></td>
                            <td align='right'>$rc_b2r_primary_total</td>
                            <td align='right'>$rc_b2r_complete_primary</td>
                            <td align='right'>$perc_complete_b2r%</td>
                            <td align='right'>$rc_b2r_inv_total</td>
                            <td align='right'>NA</td>
                            <td align='right'>$btn_action</td>
                        </tr>";


        }elseif ($stk_type=="Stocktake") {
            $sql = "SELECT 
                        sum(CASE WHEN first_found_flag = 1 THEN 1 ELSE 0 END) AS rowcount_firstfound,
                        sum(CASE WHEN res_completed = 1 THEN 1 ELSE 0 END) AS rowcount_completed,
                        sum(CASE WHEN rr_id IS NOT NULL THEN 1 ELSE 0 END) AS rowcount_other
                    FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id AND delete_date IS NULL";
            $result2 = $con->query($sql);
            if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $rowcount_firstfound    = $row2["rowcount_firstfound"];
                $rowcount_completed     = $row2["rowcount_completed"];
                $rowcount_other         = $row2["rowcount_other"];
            }}
            $perc_complete = round((($rowcount_completed/($rowcount_original+$rowcount_firstfound+$rowcount_other))*100),2);

            $rw_stk .= "<tr>
                        <td>$stkm_id</td>
                        <td>$stk_type</td>
                        <td>$flag_included</td>
                        <td>$stk_id</td>
                        <td>$stk_name</td>
                        <td align='right'>$rowcount_original</td>
                        <td align='right'>$rowcount_completed</td>
                        <td align='right'>$perc_complete%</td>
                        <td align='right'>$rowcount_firstfound</td>
                        <td align='right'>$rowcount_other</td>
                        <td align='right'>$btn_action</td>
                    </tr>";
        }



        
        
}}
$btnMerge = "";
if ($merge_count==2&&$flag_merge_enabled==1){
    $btnMerge = "<a href='05_action.php?act=save_merge_initiate' class='btn btn-outline-primary float-right'>Merge</a>";
}
// echo "<br><br><br><br>Merge enabled:$flag_merge_enabled";
// echo "<br><br><br><br>Merge count:$merge_count";
?>

<style>
    #myProgress {
        width: 100%;
        background-color: #ddd;
    }
    #myBar {
        width: 1%;
        height: 30px;
        background-color: #4CAF50;
    }
</style>

<script type="text/javascript">
$(document).ready(function() {
    $('#area_upload_status').hide();
    $('#fileToUpload').change(function(){
        let filename = $(this).val();
        if (filename) {
            $('#btn_submit_upload').show();    
        }else{
            $('#btn_submit_upload').hide();    
        }
    });
    $('#btn_submit_upload').click(function(){
        $('#area_upload_status').show();    
        $('#form_upload').hide();
        check_upload_progress();
    });

    function check_upload_progress(){
        // do whatever you like here
        $.get( {
            url: "05_action.php",
            data: {
                act: "get_check_upload_rr"
            },
            success: function( data ) {
                console.log(data)
                $("#upload_count").text(data+" records uploaded");
            }
        });
        setTimeout(check_upload_progress, 1000);//1000 = 1 sec
    }



});
</script>
<main role="main" class="flex-shrink-0">
	<div class="container">
		<h1 class="mt-5 display-4">SMART Mobile</h1>
	</div>
</main>

<div class="container">
    <table id="table_assets" class="table">
            <tr>
                <td>ID</td>
                <td>Type</td>
                <td>Included</td>
                <td>ID</td>
                <td>Name</td>
                <td align='right'>Orig</td>
                <td align='right'>Completed</td>
                <td align='right'>Status</td>
                <td align='right'>FF</td>
                <td align='right'>RR</td>
                <td align='right'>Action</td>
            </tr>
        <tbody>
            <?=$rw_stk?>
        </tbody>
    </table>
    <?=$btnMerge?>
    <br><hr>
    <form action="05_action.php" method="post" enctype="multipart/form-data" id="form_upload">
        <h5 class="card-title">Upload file</h5>
        <h6 class="card-subtitle mb-2 text-muted">Stocktake and Raw Remainder</h6>
        <p class="card-text">
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
        </p>
        <input type="hidden" name="act" value="upload_file">
        <input type="submit" value="Upload File" name="submit" class="btn btn-link" id="btn_submit_upload" style="display:none">
    </form>
    <span id="area_upload_status" style="display:none!important">
        <div class="spinner-border" role="status" id='loading_spinner' style="width: 3rem; height: 3rem;">
            <span class="sr-only" style="">Loading...</span>
        </div>
        <span id="upload_count"></span>
    </span>

    

</div>
<?php include "04_footer.php"; ?>