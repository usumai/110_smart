<?php
include "../../01_dbcon.php"; 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



function get_file(){
    foreach(glob('config/*.*') as $filename) {//find the filename in the config folder
        if (strpos($filename, 'reason_codes') !== false) {
            return $filename;
        }
    }
}
$sql            = "TRUNCATE TABLE smartdb.sm15_rc;";
$res_truncate   = mysqli_multi_query($con,$sql);
$filename       = get_file('reason_codes');
$file_contents  = file_get_contents($filename);
$string         = "[" . trim($file_contents) . "]";
$json           = json_decode($string, true);
$header         = $json[0];

$stmt   = $con->prepare("INSERT INTO smartdb.sm15_rc (res_reason_code, rc_desc, rc_long_desc, rc_example, rc_origin, rc_action, rc_states, rc_sorting_cat, rc_color) VALUES (?,?,?,?,?,?,?,?,?);");
foreach ($header['reason_codes'] as $key => $val) {    
    echo "<br>";
    print_r($val);
    $stmt   ->bind_param("sssssssss", $val['res_reason_code'], $val['rc_desc'], $val['rc_long_desc'], $val['rc_example'], $val['rc_origin'], $val['rc_action'], $val['rc_states'], $val['rc_sorting_cat'], $val['rc_color']);
    $stmt   ->execute();
}


?>