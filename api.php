<?php include "01_dbcon.php"; ?><?php
if (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}

// echo $act;
$dbname = "smartdb";



if ($act=='get_system'){
    $stks = $sett = [];
    $sql = "SELECT * FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $stks[] = $row;
    }}

    $sql = "SELECT * FROM smartdb.sm10_set;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sett[] = $row;
    }}

    $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $system_stk_type = $row["stk_type"];
        }}
    if(empty($system_stk_type)) {
        $system_stk_type = "notset";
    }


    $sys  = [];
    $sys["stks"]            = $stks;
    $sys["sett"]            = $sett;
    $sys["system_stk_type"] = $system_stk_type;
    $sys = json_encode($sys);
    echo $sys;


}elseif ($act=='get_templates'){

     $data = [];
     $sqlsub = "SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_id=0 and smm_delete_date IS NULL";
     $sql = "SELECT ass_id, res_AssetDesc1 FROM smartdb.sm14_ass WHERE stkm_id IN ($sqlsub) AND delete_date IS NULL ORDER BY AssetDesc1";
     // echo $sql;
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $data[] = $row;
          // $listTmplt .= "<a class='dropdown-item' href='05_action.php?act=save_usetemplate&ass_id=$ass_id'>ASASS$res_AssetDesc1</a>";
     }}
     $data = json_encode($data);
     echo $data;

}elseif ($act=='save_archive_return'){
    $stkm_id = $_GET["stkm_id"];  

    $sql = "SELECT * FROM smartdb.sm10_set;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $active_profile_id    = $row["active_profile_id"];
    }}

    $sql = "UPDATE smartdb.sm13_stk SET smm_delete_date=NOW(),smm_delete_user='$active_profile_id' WHERE stkm_id = $stkm_id;";
    // echo $sql_save;
    echo runSql($sql);

}elseif ($act=='get_menu_details'){

     $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $system_stk_type = $row["stk_type"];
         }}
     if(empty($system_stk_type)) {
         $system_stk_type = "notset";
     }
     echo $system_stk_type;


}elseif ($act=='save_check_version'){
     $test_internet = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
     if ($test_internet){
          $URL = 'https://raw.githubusercontent.com/usumai/110_smart/master/08_version.json';
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_URL, $URL);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          $data = curl_exec($ch);
          curl_close($ch);
          $json = json_decode($data, true);
          $latest_version_no       = $json["latest_version_no"];
          $version_publish_date    = $json["version_publish_date"];

          $sql_save = "UPDATE smartdb.sm10_set SET date_last_update_check=NOW(), versionRemote=$latest_version_no; ";

          mysqli_multi_query($con,$sql_save);
          $test_results = "Check performed";

     }else{
          $test_results = "Internet is required to check the version";
     }

     // Compare remote to local and advise if update button should be displayed
     $sql = "SELECT versionLocal, versionRemote FROM smartdb.sm10_set";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
          $versionLocal	= $row["versionLocal"];
          $versionRemote	= $row["versionRemote"];
     }}
     $data  = [];
     $data["versionLocal"]    = $versionLocal;
     $data["versionRemote"]   = $versionRemote;
     $data["test_results"]    = $test_results;
     $data = json_encode($data);
     echo $data;

}elseif ($act=='save_toggle_stk_return'){
     $stkm_id = $_GET["stkm_id"];  
     
     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id = ".$stkm_id.";";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $stkm_id       = $row["stkm_id"];
               $stk_include   = $row["stk_include"];
     }}
     if ($stk_include==1) {
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=0 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=0 WHERE stkm_id = $stkm_id;";
     }else{
          $sql_save_stk = "UPDATE smartdb.sm13_stk SET stk_include=1 WHERE stkm_id = $stkm_id;";
          $sql_save_ass = "UPDATE smartdb.sm14_ass SET stk_include=1 WHERE stkm_id = $stkm_id;";
     }
     $sql = $sql_save_stk.$sql_save_ass;
     $res = runSql($sql);
     if($res=="success"){
          $res = ($stk_include==0) ? "Included" : "Excluded";
     }else{
          $res = "failed".$res;
     }
     echo $res;
     
}elseif ($act=='get_SystemStkType'){
     // Get what the tool is configured for: stocktake, impairment or nothing
     
     $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE smm_delete_date IS NULL AND stk_include =1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $system_stk_type = $row["stk_type"];
         }}
     if(empty($system_stk_type)) {
         $system_stk_type = "notset";
     }
     echo $system_stk_type;

}elseif ($act=='save_archive_stk'){
     $stkm_id = $_GET["stkm_id"];


     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql_save = "UPDATE smartdb.sm13_stk SET smm_delete_date=NOW(),smm_delete_user='$active_profile_id' WHERE stkm_id = $stkm_id;";
     echo $sql_save;
     mysqli_multi_query($con,$sql_save);
     header("Location: index.php");


}











function runSql($stmt){
     global $con;
     if (!mysqli_multi_query($con,$stmt)){
          $save_error = mysqli_error($con);
          $log ='failure: '.$save_error;
     }else{
          $log ='success';     
     }
     // echo "<br><br>".$stmt."<br>".$log;
     return $log;
}


function cleanvalue($fieldvalue) {
     // $fieldvalue = str_replace("'", "\'", $fieldvalue);
     // $fieldvalue = str_replace('"', '\"', $fieldvalue);
     $fieldvalue = str_replace("'", "''", $fieldvalue);
     $fieldvalue = str_replace('"', '""', $fieldvalue);
     // $fieldvalue = str_replace("""", "/""", $fieldvalue);
     if ($fieldvalue=="") {
          $fieldvalue="NULL";
     }elseif (empty($fieldvalue)) {
          $fieldvalue="NULL";
     }elseif ($fieldvalue=="NULL") {
          $fieldvalue="NULL";
     }elseif ($fieldvalue=="null") {
          $fieldvalue="NULL";
     }elseif (strlen($fieldvalue)==0) {
          $fieldvalue="NULL";
     }else{
          $fieldvalue="'".$fieldvalue."'";
     }
     return $fieldvalue;
}

function checkExtrasFinished($BIN_CODE){
     global $con;

     $fingerprint        = time();
     $sql = "SELECT COUNT(*) AS extraCount, SUM(CASE WHEN finalResult IS NULL THEN 0 ELSE 1 END) AS extraComplete FROM smartdb.sm18_impairment WHERE BIN_CODE = '$BIN_CODE' AND isChild=1 AND isType='b2r'";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {    
          $extraCount    = $row['extraCount']; 
          $extraComplete = $row['extraComplete'];  
     }}
     if($extraCount==$extraComplete){
          $sql = "UPDATE smartdb.sm18_impairment SET 
          findingID=16,
          fingerprint='$fingerprint'
          WHERE BIN_CODE='$BIN_CODE' ";
     }else{
          $sql = "UPDATE smartdb.sm18_impairment SET 
          findingID=15
          WHERE BIN_CODE='$BIN_CODE' ";
     }
     runSql($sql);
}

?>