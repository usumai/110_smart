<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$auto_storageID = $_GET["auto_storageID"];
$stkm_id        = $_GET["stkm_id"];
$BIN_CODE       = $_GET["BIN_CODE"];

$sql = "SELECT * FROM smartdb.sm18_impairment WHERE auto_storageID = $auto_storageID";
// $sql .= " LIMIT 500; ";   
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {    
        $auto_storageID     = $row['auto_storageID'];    
        $STOCK_CODE         = $row['STOCK_CODE'];
        $ITEM_NAME          = $row['ITEM_NAME'];
        $SOH                = $row['SOH'];
        $finalResult        = $row['finalResult'];
        $res_comment        = $row['res_comment'];
}}
?>
<br><br>
<div class='container'>
    <div class='row'>
        <div class='col'>
            <h1 class="display-4" id="exampleModalLabel">
                Edit extra stockcode
                <a href="17_b2r.php?BIN_CODE=<?=$BIN_CODE?>&stkm_id=<?=$stkm_id?>" class="btn btn-outline-dark float-right" >Back</a>
            </h1>
        </div>
    </div>

    <div class='row'>
        <div class='col'>
            <form action='05_action.php' method='post' id='formAddExtra'>
                NSN/Stockcode
                <input type="text" name="extraStockcode" id="extraStockcode" class="form-control" value='<?=$STOCK_CODE?>'>
                Stockcode description
                <input type="text" name="extraName" id="extraName" class="form-control" value='<?=$ITEM_NAME?>'>
                SOH
                <input type="text" name="extraSOH" id="extraSOH" class="form-control" value='<?=$SOH?>'>
                Comments
                <textarea name="extraComments" id="extraComments" class="form-control" rows='5'><?=$res_comment?></textarea>
                <input type="hidden" name="BIN_CODE" value="<?=$BIN_CODE?>">
                <input type="hidden" name="stkm_id" value="<?=$stkm_id?>">
                <input type="hidden" name="auto_storageID" value="<?=$auto_storageID?>">
                <input type="hidden" name="act" value="save_b2r_add_extra">
                <a href='05_action.php?act=save_delete_extra&auto_storageID=<?=$auto_storageID?>&stkm_id=<?=$stkm_id?>&BIN_CODE=<?=$BIN_CODE_code?>' class='btn btn-outline-danger'>Delete</a>
                <input type="submit" class="btn btn-outline-dark float-right" value='Save' id='btnAddSC'>     
            </form>
        </div>
    </div>
</div>


<?php include "04_footer.php"; ?>