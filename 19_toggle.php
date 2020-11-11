<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$sqlInclude = "
	SELECT stkm_id 
	FROM smartdb.sm13_stk 
	WHERE stk_include=1 AND smm_delete_date IS NULL";


$rws = '';
$sql = "
	SELECT 
		isType, 
		targetID, 
		stkm_id, 
		DSTRCT_CODE, 
		WHOUSE_ID, 
		SUPPLY_CUST_ID, 
		STOCK_CODE, 
		ITEM_NAME, 
		isBackup, 
		COUNT(*) AS targetItemCount 
	FROM smartdb.sm18_impairment 
	WHERE 
		stkm_id IN ($sqlInclude) AND 
		(LEFT(isType,3)='imp') AND 
		((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00')) 
	GROUP BY 
		isType, 
		targetID, 
		stkm_id, 
		DSTRCT_CODE, 
		WHOUSE_ID, 
		SUPPLY_CUST_ID, 
		STOCK_CODE, 
		ITEM_NAME, 
		isBackup"; 
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {   
        $isType             = $row['isType'];
        $targetID           = $row['targetID']; 
        $stkm_id            = $row['stkm_id'];    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $targetItemCount    = $row['targetItemCount'];
        $isBackup           = $row['isBackup'];

        $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=1' class='btn btn-outline-dark'>Primary</a>";
        if($isBackup==1){
            $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=0' class='btn btn-dark'>Backup</a>";
        }
        
        $btnType = "<span class='badge badge-info'>IMP</span>";
        
        $rws.="<tr><td>$btnType</td><td>$targetID</td><td>$DSTRCT_CODE</td><td>$WHOUSE_ID</td><td>$SUPPLY_CUST_ID</td><td></td><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td>$targetItemCount</td><td class='float-right'>$btnBackup</td></tr>";
}}


$sql = "
	SELECT 
		isType, 
		targetID, 
		stkm_id, 
		DSTRCT_CODE, 
		WHOUSE_ID, 
		SUPPLY_CUST_ID, 
		BIN_CODE,	
		STOCK_CODE, 
		ITEM_NAME,  
		isBackup, 
		COUNT(*) AS targetItemCount 
	FROM smartdb.sm18_impairment 
	WHERE 
		stkm_id IN ($sqlInclude) AND 
		(isType='b2r') AND 
		(data_source='skeleton') AND
		((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))  
	GROUP BY 
		isType, 
		targetID, 
		stkm_id, 
		DSTRCT_CODE, 
		WHOUSE_ID, 
		SUPPLY_CUST_ID, 
		BIN_CODE, 
		STOCK_CODE, 
		ITEM_NAME, 
		isBackup";   
		
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $stkm_id            = $row['stkm_id'];    
        $DSTRCT_CODE        = $row['DSTRCT_CODE'];
        $WHOUSE_ID          = $row['WHOUSE_ID'];
        $SUPPLY_CUST_ID     = $row['SUPPLY_CUST_ID'];
        $BIN_CODE           = $row['BIN_CODE'];
        $isType             = $row['isType'];
        $targetID           = $row['targetID'];
        $targetItemCount    = $row['targetItemCount'];
        $isBackup           = $row['isBackup'];
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $BIN_CODE_code = str_replace("&","%26",$BIN_CODE);
        $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&BIN_CODE=$BIN_CODE_code&isType=$isType&isBackup=1' class='btn btn-outline-dark'>Primary</a>";
        if($isBackup==1){
            $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&BIN_CODE=$BIN_CODE_code&isType=$isType&isBackup=0' class='btn btn-dark'>Backup</a>";
        }
        
        $btnType = "<span class='badge badge-primary'>B2R</span>";
        
        $rws.="<tr><td>$btnType</td><td>$targetID</td><td>$DSTRCT_CODE</td><td>$WHOUSE_ID</td><td>$SUPPLY_CUST_ID</td><td>$BIN_CODE</td><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td>$targetItemCount</td><td class='float-right'>$btnBackup</td></tr>";
}}


$sqlStats = "SELECT 
	SUM(CASE WHEN (
					(LEFT(isType,3)='imp') AND 
					((isBackup IS NULL) OR (isBackup=0)) AND 
					((res_create_date IS NOT null) AND (date(res_create_date) <> '0000-00-00'))
				   ) 
		THEN 1 ELSE 0 END
	) AS impPrimeComplete, 
	SUM(CASE WHEN (
					(LEFT(isType,3)='imp') AND 
					(isBackup IS NULL or isBackup=0)) 
		THEN 1 ELSE 0 END
	) AS impPrimeTotal,
	SUM(CASE WHEN (
					(LEFT(isType,3)='imp') AND 
					(isBackup=1) AND 
					((res_create_date IS NOT null) AND (date(res_create_date) <> '0000-00-00'))
				 	)
		THEN 1 ELSE 0 END
	) AS impBackupComplete,
	SUM(CASE WHEN (
					(LEFT(isType,3)='imp') AND 
					(isBackup=1)) 
		THEN 1 ELSE 0 END
	) AS impBackupTotal,
	SUM(CASE WHEN (
					(isType='b2r') AND 
					(data_source='skeleton') AND
					(isBackup IS NULL or isBackup=0) AND 				
					((res_create_date IS NOT null) AND (date(res_create_date) <> '0000-00-00'))
					)
		THEN 1 ELSE 0 END
	) AS b2rPrimeComplete,
	SUM(CASE WHEN (
					(isType='b2r') AND 
					(data_source='skeleton') AND
					(isBackup IS NULL or isBackup=0)) 
		THEN 1 ELSE 0 END
	) AS b2rPrimeTotal,
	SUM(
		CASE WHEN (	(isType='b2r') AND 
					(data_source='skeleton') AND
					(isBackup=1) AND 
					((res_create_date IS NOT null) AND (date(res_create_date) <> '0000-00-00'))
					) 
		THEN 1 ELSE 0 END
	) AS b2rBackupComplete,
	SUM(CASE WHEN (
					(isType='b2r') AND 
					(data_source='skeleton') AND
					(isBackup=1)) 
		THEN 1 ELSE 0 END
	) AS b2rBackupTotal
	FROM smartdb.sm18_impairment
	WHERE stkm_id IN ($sqlInclude)";
// $sql .= " LIMIT 500; ";   
// echo "<br><br><br>".$sqlStats;
$result = $con->query($sqlStats);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $impPrimeComplete        = $row['impPrimeComplete'];
        $impPrimeTotal        = $row['impPrimeTotal'];
        $impBackupComplete        = $row['impBackupComplete'];
        $impBackupTotal        = $row['impBackupTotal'];
        $b2rPrimeComplete        = $row['b2rPrimeComplete'];
        $b2rPrimeTotal        = $row['b2rPrimeTotal'];
        $b2rBackupComplete        = $row['b2rBackupComplete'];
        $b2rBackupTotal        = $row['b2rBackupTotal'];
    }}

$stats = "<tr><td></td><td colspan='2'><strong>Impairment</strong></td><td colspan='2'><strong>Bin to Register</strong></td></tr>";
$stats .= "<tr><td></td><td>Complete</td><td>Total</td><td>Complete</td><td>Total</td></tr>";
$stats .= "<tr><td>Primary</td><td>$impPrimeComplete</td><td>$impPrimeTotal</td><td>$b2rPrimeComplete</td><td>$b2rPrimeTotal</td></tr>";
$stats .= "<tr><td>Backup</td><td>$impBackupComplete</td><td>$impBackupTotal</td><td>$b2rBackupComplete</td><td>$b2rBackupTotal</td></tr>";
$statsTbl = "<table class='table table-sm'>$stats</table>";


?>

<script type="text/javascript">
//Declare other global variables
let dispQtrack,dispStrack,complete;

$(document).ready(function() {
    
    $('#table_backup').DataTable({
        stateSave: true
    });
});
</script>


<style>
.list-group-item{
    margin-bottom:10px;
}
</style>



<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Toggle items between primary and backup</h1>
    </div>
</div>


<div class='row'>
    <div class='col-6'>
        <?=$statsTbl?>
    </div>
</div>






<div class='row'>
    <div class='col lead table-responsive-sm'> 
        <table id='table_backup' class='table table-sm table-striped table-hover'>
            <thead class="table-dark">
                <tr>
                    <th>Type</th>
                    <th>TargetID</th>
                    <th>DIST</th>
                    <th>WHSE</th>
                    <th>SCA</th>
                    <th>BIN_CODE</th>
                    <th>Stockcde</th>
                    <th>Name</th>
                    <th>Count</th>
                    <th class='text-right'>Action</th>
                </tr>
            </thead>
            <tbody>
                <?=$rws?>
            </tbody>
        </table>
    </div>
</div>


<?php include "04_footer.php"; ?>