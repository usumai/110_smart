
<?php 
include "02_header.php"; 
include "php/common/common.php";

$sqlInclude = "
	SELECT stkm_id 
	FROM smartdb.sm13_stk 
	WHERE 
		stk_include=1 AND 
		((delete_date IS NULL) OR (date(delete_date)='0000-00-00'))";


$rws = '';
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
		(LEFT(isType,3)='imp') AND 
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

        $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=1' class='btn btn-outline-dark'>Primary</a>";
        if($isBackup==1){
            $btnBackup = "<a href='05_action.php?act=save_toggle_imp_backup&stkm_id=$stkm_id&targetID=$targetID&STOCK_CODE=$STOCK_CODE&isType=$isType&isBackup=0' class='btn btn-dark'>Backup</a>";
        }
        
        $btnType = "<span class='badge badge-info'>IMP</span>";
	    $rws.="<tr><td>$btnType</td><td>$targetID</td><td>$DSTRCT_CODE</td><td>$WHOUSE_ID</td><td>$SUPPLY_CUST_ID</td><td>$BIN_CODE</td><td>$STOCK_CODE</td><td>$ITEM_NAME</td><td>$targetItemCount</td><td class='float-right'>$btnBackup</td></tr>";
	
	}
}


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
	}
}

$impMilisFindingIDs = getFindingIDsString("imp%",  $isImpAbbrsWithMilisEnabled);
$impCompletedFindingIDs = getFindingIDsString("imp%", $isImpAbbrsCompletedStatus);	
$b2rCompletedFindingIDs = getFindingIDsString("b2r", $isB2rAbbrsCompletedStatus);	



$sqlStats = "
select 
	sum(stat.impPrimaryCompleteFlag) as impPrimeComplete,
	sum(stat.impPrimaryTotalFlag) as impPrimeTotal,
	
	sum(stat.impBackupCompleteFlag) as impBackupComplete,
	sum(stat.impBackupTotalFlag) as impBackupTotal,
	
	sum(stat.b2rPrimaryCompleteFlag) as b2rPrimeComplete,
	sum(stat.b2rPrimaryTotalFlag) as b2rPrimeTotal,

	sum(stat.b2rBackupCompleteFlag) as b2rBackupComplete,
	sum(stat.b2rBackupTotalFlag) as  b2rBackupTotal
from (
	select 
		(CASE WHEN (t.impPrimaryTotal>0 AND t.impPrimaryComplete=t.impPrimaryTotal) THEN 1 ELSE 0 END) as impPrimaryCompleteFlag,
		(CASE WHEN (t.impPrimaryTotal>0) THEN 1 ELSE 0 END) as impPrimaryTotalFlag,
	
		(CASE WHEN (t.b2rPrimaryTotal>0 AND t.b2rPrimaryComplete=t.b2rPrimaryTotal) THEN 1 ELSE 0 END) as b2rPrimaryCompleteFlag,
		(CASE WHEN (t.b2rPrimaryTotal>0) THEN 1 ELSE 0 END) as b2rPrimaryTotalFlag,
	
		(CASE WHEN (t.impBackupTotal>0 AND t.impBackupComplete=t.impBackupTotal) THEN 1 ELSE 0 END) as impBackupCompleteFlag,
		(CASE WHEN (t.impBackupTotal>0) THEN 1 ELSE 0 END) as impBackupTotalFlag,
	
		(CASE WHEN (t.b2rBackupTotal>0 AND t.b2rBackupComplete=t.b2rBackupTotal) THEN 1 ELSE 0 END) as b2rBackupCompleteFlag,
		(CASE WHEN (t.b2rBackupTotal>0) THEN 1 ELSE 0 END) as b2rBackupTotalFlag
	
		from (
	
			select
				(CASE WHEN isBackup=1 THEN 1 ELSE 0 END) as backup,
				DSTRCT_CODE,
				WHOUSE_ID,
				BIN_CODE,
				STOCK_CODE,
				isType,
				SUM(CASE WHEN(	isType like 'imp%' 
								AND (isBackup<>1) 
								AND findingID IN (
									SELECT findingID 
									FROM smartdb.sm19_result_cats 
									WHERE 
										isType like 'imp%' 
										AND resAbbr in ('SER','USWD','USND','NIC','SPLT')
								)
						)
						THEN (
			    			CASE WHEN ( findingID in (
					 						SELECT findingID 
											FROM smartdb.sm19_result_cats 
											WHERE 
												isType like 'imp%' 
												AND resAbbr in ('USWD','USND')   			
			    						) 
			    						AND (checked_to_milis<>1)
			    					)
			    				 THEN 0 
			    				 ELSE 1 
			    			END				
						) 
						ELSE 0
						END
				) as impPrimaryComplete,
				
				SUM(CASE WHEN(
							(isType like 'imp%' )
							AND (isBackup<>1) 
						)
						THEN 1
						ELSE 0
						END
				) as impPrimaryTotal,
		
				SUM(CASE WHEN ( findingID in (
				
									SELECT findingID 
									FROM smartdb.sm19_result_cats 
									WHERE 
										isType='b2r' 
										AND resAbbr in ('INV','NSTR')
								) AND
								(isType='b2r') 
								AND (isBackup <> 1)
								AND	(data_source='skeleton') 
								
							  )
						THEN 1 
						ELSE 0 
					END
				) AS b2rPrimaryComplete,
			
				SUM(CASE WHEN(
							(isType='b2r' )
							AND (isBackup<>1) 
							AND (data_source='skeleton')
						)
						THEN 1
						ELSE 0
						END
				) as b2rPrimaryTotal,
				
				SUM(CASE WHEN(	isType like 'imp%' 
								AND (isBackup=1) 
								AND findingID IN (
									SELECT findingID 
									FROM smartdb.sm19_result_cats 
									WHERE 
										isType like 'imp%' 
										AND resAbbr in ('SER','USWD','USND','NIC','SPLT')
								)
						)
						THEN (
			    			CASE WHEN ( findingID in (
					 						SELECT findingID 
											FROM smartdb.sm19_result_cats 
											WHERE 
												isType like 'imp%' 
												AND resAbbr in ('USWD','USND')   			
			    						) 
			    						AND (checked_to_milis<>1)
			    					)
			    				 THEN 0 
			    				 ELSE 1 
			    			END				
						) 
						ELSE 0
						END
				) as impBackupComplete,
				
				SUM(CASE WHEN(
							(isType like 'imp%' )
							AND (isBackup=1) 
						)
						THEN 1
						ELSE 0
						END
				) as impBackupTotal,
		
				SUM(CASE WHEN ( findingID in (
				
									SELECT findingID 
									FROM smartdb.sm19_result_cats 
									WHERE 
										isType='b2r' 
										AND resAbbr in ('INV','NSTR')
								) AND
								(isType='b2r') 
								AND (isBackup = 1)
								AND	(data_source='skeleton') 
								
							  )
						THEN 1 
						ELSE 0 
					END
				) AS b2rBackupComplete,
			
				SUM(CASE WHEN(
							(isType='b2r' )
							AND (isBackup=1) 
							AND (data_source='skeleton')
						)
						THEN 1
						ELSE 0
						END
				) as b2rBackupTotal		
			FROM smartdb.sm18_impairment
			WHERE
				data_source <> 'extra'
				AND stkm_id IN ($sqlInclude)			
			GROUP BY
				isBackup,
				DSTRCT_CODE,
				WHOUSE_ID,
				BIN_CODE,
				STOCK_CODE,
				isType				
	) as t
) as stat";

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