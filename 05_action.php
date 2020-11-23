<?php 
include "01_dbcon.php"; 
include "05_scripts.php";
include "05_db_designer.php";

$current_row=0;
if(array_key_exists("current_row",$_POST)){
	$current_row=$_POST["current_row"];
}elseif(array_key_exists("current_row",$_GET)){
	$current_row=$_GET["current_row"];
}


if (isset($_POST["act"])) {
	$act = $_POST["act"];
}else{
	$act = $_GET["act"];
}
// echo $act;
// $exportFileVersion=7;
$this_version_no  = 12;
$date_version_published = "2019-12-10 00:00:00";
// Steps for relesing a new version:
// 1. Update the version info above with version number one more than current
// 2. Update the 08_version.json as per above details
// 3. Delete json and xls files from directory to stop any leaks
// 4. Push local to master using toolshelf

// echo $act;
$dbname        = "smartdb";
$addr_git      = ' "\Program Files\Git\bin\git"  ';
$log           = "<br>"."Initialising action file";
$active_user   = "";
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//CRUD











































// ####################################################################################
// System actions
// ####################################################################################
if ($act=='sys_pull_master') {
     // $sql_save = "DROP DATABASE $dbname;";
     // mysqli_multi_query($con,$sql_save); 
	//This file updates the local software with the currently published software
	$output  = shell_exec($addr_git.' init 2>&1'); 
	$output  = shell_exec($addr_git.' remote set-url https://github.com/usumai/110_smart.git'); 
	$output .= shell_exec($addr_git.' clean  -d  -f .');
	$output .= shell_exec($addr_git.' reset --hard');  
	$output .= shell_exec($addr_git.' pull https://github.com/usumai/110_smart.git');
	echo "<pre>$output</pre>";
     mysqli_multi_query($con,$sql_save);
	header("Location: 05_action.php?act=sys_reset_data");

}elseif ($act=='sys_initialise') {
     $log .= "<br>"."creating database: $dbname";
     $sql_save = "CREATE DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();

}elseif ($act=='sys_reset_data') {
     $sql_save = "DROP DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     
     $log .= "<br>"."creating database: $dbname";
     $sql_save = "CREATE DATABASE $dbname;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();

}elseif ($act=='sys_reset_data_minus_rr') {
     //Delete all tables except for RR
     $sql_save = "DROP TABLE $dbname.sm10_set, $dbname.sm11_pro, $dbname.sm13_stk, $dbname.sm14_ass, $dbname.sm15_rc, $dbname.sm16_file, $dbname.sm17_history, $dbname.sm18_impairment, $dbname.sm19_result_cats, $dbname.sm20_quarantine;";
     mysqli_multi_query($con,$sql_save); 
     fnInitiateDatabase();












































// ####################################################################################
// Menu actions
// ####################################################################################


}elseif ($act=='get_system'){
     $stks = $sett = $pro = [];
 
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
     
     $sql = "SELECT * FROM smartdb.sm11_pro WHERE delete_date IS NULL;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $pro[] = $row;
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
     $sys["pro"]             = $pro;
     $sys["system_stk_type"] = $system_stk_type;
     $sys = json_encode($sys);
     echo $sys;
 
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
 
}elseif ($act=='sys_open_image_folder') {
    // $output  = shell_exec('cd/'); 
    shell_exec('cd C:\xampp\htdocs\110_smart\images'); 
    // shell_exec('cd/ C:\users\Google Drive\015_www\110_smarter_master\images\ ');
    // $output  = shell_exec('cd images '); 
    shell_exec('start .'); 
    header("Location: index.php");

}else if ($act=="save_invertcolors") {
     $sql = "SELECT * FROM smartdb.sm10_set";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $theme_type    = $row["theme_type"];
     }}

     if ($theme_type==1) {
          $sql_save = "UPDATE smartdb.sm10_set SET theme_type = '0' ";
     }else{
          $sql_save = "UPDATE smartdb.sm10_set SET theme_type = '1' ";
     }

     sleep(1);
     // echo $_SERVER['HTTP_REFERER'];
     mysqli_multi_query($con,$sql_save);
     header("Location: ".$_SERVER['HTTP_REFERER']);


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


}elseif ($act=='get_excel'){
     $stkm_id = $_GET["stkm_id"];

     $sql = "SELECT stk_type FROM smartdb.sm13_stk WHERE stkm_id=1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
          $stk_type = $row['stk_type'];
     }}
     if($stk_type=='impairment'){
          $sql = "SELECT *  FROM smartdb.sm18_impairment WHERE stkm_id = $stkm_id;";
     }else{
          $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id;";
     }

     $mydate=getdate(date("U"));
     $month_disp = substr("00".$mydate['mon'], -2);
     $day_disp      = substr("00".$mydate['mday'], -2);
     $hours_disp    = substr("00".$mydate['hours'], -2);
     $minutes_disp  = substr("00".$mydate['minutes'], -2);
     $seconds_disp  = substr("00".$mydate['seconds'], -2);
     $date_disp     = $mydate['year'].$month_disp.$day_disp;
     $date_disp     = $mydate['year'].$month_disp.$day_disp."_".$hours_disp.$minutes_disp.$seconds_disp;

     $txt_file_link = "$date_disp.xls";
     $file_excel    = fopen($txt_file_link, "w") or die("Unable to open file!");

     $cherry=0;
     $contents = "";
     $header   = "<html><body><table border='1'><tr>";
     $arr_asset = array();
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($r = $result->fetch_assoc()) {
               $contents .= "<tr>";

               if($cherry==0){
                    $cherry=1;
                    foreach($r as $column=>$value) {
                         $header .= "<td><b>$column</b></td>";
                    }
               }
               foreach($r as $column=>$value) {
                    // echo "<br>$column = $value\n";
                    $contents .= "<td>$value</td>";
               }
               $contents .= "</tr>";
     }}

     $header   .= "</tr>";
     $contents = $header.$contents."</table></body></html>";

     fwrite($file_excel, $contents);
     fclose($file_excel);
     if (file_exists($txt_file_link)) {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.basename($txt_file_link));
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header('Content-Length: ' . filesize($txt_file_link));
         ob_clean();
         flush();
         readfile($txt_file_link);
         exit;
     }


}elseif ($act=='get_asset_list') {
     $search_term = $_GET["search_term"];     
     $ar = array();


     $limitsql = "(SELECT * FROM smartdb.sm14_ass WHERE stk_include=1 AND stkm_id IN (SELECT stkm_id FROM smartdb.sm13_stk WHERE stk_include = 1 )) AS vtIncludedAssets";


     $sql = "  SELECT ass_id, Asset, Subnumber, res_AssetDesc1, res_AssetDesc2, res_InventNo, res_SNo, res_Location, res_Room, res_reason_code

               FROM $limitsql

               WHERE storage_id LIKE '%$search_term%'
               OR stk_include LIKE '%$search_term%'
               OR Asset LIKE '%$search_term%'
               OR Subnumber LIKE '%$search_term%'
               OR genesis_cat LIKE '%$search_term%'
               OR first_found_flag LIKE '%$search_term%'
               OR rr_id LIKE '%$search_term%'
               OR fingerprint LIKE '%$search_term%'
               OR res_create_date LIKE '%$search_term%'
               OR res_create_user LIKE '%$search_term%'
               OR res_reason_code LIKE '%$search_term%'
               OR res_reason_code_desc LIKE '%$search_term%'
               OR res_completed LIKE '%$search_term%'
               OR res_comment LIKE '%$search_term%'
               OR AssetDesc1 LIKE '%$search_term%'
               OR AssetDesc2 LIKE '%$search_term%'
               OR AssetMainNoText LIKE '%$search_term%'
               OR Class LIKE '%$search_term%'
               OR assetType LIKE '%$search_term%'
               OR Inventory LIKE '%$search_term%'
               OR Quantity LIKE '%$search_term%'
               OR SNo LIKE '%$search_term%'
               OR InventNo LIKE '%$search_term%'
               OR accNo LIKE '%$search_term%'
               OR Location LIKE '%$search_term%'
               OR Room LIKE '%$search_term%'
               OR State LIKE '%$search_term%'
               OR latitude LIKE '%$search_term%'
               OR longitude LIKE '%$search_term%'
               OR CurrentNBV LIKE '%$search_term%'
               OR AcqValue LIKE '%$search_term%'
               OR OrigValue LIKE '%$search_term%'
               OR ScrapVal LIKE '%$search_term%'
               OR ValMethod LIKE '%$search_term%'
               OR RevOdep LIKE '%$search_term%'
               OR CapDate LIKE '%$search_term%'
               OR LastInv LIKE '%$search_term%'
               OR DeactDate LIKE '%$search_term%'
               OR PlRetDate LIKE '%$search_term%'
               OR CCC_ParentName LIKE '%$search_term%'
               OR CCC_GrandparentName LIKE '%$search_term%'
               OR GrpCustod LIKE '%$search_term%'
               OR CostCtr LIKE '%$search_term%'
               OR WBSElem LIKE '%$search_term%'
               OR Fund LIKE '%$search_term%'
               OR RspCCtr LIKE '%$search_term%'
               OR CoCd LIKE '%$search_term%'
               OR PlateNo LIKE '%$search_term%'
               OR Vendor LIKE '%$search_term%'
               OR Mfr LIKE '%$search_term%'
               OR UseNo LIKE '%$search_term%'
               OR res_AssetDesc1 LIKE '%$search_term%'
               OR res_AssetDesc2 LIKE '%$search_term%'
               OR res_AssetMainNoText LIKE '%$search_term%'
               OR res_Class LIKE '%$search_term%'
               OR res_assetType LIKE '%$search_term%'
               OR res_Inventory LIKE '%$search_term%'
               OR res_Quantity LIKE '%$search_term%'
               OR res_SNo LIKE '%$search_term%'
               OR res_InventNo LIKE '%$search_term%'
               OR res_accNo LIKE '%$search_term%'
               OR res_Location LIKE '%$search_term%'
               OR res_Room LIKE '%$search_term%'
               OR res_State LIKE '%$search_term%'
               OR res_latitude LIKE '%$search_term%'
               OR res_longitude LIKE '%$search_term%'
               OR res_CurrentNBV LIKE '%$search_term%'
               OR res_AcqValue LIKE '%$search_term%'
               OR res_OrigValue LIKE '%$search_term%'
               OR res_ScrapVal LIKE '%$search_term%'
               OR res_ValMethod LIKE '%$search_term%'
               OR res_RevOdep LIKE '%$search_term%'
               OR res_CapDate LIKE '%$search_term%'
               OR res_LastInv LIKE '%$search_term%'
               OR res_DeactDate LIKE '%$search_term%'
               OR res_PlRetDate LIKE '%$search_term%'
               OR res_CCC_ParentName LIKE '%$search_term%'
               OR res_CCC_GrandparentName LIKE '%$search_term%'
               OR res_GrpCustod LIKE '%$search_term%'
               OR res_CostCtr LIKE '%$search_term%'
               OR res_WBSElem LIKE '%$search_term%'
               OR res_Fund LIKE '%$search_term%'
               OR res_RspCCtr LIKE '%$search_term%'
               OR res_CoCd LIKE '%$search_term%'
               OR res_PlateNo LIKE '%$search_term%'
               OR res_Vendor LIKE '%$search_term%'
               OR res_Mfr LIKE '%$search_term%'
               OR res_UseNo LIKE '%$search_term%'

 
               LIMIT 10";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $arr = array();
               $arr["label"]            = $row["Asset"].'-'.$row["Subnumber"].':'.$row["res_AssetDesc1"];
               $arr["Asset"]            = $row["Asset"];
               $arr["Subnumber"]        = $row["Subnumber"];
               $arr["AssetDesc1"]       = $row["res_AssetDesc1"];
               $arr["AssetDesc2"]       = $row["res_AssetDesc2"];
               $arr["InventNo"]         = $row["res_InventNo"];
               $arr["SNo"]              = $row["res_SNo"];
               $arr["Location"]         = $row["res_Location"];
               $arr["Room"]             = $row["res_Room"];

               if ($row["res_reason_code"]) {
                    $arr["status_compl"] = "<span class='octicon octicon-check text-success'></span>";
               }else{
                    $arr["status_compl"] = "<span class='octicon octicon-x text-danger' ></span>";
               }

               $arr["value"]  = $row["ass_id"];
               $ar[]          = $arr;
     }}
     $sql = "  SELECT COUNT(*) AS rwrCount FROM smartdb.sm12_rwr
               WHERE Asset LIKE '%$search_term%'
               OR InventNo LIKE '%$search_term%'
               OR AssetDesc1 LIKE '%$search_term%'";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
          $arr = array();
          $arr["Asset"]       = "Raw remainder results";
          $arr["value"]       = "RR";
          $arr["Subnumber"]   = $row["rwrCount"];
          $ar[]               = $arr;
     }}
     
     echo json_encode($ar);


}elseif ($act=='save_edit_user_profile') {
     $edit_profile_id         = $_POST["edit_profile_id"];
     $profile_name            = $_POST["profile_name"];
     $profile_phone_number    = $_POST["profile_phone_number"];

     echo "<br>edit_profile_id: ".$edit_profile_id;
     echo "<br>profile_name: ".$profile_name;
     echo "<br>profile_phone_number: ".$profile_phone_number;

     if ($edit_profile_id==0){
          $sql = "  INSERT INTO smartdb.sm11_pro 
                    (create_date, profile_name, profile_phone_number)
                    VALUE (NOW(), '$profile_name', '$profile_phone_number') ";
          $epi_sql= "(SELECT max(profile_id) AS new_profile_id FROM smartdb.sm11_pro)";
     }else{
          $sql = "  UPDATE smartdb.sm11_pro SET 
                    update_date=NOW(),
                    profile_name='$profile_name',
                    profile_phone_number='$profile_phone_number'
                    WHERE profile_id='$edit_profile_id' ";
          $epi_sql= "'$edit_profile_id'";
     }
     echo $sql;
     runSql($sql);

     $sql = "UPDATE smartdb.sm10_set SET active_profile_id=$epi_sql ";
     runSql($sql);

}elseif ($act=='save_delete_user_profile') {
     $edit_profile_id = $_POST["edit_profile_id"];
     $sql = "  UPDATE smartdb.sm11_pro SET delete_date=NOW() WHERE profile_id='$edit_profile_id' ";
     runSql($sql);


































// ####################################################################################
// Stocktake management actions
// ####################################################################################

}elseif ($act=='save_stk_toggle') {
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
     echo "<br>".$sql_save_stk;
     echo "<br>".$sql_save_ass;
     mysqli_multi_query($con,$sql_save_stk);
     mysqli_multi_query($con,$sql_save_ass);

     header("Location: index.php");

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




}elseif ($act=='get_check_merge_criteria') {
     $sql = "SELECT COUNT(DISTINCT stk_id) as CountDistinctStk, COUNT(*) AS CountActiveStks FROM smartdb.sm13_stk WHERE stk_include = 1 AND smm_delete_date IS NULL";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $CountDistinctStk   = $row["CountDistinctStk"];
               $CountActiveStks    = $row["CountActiveStks"];
     }}
     $mergeEnagbled=0;
     if ($CountDistinctStk==1&&$CountActiveStks==2){
          $mergeEnagbled=1;
     }
     echo $mergeEnagbled;



}elseif ($act=='upload_file') {
     $dev=false;
     $target_file = $_FILES["fileToUpload"]["tmp_name"];
     $fileContents = file_get_contents($target_file);

     //This is to remove the unicode encoding on the file. It leaves two characters at the start of the file which throw an error.
     $fileContents  = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $fileContents);
     $arr_full      = json_decode($fileContents, true);
     $arr           = $arr_full['import'];

     echo "<br>Type:" .$arr['type'];
     echo "<br>";
     if ($arr['type']=="stocktake") {
          fnUpload_stocktake($arr, $dev);
     }elseif ($arr['type']=="raw remainder v2") {
          fnUpload_rawremainder($arr, $dev);
     }elseif ($arr['type']=="impairment") {
          fnUpload_impairment($arr, $dev);
     }

     header("Location: index.php");

}elseif ($act=='get_export_stk'){
     $stkm_id = $_GET["stkm_id"];

     error_reporting(-1);
     ini_set('display_errors', 'On');

     $mydate=getdate(date("U"));
     $month_disp = substr("00".$mydate['mon'], -2);
     $day_disp      = substr("00".$mydate['mday'], -2);
     $hours_disp    = substr("00".$mydate['hours'], -2);
     $minutes_disp  = substr("00".$mydate['minutes'], -2);
     $seconds_disp  = substr("00".$mydate['seconds'], -2);
     $date_disp = $mydate['year'].$month_disp.$day_disp;
     $date_disp = $mydate['year'].$month_disp.$day_disp."_".$hours_disp.$minutes_disp.$seconds_disp;
     $date_disp = $mydate['year'].$month_disp.$day_disp.$hours_disp.$minutes_disp;

     $sql = "SELECT * FROM smartdb.sm13_stk WHERE stkm_id=$stkm_id;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $stk_id               = $row["stk_id"];
             $stk_name             = $row["stk_name"];
             $dpn_extract_date     = $row["dpn_extract_date"];
             $dpn_extract_user     = $row["dpn_extract_user"];
             $stk_type             = $row["stk_type"];
             $rc_orig              = $row["rc_orig"];
             $rc_orig_complete     = $row["rc_orig_complete"];
             $rc_extras            = $row["rc_extras"];
     }}

     if ($stk_type=='stocktake'){
          $sql = "SELECT *  FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id AND delete_date IS NULL AND flagTemplate IS NULL";
     }else{
          $sql = "SELECT *  FROM smartdb.sm18_impairment WHERE stkm_id = $stkm_id AND delete_date IS NULL ";
     }
     $arr_asset = array();
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($r = $result->fetch_assoc()) {
             $arr_asset[] = $r;
     }}

     $sql_count = "SELECT COUNT(*) AS rc_totalSent FROM ($sql) as vtMainSummary";
     $result = $con->query($sql_count);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $rc_totalSent         = $row["rc_totalSent"];
     }}
     //echo "rc_totalSent[$rc_totalSent]";

// echo "[$stk_name]";
     $stk_name_disp = substr($stk_name, 0, 30);
     // $txt_file_link = "SMARTm_$date_disp"."_$stk_id:$stk_name_disp.json";
     $stk_name_disp = str_replace(" ","_",$stk_name_disp);
     $stk_name_disp = str_replace(":","_",$stk_name_disp);
     $txt_file_link = "SMARTm_$stk_name_disp.json";
     // echo "<br>[$txt_file_link]";
     // $txt_file_link = "SMARTm.json";
     // $txt_file_link = "$stk_name_disp.json";
     $fp = fopen($txt_file_link, 'w');

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $smm_extract_user    = $row["active_profile_id"];
     }}
     $smm_extract_date = $mydate['year']."-".$month_disp."-".$day_disp;

     $response = array();
     $response['import']['type']                  = $stk_type;
     $response['import']['stkm_id']               = $stkm_id;
     $response['import']['fileversion']           = $this_version_no;
     $response['import']['stk_id']                = $stk_id;
     $response['import']['stk_name']              = $stk_name;
     $response['import']['dpn_extract_date']      = $dpn_extract_date;
     $response['import']['dpn_extract_user']      = $dpn_extract_user;
     $response['import']['smm_extract_user']      = $smm_extract_user;
     $response['import']['smm_extract_date']      = $smm_extract_date;
     // $response['import']['journal_text']          = $journal_text;
     $response['import']['rc_orig']               = $rc_orig;
     $response['import']['rc_orig_complete']      = $rc_orig_complete;
     $response['import']['rc_extras']             = $rc_extras;
     $response['import']['rc_totalSent']          = $rc_totalSent;
     $response['import']['results']               = $arr_asset;

     // print_r($response);
     fwrite($fp, json_encode($response));
     fclose($fp);
     if (file_exists($txt_file_link)) {
          echo "Opening file";
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.basename($txt_file_link));
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header('Content-Length: ' . filesize($txt_file_link));
         ob_clean();
         flush();
         readfile($txt_file_link);
         exit;
     }

}elseif ($act=='save_dearchive_stk'){
     $stkm_id = $_GET["stkm_id"];
     $sql_save = "UPDATE smartdb.sm13_stk SET smm_delete_date=null,smm_delete_user=null WHERE stkm_id = $stkm_id;";
     echo $sql_save;
     mysqli_multi_query($con,$sql_save);
     header("Location: index.php");


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

}elseif ($act=='save_archive_return'){
     $stkm_id = $_GET["stkm_id"];  

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql = "UPDATE smartdb.sm13_stk SET smm_delete_date=NOW(),smm_delete_user='$active_profile_id' WHERE stkm_id = $stkm_id; ";
     $sql .= "UPDATE smartdb.sm14_ass SET stk_include=NULL WHERE stkm_id = $stkm_id; ";
     // echo $sql_save;
     echo runSql($sql);

























// ####################################################################################
// Asset page actions
// ####################################################################################
}elseif ($act=='save_photo'){
     $ass_id   = $_POST["ass_id"];
     $input    = $_POST["res_img_data"];

     $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id = ".$ass_id."; ";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $res_asset_id           = $row["res_asset_id"];
               $res_fingerprint        = $row["res_fingerprint"];
     }}
     if ($Asset=="First found") {
          $photo_name              = "images/".$res_fingerprint;
     }else{
          $photo_name              = "images/".$res_asset_id;
     }
     $original_photo_name     = $photo_name;
     $counter = 1;
     $photo_name              = $photo_name.'_'.$counter.'.jpg';
     while (file_exists($photo_name)) {
          $counter++;
          $photo_name = $original_photo_name.'_'.$counter.'.jpg';
     }
     file_put_contents($photo_name, file_get_contents($input));
     header("Location: 11_ass.php?current_row=$current_row&ass_id=".$ass_id);


}elseif ($act=='save_ResetAssetResults'){ 
     $ass_id        = $_POST["ass_id"];  


     $sql = "  UPDATE smartdb.sm14_ass SET 
               res_AssetDesc1 = AssetDesc1,
               res_AssetDesc2 = AssetDesc2,
               res_AssetMainNoText = AssetMainNoText,
               res_Class = Class,
               res_assetType = assetType,
               res_Inventory = Inventory,
               res_Quantity = Quantity,
               res_SNo = SNo,
               res_InventNo = InventNo,
               res_accNo = accNo,
               res_Location = Location,
               res_Room = Room,
               res_State = State,
               res_latitude = latitude,
               res_longitude = longitude,
               res_CurrentNBV = CurrentNBV,
               res_AcqValue = AcqValue,
               res_OrigValue = OrigValue,
               res_ScrapVal = ScrapVal,
               res_ValMethod = ValMethod,
               res_RevOdep = RevOdep,
               res_CapDate = CapDate,
               res_LastInv = LastInv,
               res_DeactDate = DeactDate,
               res_PlRetDate = PlRetDate,
               res_CCC_ParentName = CCC_ParentName,
               res_CCC_GrandparentName = CCC_GrandparentName,
               res_GrpCustod = GrpCustod,
               res_CostCtr = CostCtr,
               res_WBSElem = WBSElem,
               res_Fund = Fund,
               res_RspCCtr = RspCCtr,
               res_CoCd = CoCd,
               res_PlateNo = PlateNo,
               res_Vendor = Vendor,
               res_Mfr = Mfr,
               res_UseNo = UseNo,
               res_reason_code = NULL
               WHERE ass_id = $ass_id;";
     // echo $sql;
     runSql($sql);

     $data = [];
     $sql = "SELECT * FROM smartdb.sm14_ass WHERE ass_id=$ass_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
      while($r = $result->fetch_assoc()) {
         $data["asset"] = $r;
         $stkm_id = $r['stkm_id'];
     }}
     
     $reasoncodes = [];
     $sql = "SELECT * FROM smartdb.sm15_rc ";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
      while($r = $result->fetch_assoc()) {
         $data["reasoncodes"][] = $r;
     }}
     $data = json_encode($data);
     echo $data;
     fnStats($stkm_id);


}elseif ($act=='save_AssetFieldValue'){
     $fieldName     = $_POST["fieldName"];  
     $fieldValue    = $_POST["fieldValue"]; 
     $ass_id        = $_POST["ass_id"];  
     $fingerprint   = time();
     $sqlUpdateFingerprint='';
     if ($fieldName=="res_reason_code"){ 
          $sqlUpdateFingerprint = ", fingerprint=$fingerprint, res_create_date=NOW()";
     }
     $sql = "UPDATE smartdb.sm14_ass SET $fieldName='$fieldValue' $sqlUpdateFingerprint WHERE ass_id = $ass_id;";
     runSql($sql);
     $sql = "SELECT $fieldName, stkm_id FROM smartdb.sm14_ass  WHERE ass_id = $ass_id;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $confirmedValue	= $row[$fieldName];
               $stkm_id	          = $row["stkm_id"];
     }}
     echo $confirmedValue;
     fnStats($stkm_id);

}elseif ($act=='save_delete_photo'){
     $photo_filename     = "images/".$_GET["photo_filename"];
     $ass_id             = $_GET["ass_id"];
     echo $photo_filename;
     $myFileLink = fopen($photo_filename, 'w') or die("can't open file");
     fclose($myFileLink);
     unlink($photo_filename) or die("Couldn't delete file");

     header("Location: 11_ass.php?current_row=$current_row&ass_id=".$ass_id);



























// ####################################################################################
// Template actions
// ####################################################################################

}elseif ($act=='get_templates'){

     $data = [];
     $sql = "SELECT ass_id, res_AssetDesc1 FROM smartdb.sm14_ass WHERE flagTemplate=1 AND delete_date IS NULL ORDER BY AssetDesc1";
     // echo $sql;
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $data[] = $row;
          // $listTmplt .= "<a class='dropdown-item' href='05_action.php?act=save_usetemplate&ass_id=$ass_id'>ASASS$res_AssetDesc1</a>";
     }}
     $data = json_encode($data);
     echo $data;


}elseif ($act=='save_RemoveTemplate'){
     $ass_id = $_POST["ass_id"];  
     $sql = "UPDATE smartdb.sm14_ass SET delete_date=NOW(),delete_user='$active_profile_id' WHERE ass_id = $ass_id;";
     echo runSql($sql);


}elseif ($act=='save_CreateTemplateAsset') {
     $ass_id        = $_POST["ass_id"];


}elseif ($act=='save_createtemplatefile') {

     $sql = "  INSERT INTO smartdb.sm13_stk (stk_id, stk_name,stk_type) VALUES (0,'template','template')";
     runSql($sql);
     header("Location: index.php");


}elseif ($act=='save_add_to_template') {
     $stkm_id       = $_GET["stkm_id"];//Template id
     $ass_id        = $_GET["ass_id"];

     $fingerprint        = time();
     $sql = " INSERT INTO smartdb.sm14_ass (create_date, stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
     SELECT Now(), $stkm_id, storage_id, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, '$fingerprint', res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_impairment_completed, res_completed, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
     FROM smartdb.sm14_ass
     WHERE ass_id =$ass_id ;";
     // echo "<br><br><br>$sql";
     runSql($sql);
     header("Location: 11_ass.php?current_row=$current_row&ass_id=".$ass_id);

}elseif ($act=='save_initiate_template') {
     $ass_id        = $_POST["ass_id"];
     $stkm_id       = $_POST["stkm_id"];
     $fingerprint   = time();
     echo "<br><br>ass_id:$ass_id";

     $sql = " INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, ledger_id, stk_include, rr_id, sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_name, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method, sto_ccc, sto_ccc_name, sto_ccc_parent, sto_ccc_parent_name, sto_wbs, sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_revodep, sto_date_lastinv,  sto_date_cap, sto_date_pl_ret, sto_date_deact, sto_loc_latitude, sto_loc_longitude, genesis_cat, res_create_date, res_create_user, res_fingerprint, res_reason_code, res_rc_desc, res_comment, res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_name, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method, res_ccc, res_ccc_name, res_ccc_parent, res_ccc_parent_name, res_wbs, res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_revodep, res_date_lastinv,  res_date_cap, res_date_pl_ret, res_date_deact, res_loc_latitude, res_loc_longitude)
     SELECT NOW(), create_user, delete_date, delete_user, ".$stkm_id.", ledger_id, stk_include, rr_id, sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_name, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method, sto_ccc, sto_ccc_name, sto_ccc_parent, sto_ccc_parent_name, sto_wbs, sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_revodep, sto_date_lastinv,  sto_date_cap, sto_date_pl_ret, sto_date_deact, sto_loc_latitude, sto_loc_longitude, 'nonoriginal', res_create_date, res_create_user, '$fingerprint', res_reason_code, res_rc_desc, res_comment, res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_name, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method, res_ccc, res_ccc_name, res_ccc_parent, res_ccc_parent_name, res_wbs, res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_revodep, res_date_lastinv,  res_date_cap, res_date_pl_ret, res_date_deact, res_loc_latitude, res_loc_longitude 
     FROM smartdb.sm14_ass
     WHERE ass_id =$ass_id ;";
     runSql($sql);

     echo "<br><br>$sql";
     $sql = "SELECT ass_id FROM smartdb.sm14_ass ORDER BY ass_id DESC LIMIT 1";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
          $new_ass_id	= $row["ass_id"];
     }}
     echo "<br><br>new_ass_id: $new_ass_id";
     header("Location: 11_ass.php?current_row=$current_row&ass_id=".$new_ass_id);

















// ####################################################################################
// Raw remainder actions
// ####################################################################################
}elseif ($act=='get_check_upload_rr') {
     $sql = "SELECT count(*) AS rowcount_rr FROM smartdb.sm12_rwr;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $rowcount_rr   = $row["rowcount_rr"];
     }}
     echo $rowcount_rr;

}elseif ($act=='save_rr_add') {
     $rr_id    = $_GET["rr_id"];
     $stkm_id  = $_GET["stkm_id"];

     $create_user = "";
     $fingerprint = TIME();

     $sql = " INSERT INTO smartdb.sm14_ass (create_date, create_user,
     stkm_id, res_asset_id, res_assetdesc1, rr_id, genesis_cat, res_create_date, res_create_user, res_reason_code, res_class, res_comment, res_fingerprint, stk_include, sto_assetdesc1, sto_class)
     SELECT Now(), '$create_user', $stkm_id, Asset, AssetDesc1, rr_id, 'nonoriginal', Now(), '$create_user', 'AF20', Class, ParentName, '$fingerprint', 1,  AssetDesc1, Class  FROM smartdb.sm12_rwr WHERE rr_id=$rr_id;";
     mysqli_multi_query($con,$sql);
     // echo $sql;

     $sql = "SELECT MAX(ass_id) AS ass_id FROM smartdb.sm14_ass;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $ass_id   = $row["ass_id"];
     }}

     $sql_save = "UPDATE smartdb.sm12_rwr SET rr_included=1 WHERE rr_id='$rr_id';";
     mysqli_multi_query($con,$sql_save);
     fnStats($stkm_id);
     header("Location: 11_ass.php?current_row=$current_row&ass_id=".$ass_id);

}elseif ($act=='get_rawremainder_asset_count') {
     $search_term = $_POST["search_term"];
     $res02 = "";
     $rr_asset_count = 0;
     $sql = "SELECT COUNT(*) AS rr_asset_count FROM smartdb.sm12_rwr WHERE Asset LIKE '%".$search_term."%' OR InventNo LIKE '%".$search_term."%' OR  AssetDesc1 LIKE '%".$search_term."%' ;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
               $rr_asset_count = $row["rr_asset_count"];
     }}
     if ($rr_asset_count>0) {
          $msg_rr_count = "This search also matched <a href='14_rr.php?search_term=".$search_term."'>".$rr_asset_count."</a> results in the raw remainder dataset.";
     }else{
          $msg_rr_count = "This search did not match anything in the raw remainder dataset.";
     }
     $res02 = $msg_rr_count;
     echo $res02;

































// ####################################################################################
// First found actions
// ####################################################################################
}elseif ($act=='save_newfirstfound'){
     $res_reason_code    = $_POST["res_reason_code"];
     $stkm_id            = $_POST["stkm_id"];
     $asset_template     = $_POST["asset_template"];
     
     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $fingerprint        = time();
     $sql_save=" INSERT INTO smartdb.sm14_ass 
     (stkm_id, create_date, create_user, stk_include, res_asset_id, genesis_cat, res_create_date, res_create_user,
     res_reason_code, res_assetdesc1, res_fingerprint) 
     VALUES('".$stkm_id."', NOW(), '".$active_profile_id."',1,'firstfound','nonoriginal',NOW(), '".$active_profile_id."','".$res_reason_code."', '".$asset_template."','$fingerprint'); ";
     mysqli_multi_query($con,$sql_save);
     echo "<br><br>".$sql_save;

     $sql = "SELECT * FROM smartdb.sm14_ass ORDER BY ass_id DESC LIMIT 1;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $new_ass_id    = $row["ass_id"];
     }}

     header("Location: 11_ass.php?ass_id=".$new_ass_id);

}elseif ($act=='save_delete_first_found'){
     $ass_id             = $_GET["ass_id"];

     $sql = "SELECT * FROM smartdb.sm10_set;";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
             $active_profile_id    = $row["active_profile_id"];
     }}

     $sql_save = "UPDATE smartdb.sm14_ass SET delete_date=NOW(), delete_user='$active_profile_id' WHERE ass_id = $ass_id;";
     echo $sql_save;
     if (!mysqli_multi_query($con,$sql_save)){
          $save_error = mysqli_error($con);
          echo 'failure'.$save_error;
     }else{
          echo 'success';     
     }
     header("Location: 10_stk.php");


// ####################################################################################
// IS actions
// ####################################################################################
}elseif ($act=='save_msi_bin_stk') {
     $findingID          = $_POST["findingID"];
     $auto_storageID     = $_POST["auto_storageID"];
     $storageID          = $_POST["storageID"];
     $stkm_id            = $_POST["stkm_id"];
     $res_update_user    = "";
     $milisFlag          = $_POST["checked_to_milis"] == "1"? 1 : 0;
     $findingID          = $_POST["findingID"];
     function clnr($fieldVal){
          // echo "<br>".$fieldVal;
          if(empty($fieldVal)&&$fieldVal==''){
               $fieldVal    = 'null';
          }else{
               $fieldVal = str_replace("'","''",$fieldVal);
               $fieldVal    = "'".$fieldVal."'";
          }
          return $fieldVal;
     }
     // print_r($_POST);
     //Delete children
     $sql = "DELETE FROM smartdb.sm18_impairment WHERE res_parent_storageID='$storageID' ";
     runSql($sql);

     $fingerprint        = time();
     if($findingID==11){

          foreach ($_POST["splityRecord"] as $key => $value) {
               $splityCount   = $_POST['splityCount'][$value];
               $splityResult  = $_POST['splityResult'][$value];
               $splityDate    = $_POST['splityDate'][$value];
               // echo "<br><b>".$splityCount." - ".$splityResult." - ".$splityDate."</b>";
               if(!empty($splityDate)){
                    $splityDate = clnr($splityDate);    
               }else{
                    $splityDate = 'null';
               }
               $sql = "  INSERT INTO smartdb.sm18_impairment (
                              res_create_date, 
                              res_update_user, 
                              findingID, 
                              res_unserv_date, 
                              res_parent_storageID, 
                              data_source,
                              SOH, 
                              fingerprint, 
                              isType, 
                              stkm_id)
                         VALUES (
                              NOW(),'$res_update_user','$splityResult',$splityDate,
                              '$storageID','extra','$splityCount','$fingerprint','imp', '$stkm_id')";
               runSql($sql);
               echo "<br>$sql";
          }

          $sql = "  UPDATE smartdb.sm18_impairment AS tblEdit, smartdb.sm18_impairment AS tblSource 
               SET 	tblEdit.DSTRCT_CODE = tblSource.DSTRCT_CODE,
                    tblEdit.WHOUSE_ID = tblSource.WHOUSE_ID,
                    tblEdit.SUPPLY_CUST_ID = tblSource.SUPPLY_CUST_ID,
                    tblEdit.SC_ACCOUNT_TYPE = tblSource.SC_ACCOUNT_TYPE,
                    tblEdit.STOCK_CODE = tblSource.STOCK_CODE,
                    tblEdit.ITEM_NAME = tblSource.ITEM_NAME,
                    tblEdit.STK_DESC = tblSource.STK_DESC,
                    tblEdit.BIN_CODE = tblSource.BIN_CODE,
                    tblEdit.INVENT_CAT = tblSource.INVENT_CAT,
                    tblEdit.TRACKING_IND = tblSource.TRACKING_IND,
                    tblEdit.TRACKING_REFERENCE = tblSource.TRACKING_REFERENCE,
                    tblEdit.LAST_MOD_DATE = tblSource.LAST_MOD_DATE,
                    WHERE 	tblEdit.res_parent_storageID = tblSource.storageID
                    AND tblEdit.storageID IS NULL
                    AND tblSource.auto_storageID=$auto_storageID ";
          runSql($sql);
     }

     if(!empty($_POST['res_unserv_date'])){
          $res_unserv_date = clnr($_POST['res_unserv_date']);    
     }else{
          $res_unserv_date = 'null';
     }

     $res_comment = clnr($_POST["res_comment"]);

     $sql = "UPDATE smartdb.sm18_impairment SET 
               findingID='$findingID',  
               res_comment=$res_comment,  
               res_unserv_date=$res_unserv_date,
               res_create_date=NOW(),
               fingerprint='$fingerprint',
               checked_to_milis='$milisFlag'
            WHERE 
               auto_storageID='$auto_storageID' ";
     runSql($sql);
     fnStats($stkm_id);

     header("Location: 16_imp.php?current_row=$current_row&auto_storageID=".$auto_storageID);

}elseif ($act=='save_clear_msi_bin') {
     $auto_storageID     = $_GET["auto_storageID"];
     $storageID          = $_GET["storageID"];

     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NULL,
     res_update_user=NULL,
     findingID=NULL,  
     res_evidence_desc=NULL,
     res_unserv_date=NULL,
     fingerprint=NULL
     WHERE 
     auto_storageID='$auto_storageID' ";
     // echo $sql;
     runSql($sql);

     $sql = "DELETE FROM smartdb.sm18_impairment WHERE res_parent_storageID='$storageID' ";
     runSql($sql);

     header("Location: 16_imp.php?current_row=$current_row&auto_storageID=".$auto_storageID);

}elseif ($act=='save_b2r_nstr') {
     $BIN_CODE = $_GET["BIN_CODE"];
     $stkm_id  = $_GET["stkm_id"];

     $fingerprint        = time();
     // 100 indicates NSTR
     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NOW(),
     res_update_user=NULL,
     findingID=14,
     fingerprint='$fingerprint'
     WHERE BIN_CODE='$BIN_CODE' AND isType='b2r' AND stkm_id=$stkm_id";
     runSql($sql);

     fnStats($stkm_id);
     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_b2r_extras') {
     $BIN_CODE = $_GET["BIN_CODE"];
     $stkm_id  = $_GET["stkm_id"];
     $fingerprint        = time();
     $sql = "UPDATE smartdb.sm18_impairment SET 
     findingID=15,
     fingerprint='$fingerprint'
     WHERE BIN_CODE='$BIN_CODE'  AND isType='b2r' AND stkm_id=$stkm_id";
     runSql($sql);
     fnStats($stkm_id);
     header("Location: 17_b2r.php?BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");
     
}elseif ($act=='save_clear_b2r') {
     $BIN_CODE       = $_GET["BIN_CODE"];
     $stkm_id        = $_GET["stkm_id"];

     $sql = "UPDATE smartdb.sm18_impairment SET 
     res_create_date=NULL,
     res_update_user=NULL,
     findingID=NULL,  
     res_comment=NULL,  
     res_evidence_desc=NULL,
     res_unserv_date=NULL,
     fingerprint=NULL
     WHERE BIN_CODE='$BIN_CODE'  
     AND isType='b2r'
     AND stkm_id=$stkm_id";
     runSql($sql);

     echo $sql;
     $sql = "DELETE FROM smartdb.sm18_impairment WHERE BIN_CODE='$BIN_CODE' AND isChild=1 AND isType='b2r' AND stkm_id=$stkm_id";
     runSql($sql);

     fnStats($stkm_id);
     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_delete_extra') {
     $auto_storageID     = $_GET["auto_storageID"];
     $BIN_CODE           = $_GET["BIN_CODE"];
     $stkm_id            = $_GET["stkm_id"];
     $sql = "UPDATE smartdb.sm18_impairment SET delete_date=NOW() WHERE auto_storageID=$auto_storageID"; 
     // echo $sql;
     runSql($sql);
     checkExtrasFinished($BIN_CODE, $stkm_id);
     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_b2r_add_extra') {
     $auto_storageID     = $_POST["auto_storageID"];
     $BIN_CODE           = $_POST["BIN_CODE"];
     $extraStockcode     = $_POST["extraStockcode"];
     $extraName          = $_POST["extraName"];
     $extraSOH           = $_POST["extraSOH"];
     $stkm_id            = $_POST["stkm_id"];
     $extraComments      = $_POST["extraComments"];

     $fingerprint        = time();
     $res_update_user='';
     echo "<br>auto_storageID: $auto_storageID<br>";
     if($auto_storageID==0){
          $DSTRCT_CODE        = $_POST["DSTRCT_CODE"];
          $WHOUSE_ID          = $_POST["WHOUSE_ID"];
          $sql = "  INSERT INTO smartdb.sm18_impairment (
               res_create_date,
               res_update_user,
               stkm_id,
               BIN_CODE, 
               DSTRCT_CODE, 
               WHOUSE_ID, 
               STOCK_CODE, 
               ITEM_NAME, 
               SOH, 
               isChild,
               isType,
               fingerprint,
               res_comment)
          VALUES (
               NOW(),
               '$res_update_user',
               '$stkm_id',
               '$BIN_CODE',
               '$DSTRCT_CODE',
               '$WHOUSE_ID',
               '$extraStockcode',
               '$extraName',
               '$extraSOH',
               1,
               'b2r',
               '$fingerprint',
               '$extraComments'
               )";         
     }else{
          $sql = "UPDATE smartdb.sm18_impairment SET
          STOCK_CODE = '$extraStockcode',
          ITEM_NAME = '$extraName',
          SOH = '$extraSOH',
          res_comment = '$extraComments'
          WHERE auto_storageID=$auto_storageID"; 
     }
     echo $sql;
     runSql($sql);
     checkExtrasFinished($BIN_CODE, $stkm_id);
     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_b2r_extra') {
     $auto_storageID     = $_POST["auto_storageID"];
     $finalResult        = $_POST["finalResult"];
     $finalResultPath    = $_POST["finalResultPath"];
     $BIN_CODE           = $_POST["BIN_CODE"];
     $stkm_id            = $_POST["stkm_id"];

     $sql = "UPDATE smartdb.sm18_impairment SET 
     finalResult='$finalResult',
     finalResultPath='".$finalResultPath."'
     WHERE auto_storageID='$auto_storageID' ";
     echo runSql($sql);
     checkExtrasFinished($BIN_CODE, $stkm_id);

}elseif ($act=='save_clear_b2r_extra') {
     $auto_storageID     = $_GET["auto_storageID"];
     $BIN_CODE           = $_GET["BIN_CODE"];
     $stkm_id            = $_GET["stkm_id"];
     $sql = "UPDATE smartdb.sm18_impairment SET 
     finalResult=NULL,
     finalResultPath=NULL
     WHERE auto_storageID='$auto_storageID' ";
     echo runSql($sql);
     checkExtrasFinished($BIN_CODE, $stkm_id);

     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");

}elseif ($act=='save_is_toggle_check') {
     $toggle        = $_GET["toggle"];
     $STOCK_CODE    = $_GET["STOCK_CODE"];
     $BIN_CODE      = $_GET["BIN_CODE"];
     $stkm_id       = $_GET["stkm_id"];
     $sql = "UPDATE smartdb.sm18_impairment SET checkFlag=$toggle WHERE STOCK_CODE='$STOCK_CODE' AND BIN_CODE='$BIN_CODE'";
     echo runSql($sql);

     header("Location: 17_b2r.php?current_row=$current_row&BIN_CODE=$BIN_CODE&stkm_id=$stkm_id");
     
}elseif ($act=='save_toggle_imp_backup') {
     $stkm_id       = $_GET["stkm_id"];
     $targetID      = $_GET["targetID"];
     $BIN_CODE      = $_GET["BIN_CODE"];
     $STOCK_CODE    = $_GET["STOCK_CODE"];
     $isType        = $_GET["isType"];
     $isBackup      = $_GET["isBackup"];
     
     if ($isBackup==1){
          $isBackup = 1;
     }else{
          $isBackup = "NULL";
     }

     $sql = "UPDATE smartdb.sm18_impairment SET isBackup=$isBackup WHERE targetID='$targetID' AND stkm_id='$stkm_id' ";
     if(($isType=="imps") || ($isType=="impq")){
          $sql .= " AND STOCK_CODE='$STOCK_CODE' AND LEFT(isType,3)='imp' ";
     }else{
          $sql .= " AND BIN_CODE='$BIN_CODE' AND isType='b2r' ";
     }
     echo $sql;
     echo runSql($sql);
     fnStats($stkm_id);
     header("Location: 19_toggle.php?current_row=$current_row");









     





























// ####################################################################################
// Merge actions
// ####################################################################################
}elseif ($act=='save_merge_initiate') {
     $debugMode = false;
     // Ascertain what type of stocktake it is - GA or IS
     // Get details of existing stocktakes - name, counts
     // Create new stocktake
     // Add all good rows to table
     //Show user rows which need comparison

     $log = !$debugMode ? $log: "<br><br>Getting details of two stocktakes being merged";
     $cherry = 0;
     $sql = "SELECT * FROM smartdb.sm13_stk WHERE  stk_include = 1 and smm_delete_date IS NULL";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
               if ($cherry==0){
                    $cherry=1;
                    $stkm_id_one    = $row["stkm_id"];
               }else{
                    $stkm_id_two    = $row["stkm_id"];
               }
               $stk_id             = $row["stk_id"];
               $stk_name           = $row["stk_name"];
               $dpn_extract_date   = $row["dpn_extract_date"];
               $stk_type           = $row["stk_type"];
     }}



     echo "Strpos".strpos($stk_name, "MERGE");
     if(strpos($stk_name, "MERGE")===false){
          $stk_name_disp = "MERGE_$stk_name";
     }else{
          $stk_name_disp = $stk_name;
     }



     $log .= !$debugMode ? $log: "<br><br>Creating new stocktake record";
     $sql = "  INSERT INTO smartdb.sm13_stk (stk_id, stk_name,dpn_extract_date,stk_type, merge_lock)
     VALUES ('$stk_id','$stk_name_disp','$dpn_extract_date','$stk_type', 1)";
     // echo "<br><br><br>$sql";
     runSql($sql);

     $sql = "SELECT MAX(stkm_id) AS new_stkm_id  FROM smartdb.sm13_stk";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {    
          $new_stkm_id    = $row['new_stkm_id'];   
     }}

     if ($stk_type=="impairment"){
          $sql1 = "(SELECT auto_storageID, storageID, fingerprint FROM smartdb.sm18_impairment WHERE stkm_id=$stkm_id_one) AS vtsql1";
          $sql2 = "(SELECT auto_storageID, storageID, fingerprint FROM smartdb.sm18_impairment WHERE stkm_id=$stkm_id_two) AS vtsql2";
          
          $sql_a = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2, 
                      'Full match', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          
          $sql_b = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'Only STK1 result', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_c = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,   
                      'Only STK2 result', vtsql2.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          
          $sql_d = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF match', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          
          $sql_e = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF stk1', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_f = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF stk2', vtsql2.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID IS NULL
                      AND vtsql2.storageID IS NULL
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          
          $sql_g = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,  
                      'No result', vtsql1.auto_storageID
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NULL";
          
          $sql_h = "  SELECT vtsql1.storageID AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storageID AS stID2, vtsql2.fingerprint AS fp2,   
                      'Needs comparison', vtsql1.auto_storageID AS asID1, vtsql2.auto_storageID AS asID2
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storageID = vtsql2.storageID
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NOT NULL
                      AND vtsql1.fingerprint <> vtsql2.fingerprint";
          $sql_allgood        = "$sql_a UNION $sql_b UNION $sql_c UNION $sql_d UNION $sql_e UNION $sql_f UNION $sql_g ";
          $sql_needscomparison= $sql_h;

          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $new_stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE auto_storageID IN (SELECT auto_storageID FROM ($sql_allgood) AS vt_merge_allgood);";
          // echo "<br><br><br>$sql";
          runSql($sql);


          $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
          SELECT $new_stkm_id, asID1, asID2 FROM ($sql_needscomparison) AS vtCompare;";
          // echo "<br><br><br>$sql";
          runSql($sql);



          $nextAddr = "Location: index.php";


     }elseif($stk_type=="stocktake"){
          $log .= !$debugMode ? $log: "<br><br>This is a stocktake merge action";
          $sql1 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one) AS vtsql1";
          $sql2 = "(SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two) AS vtsql2";
          
          
          $log .= !$debugMode ? $log: "<br><br>The following are category sqls for types of merge candidates";
          $sql_a = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2, 
                      'Storage match', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND  vtsql1.fingerprint = vtsql2.fingerprint";
          $log .= !$debugMode ? $log: "<br><br><br><b>Storage match</b><br>$sql_a";

          $sql_b = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'Storage result - only STK1', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NULL";
          $log .= !$debugMode ? $log: "<br><br><br><b>Storage result - only STK1</b><br>$sql_b";
          
          $sql_c = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
                      'Storage result - only STK2', vtsql2.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NOT NULL";
          $log .= !$debugMode ? $log: "<br><br><br><b>Storage result - only STK2</b><br>$sql_c";
          
          $sql_d = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'FF match', vtsql1.ass_id
                      FROM 
                         (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
                         (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
                      WHERE vtsql1.fingerprint = vtsql2.fingerprint";
          $log .= !$debugMode ? $log: "<br><br><br><b>FF match</b><br>$sql_d";

          $sql_e = "     SELECT NULL AS stID1, fingerprint AS fp1, NULL AS stID2, NULL AS fp2,
          'FF stk1', ass_id
          FROM smartdb.sm14_ass 
          WHERE stkm_id = $stkm_id_one 
          AND storage_id IS NULL
          AND fingerprint IS NOT NULL
          AND ass_id NOT IN (
              SELECT 
              vtsql1.ass_id
              FROM
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
              WHERE vtsql1.fingerprint = vtsql2.fingerprint
          )";          
          $log .= !$debugMode ? $log: "<br><br><br><b>FF stk1</b><br>$sql_e";
          
          $sql_f = "     SELECT NULL AS stID1, NULL AS fp1, NULL AS stID2, fingerprint AS fp2,
          'FF stk2', ass_id
          FROM smartdb.sm14_ass 
          WHERE stkm_id = $stkm_id_two 
          AND storage_id IS NULL
          AND fingerprint IS NOT NULL
          AND ass_id NOT IN (
               SELECT 
               vtsql2.ass_id
               FROM
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_one AND storage_id IS NULL) AS vtsql1,
              (SELECT ass_id, storage_id, fingerprint FROM smartdb.sm14_ass WHERE stkm_id=$stkm_id_two AND storage_id IS NULL) AS vtsql2
               WHERE vtsql1.fingerprint = vtsql2.fingerprint
          )";
          $log .= !$debugMode ? $log: "<br><br><br><b>FF stk2</b><br>$sql_f";
          
          $sql_g = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,  
                      'No result', vtsql1.ass_id
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NULL
                      AND vtsql2.fingerprint IS NULL";
          $log .= !$debugMode ? $log: "<br><br><br><b>No result</b><br>$sql_g";
          
          $sql_h = "  SELECT vtsql1.storage_id AS stID1, vtsql1.fingerprint AS fp1, 
                         vtsql2.storage_id AS stID2, vtsql2.fingerprint AS fp2,   
                      'Needs comparison', vtsql1.ass_id AS asID1, vtsql2.ass_id AS asID2
                      FROM $sql1, $sql2 
                      WHERE vtsql1.storage_id = vtsql2.storage_id
                      AND vtsql1.fingerprint IS NOT NULL
                      AND vtsql2.fingerprint IS NOT NULL
                      AND vtsql1.fingerprint <> vtsql2.fingerprint";
          $log .= !$debugMode ? $log: "<br><br><br><b>Needs comparison</b><br>$sql_h";


          $sql_allgood        = "$sql_a UNION $sql_b UNION $sql_c UNION $sql_d UNION $sql_e UNION $sql_f UNION $sql_g ";
          $log .= !$debugMode ? $log: "<br><br><br><b>sql_allgood</b>$sql_allgood";

          $sql_needscomparison= $sql_h;
          $log .= !$debugMode ? $log: "<br><br><br><b>sql_needscomparison</b><br>$sql_h";

          $sql = "  INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
          SELECT create_date, create_user, delete_date, delete_user, $new_stkm_id, storage_id, 0, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
          FROM smartdb.sm14_ass
          WHERE ass_id IN (SELECT ass_id FROM ($sql_allgood) AS vt_merge_allgood);";
          $log .= !$debugMode ? $log: "<br><br><br><b>Insert all good</b><br>$sql";

          if (!$debugMode){
               runSql($sql);
          }

          $sql = "  INSERT INTO smartdb.sm20_quarantine (stkm_id, auto_storageID_one, auto_storageID_two)
          SELECT $new_stkm_id, asID1, asID2 FROM ($sql_needscomparison) AS vtCompare;";
          $log .= !$debugMode ? $log: "<br><br><br><b>Insert needs comparison into quarantine</b><br>$sql";
          

          if (!$debugMode){
               runSql($sql);
          }else{
               echo $log;
          }


          $sql = "SELECT COUNT(*) AS qCount FROM smartdb.sm20_quarantine WHERE stkm_id = '$new_stkm_id'";
          $result = $con->query($sql);
          if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {    
               $qCount    = $row['qCount'];  
          }}
     
          $qCount = (empty($qCount) ? 0 : $qCount);
          if ($qCount>0){
               $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=1 WHERE stkm_id = $new_stkm_id;";
               $nextAddr = "Location: 20_merge.php?stkm_id=$new_stkm_id";
          }else{
               $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=NULL WHERE stkm_id = $new_stkm_id;";
               $nextAddr = "Location: index.php";
          }

          
          fnStats($new_stkm_id);



     }

     if (!$debugMode){
          runSql($sql);
          header($nextAddr);
     }

}elseif ($act=='save_merge_select') {
     $q_id     = $_GET["q_id"];
     $stmnum   = $_GET["stmnum"];

     $sql = "SELECT * FROM smartdb.sm20_quarantine WHERE q_id = $q_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
          $stkm_id_new             = $row["stkm_id_new"];
          $stkm_id_one             = $row["stkm_id_one"];
          $stkm_id_two             = $row["stkm_id_two"];
          $isType                  = $row["isType"];
          $pkID1                   = $row["pkID1"];
          $pkID2                   = $row["pkID2"];
          $BIN_CODE                = $row["BIN_CODE"];
          $res_pkID_selected       = $row["res_pkID_selected"];
          $res_stkm_id_selected    = $row["res_stkm_id_selected"];
          $complete_date           = $row["complete_date"];
     }}

     if($stmnum=="one"){
          $selectedStkm = $stkm_id_one;
          $selectedPkID = " pkID1 ";
     }elseif($stmnum=="two"){
          $selectedStkm = $stkm_id_two;
          $selectedPkID = " pkID2 ";
     }

     $sql = "  UPDATE smartdb.sm20_quarantine SET 
               complete_date = NOW(), 
               res_pkID_selected=$selectedPkID, 
               res_stkm_id_selected=$selectedStkm
               WHERE q_id = $q_id;";


     echo "<br><br><br>$sql";
     runSql($sql);
     
     header("Location: 22_merge.php?stkm_id=$stkm_id_new");

}elseif ($act=='save_merge_finalise') {
     $stkm_id                 = $_GET["stkm_id"];


     $sql = "SELECT * FROM smartdb.sm20_quarantine WHERE stkm_id_new = $stkm_id";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
          $stkm_id_one             = $row["stkm_id_one"];
          $stkm_id_two             = $row["stkm_id_two"];
     }}



     $sql = "SELECT * FROM smartdb.sm13_stk WHERE  stk_include = 1";
     $result = $con->query($sql);
     if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
               $stk_type           = $row["stk_type"];
     }}

     if($stk_type=="impairment"){
          $sub = "SELECT res_pkID_selected FROM smartdb.sm20_quarantine WHERE stkm_id_new = $stkm_id AND isType='imp'";
          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID,  DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE auto_storageID IN ($sub); ";
          runSql($sql);

          $sub = "SELECT BIN_CODE FROM smartdb.sm20_quarantine WHERE res_stkm_id_selected = $stkm_id_one AND isType='b2r' AND stkm_id_new=$stkm_id";
          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE BIN_CODE IN ($sub) AND stkm_id=$stkm_id_one; ";
          runSql($sql);

          $sub = "SELECT BIN_CODE FROM smartdb.sm20_quarantine WHERE res_stkm_id_selected = $stkm_id_two AND isType='b2r' AND stkm_id_new=$stkm_id";
          $sql = "  INSERT INTO smartdb.sm18_impairment (stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint)
          SELECT $stkm_id, storageID, DSTRCT_CODE, WHOUSE_ID, SUPPLY_CUST_ID, SC_ACCOUNT_TYPE, STOCK_CODE, ITEM_NAME, STK_DESC, BIN_CODE, INVENT_CAT, INVENT_CAT_DESC, TRACKING_IND, SOH, TRACKING_REFERENCE, LAST_MOD_DATE, sampleFlag, serviceableFlag, isBackup, isType, targetID, delete_date, delete_user, res_create_date, res_update_user, findingID, res_comment, res_evidence_desc, res_unserv_date, isChild, res_parent_storageID, finalResult, finalResultPath, fingerprint
          FROM smartdb.sm18_impairment
          WHERE BIN_CODE IN ($sub) AND stkm_id=$stkm_id_two; ";
          runSql($sql);


          
     }elseif($stk_type=="stocktake"){
          $sub = "SELECT res_pkID_selected FROM smartdb.sm20_quarantine WHERE stkm_id_new = $stkm_id";
          $sql = "  INSERT INTO smartdb.sm14_ass (create_date, create_user, delete_date, delete_user, stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo)
          SELECT create_date, create_user, delete_date, delete_user, $stkm_id, storage_id, stk_include, Asset, Subnumber, genesis_cat, first_found_flag, rr_id, fingerprint, res_create_date, res_create_user, res_reason_code, res_reason_code_desc, res_comment, AssetDesc1, AssetDesc2, AssetMainNoText, Class, assetType, Inventory, Quantity, SNo, InventNo, accNo, Location, Room, State, latitude, longitude, CurrentNBV, AcqValue, OrigValue, ScrapVal, ValMethod, RevOdep, CapDate, LastInv, DeactDate, PlRetDate, CCC_ParentName, CCC_GrandparentName, GrpCustod, CostCtr, WBSElem, Fund, RspCCtr, CoCd, PlateNo, Vendor, Mfr, UseNo, res_AssetDesc1, res_AssetDesc2, res_AssetMainNoText, res_Class, res_assetType, res_Inventory, res_Quantity, res_SNo, res_InventNo, res_accNo, res_Location, res_Room, res_State, res_latitude, res_longitude, res_CurrentNBV, res_AcqValue, res_OrigValue, res_ScrapVal, res_ValMethod, res_RevOdep, res_CapDate, res_LastInv, res_DeactDate, res_PlRetDate, res_CCC_ParentName, res_CCC_GrandparentName, res_GrpCustod, res_CostCtr, res_WBSElem, res_Fund, res_RspCCtr, res_CoCd, res_PlateNo, res_Vendor, res_Mfr, res_UseNo
          FROM smartdb.sm14_ass
          WHERE ass_id IN ($sub)";
          echo "<br><br>".$sql;
          runSql($sql);
     }
     fnStats($stkm_id);

     $sql = "  UPDATE smartdb.sm13_stk SET merge_lock=NULL WHERE stkm_id = $stkm_id;";
     runSql($sql);


     // header("Location: index.php");

























































}elseif ($act=='testarea') {
     fnStats(7);

}
// echo $log;






















?>