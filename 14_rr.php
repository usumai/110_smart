<?php 
include "02_header.php";

$search_term = $_GET["search_term"];

$options_stks_template="";
$sql = "SELECT * FROM smartdb.sm13_stk WHERE delete_date IS NULL";
// echo $sql;
$result2 = $con->query($sql);
if ($result2->num_rows > 0) {
  while($row2 = $result2->fetch_assoc()) {
    $stkm_id  	= $row2["stkm_id"];
    $stk_id  	= $row2["stk_id"];
    $stk_name  	= $row2["stk_name"];

    $stk_name  	= str_replace("_", " ", $stk_name);

	$options_stks_template .= "<a class='dropdown-item' href='05_action.php?act=save_rr_add&rr_id=XYZABC!!!&stkm_id=".$stkm_id."'>".$stk_id.": ".$stk_name."</a>";
}}





$rows_assets = "";
$sql = "SELECT * FROM smartdb.sm12_rwr WHERE Asset LIKE '%$search_term%' OR InventNo LIKE '%$search_term%' OR  AssetDesc1 LIKE '%$search_term%' LIMIT 100;";
$result = $con->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$rr_id 		= $row["rr_id"];
		$Asset 		= $row["Asset"];
		$AssetDesc1 = $row["AssetDesc1"];
		$InventNo 	= $row["InventNo"];
		$Class 		= $row["Class"];
		$ParentName = $row["ParentName"];


		$options_stks = str_replace("XYZABC!!!", $rr_id, $options_stks_template);

		$stkm_option = "<div class='dropdown'><button class='btn dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Add to a stocktake/site</button><div class='dropdown-menu' aria-labelledby='btn_clear'>".$options_stks."</div></div>";

		$rows_assets .= "<tr><td>".$stkm_option."<input type='hidden' name='rr_id' value='".$rr_id."'></td><td>".$Asset."</td><td>".$AssetDesc1."</td><td>".$InventNo."</td><td>".$Class."</td><td>".$ParentName."</td></tr>";
}}
?>
<?php include "03_menu.php"; ?>


<div class="container">
	<div class="row" style="margin-top: 15px">
		<h2>Raw remainder assets. Search result for: "<?=$search_term?>" </h2>
		This page will only show the first 100 results, search for a more specific term above.
	</div>
	<div class="row">
	    <div class="col-lg">    	

      		<table id="table_assets" class="table table-sm table-striped table-border" width="100%">
	        	<thead class="table-dark sticky-top">
		    		<tr>
		    			<th>Action</th>
		    			<th>Asset</th>
		    			<th>Description</th>
		    			<th>InventNo</th>
		    			<th>Class</th>
		    			<th>ParentName</th>
		    		</tr>
		        </thead>
		        <tbody>
		            <?=$rows_assets?>
		        </tbody>
	    	</table>

	    </div>
	</div>
</div>

<?php include "04_footer.php"; ?>