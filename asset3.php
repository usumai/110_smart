<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<?php
$stkm_id	= 1;

$arr  = [];
$sql = "SELECT AssetDesc1, AssetDesc2 FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id; ";
$arrsql = $arr = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
    $arr[] = $r;
}}
$arr = json_encode($arr);
?>

<br><br>

<table id="example" class="display" width="100%"></table>



<br><br><br><br><br><br><br><br><br><br><br>


<script>
let arr = <?=$arr?>;
console.log(arr)
$( function() {
    $('#example').DataTable({
        data: arr,
        columns: [
            { data: 'AssetDesc1' },
            { data: 'AssetDesc2' }
        ]
    });
});
</script>

<?php include "04_footer.php"; ?>