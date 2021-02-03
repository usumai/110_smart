<?php

$isImpAbbrsWithWarningEnabled = ["USND"];
$isImpAbbrsWithMilisEnabled = ["USWD","USND"];
$isImpAbbrsCompletedStatus = ["SER","USWD","USND","NIC","SPLT"];
$isB2rAbbrsCompletedStatus = ["INV","NSTR"];

define ('GIT_CMD','"\Program Files\Git\bin\git"');
define ('HTTP_PROXY',"10.3.135.2:80");
define ('SMART_VERSION_URL',"https://raw.githubusercontent.com/usumai/110_smart/master/08_version.json");

define ('NET_OK','1');
define ('NET_NO_INTERNET','2');
define ('NET_HTTP_PROXY','3');
define ('NET_NO_SERVICE','4');

define ('DB_ERR_RECORD_EXIST',20000);
define ('DB_ERR_STATE','45000');

function getAPIAction() {
    if(!array_key_exists("CONTENT_TYPE",$_SERVER))
        return null;
    $index = strpos($_SERVER["CONTENT_TYPE"],";");    
    if($index>0) {
        $req_content_type=substr($_SERVER["CONTENT_TYPE"],0, $index);
    }else{
        $req_content_type=$_SERVER["CONTENT_TYPE"];
    }

    if ($req_content_type=="application/json") {
        $request=json_decode(file_get_contents('php://input'));
        if(!empty($request->action)){
            return $request;
        }
    }
    return null;   
}

function qget($sql){
    // Submits a basic sql and returns an array result
    global $con;
    $res = [];
    $result = $con->query($sql);

    if (is_a($result,'mysqli_result')){
		if($result->num_rows > 0) {
        	while($row = $result->fetch_assoc()) {
           		$res[] = $row;
    		}
		}
	}
    return $res;
}

function getFindingIDs($isType, $findingCodes){
	$codes="";
	$findingIDs=[];
	$first=0;
	foreach($findingCodes as $code){
		if($first==0){
			$codes.= "'".$code."'";
			$first=1;
		}else{
			$codes.= ",'".$code."'";
		}
	}
		
	$rows = qget("
				SELECT findingID 
				FROM smartdb.sm19_result_cats 
				WHERE 
					isType like '$isType' AND
					resAbbr in ($codes)");
					
	if(count($rows)>0){
		$index=0;
		foreach($rows as $rec){
			$findingIDs[$index++]=$rec["findingID"];
		}
	}
		
	return $findingIDs;
}

//convert array of findingID abbr code to a comma separate list of findingID string
function getFindingIDsString($isType, $findingCodes){
	$idArray=getFindingIDs($isType,  $findingCodes);
	$findingIDsString="";
	$first=0;
	foreach ($idArray as $findingID ){		
		if($first==0){		
			$findingIDsString .= $findingID;
			$first=1;			
		}else{
			$findingIDsString .= ",".$findingID;
		}
	}
	return $findingIDsString;
}

function template($filename){
	$content = file_get_contents($filename.".vue");
	$content = str_replace("\r\n","",$content);
	return $content;
}
function execWithErrorHandler($callback){
    try {
        $callback();
    }catch(Throwable $e){
		$response = new ResponseMessage("ERROR", null);
	    $response->errors[0]=new ErrorInfo($e->getCode(),$e->getMessage());
	    echo json_encode($response);
    }    
}

function errorHandler($error){
	$response = new ResponseMessage("ERROR", null);
    $response->errors[0]=new ErrorInfo(0, $error ? $error->getMessage() : "Unknown system exception occured");
    echo json_encode($response);
}

function getSoftwareVersion(){
	$networkStatus=getNetworkStatus();
	
	$result['localVersion']=12;
	$result['localRevision']=str_replace("\n", "", shell_exec(GIT_CMD .' rev-parse --short HEAD'));	
	$result['remoteVersion']=12;
	$result['remoteRevision']='';

	if(($networkStatus == NET_NO_INTERNET)||
		($networkStatus == NET_NO_SERVICE)){
		Throw new Exception("No Network connection detected");
	}
	
	$URL = 'https://raw.githubusercontent.com/usumai/110_smart/master/08_version.json';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);

	if($networkStatus == NET_HTTP_PROXY){
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
	 	curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY);
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		shell_exec(GIT_CMD .' config http.proxy http://' . HTTP_PROXY);
		$result['remoteRevision']=shell_exec(GIT_CMD .' ls-remote https://github.com/usumai/110_smart.git HEAD');
		shell_exec(GIT_CMD .' config --unset http.proxy');
	}else{
	    shell_exec(GIT_CMD .' config --unset http.proxy');
		$result['remoteRevision']=shell_exec(GIT_CMD .' ls-remote https://github.com/usumai/110_smart.git HEAD');
	}

	$data = curl_exec($ch);
	if($data!=null){
	 	$json = json_decode($data, true);
	 	$result['remoteVersion']= $json["latest_version_no"];
	 	$result['remoteVersionDate'] = $json["version_publish_date"];
	}
	return $result;
}
function getNetworkStatus(){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, SMART_VERSION_URL);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);

	$data = curl_exec($ch);

	if($data != null){
		curl_close($ch);
		return NET_OK;
	}
	//Try with proxy server setting
	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
	curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$data = curl_exec($ch);        
	
	if($data!=null){
		curl_close($ch);
		return NET_HTTP_PROXY;
	}	

	if(!@fsockopen("www.example.com", 80)){
		curl_close($ch);
		return NET_NO_INTERNET;
	}
	
	return NET_NO_SERVICE;
}

class ResponseMessage {
    public $status;
    public $errors=[];
    public $result;
    public function __construct($status, $data){
        $this->status=$status;
        $this->result=$data;
    }    
}

class ErrorInfo {
    public $code;
    public $info;
    public function __construct($code, $info){
        $this->code=$code;
        $this->info=$info;
    }    
}

/*
class FileUploadProcessThread extends Thread {
    
    public function __construct($aList, $conn){
        $this->assetList=$aList;
        $this->connection=$conn;
    }
    public function run(){
        $stmt   = $this->connection->prepare("INSERT INTO smartdb.sm14_ass (
                create_date, create_user, stkm_id, ledger_id, rr_id,

                sto_asset_id, sto_assetdesc1, sto_assetdesc2, sto_assettext, sto_class, sto_class_ga_cat, sto_loc_location, sto_loc_room, sto_loc_state, sto_quantity, 
                sto_val_nbv, sto_val_acq, sto_val_orig, sto_val_scrap, sto_valuation_method,  sto_ccc, sto_ccc_name, sto_ccc_grandparent, sto_ccc_grandparentname, sto_wbs, 
                sto_fund, sto_responsible_ccc, sto_mfr, sto_inventory, sto_inventno, sto_serialno, sto_site_no, sto_grpcustod, sto_plateno, sto_date_lastinv, 
                sto_date_cap, sto_loc_latitude, sto_loc_longitude, 
                
                genesis_cat, res_create_date, res_create_user, res_fingerprint, res_reason_code, res_rc_desc, res_comment, 
                
                res_asset_id, res_assetdesc1, res_assetdesc2, res_assettext, res_class, res_class_ga_cat, res_loc_location, res_loc_room, res_loc_state, res_quantity, 
                res_val_nbv, res_val_acq, res_val_orig, res_val_scrap, res_valuation_method,  res_ccc, res_ccc_name, res_ccc_grandparent, res_ccc_grandparent_name, res_wbs, 
                res_fund, res_responsible_ccc, res_mfr, res_inventory, res_inventno, res_serialno, res_site_no, res_grpcustod, res_plateno, res_date_lastinv, 
                res_date_cap, res_loc_latitude, res_loc_longitude
        ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");

        foreach ($this->assetList as $key => $row) {
            $stmt   ->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss", 
                $row['create_date'],
                $row['create_user'],
                $new_stkm_id, 
                $row['ledger_id'],
                $row['rr_id'],
                $row['sto_asset_id'], $row['sto_assetdesc1'], $row['sto_assetdesc2'], $row['sto_assettext'], $row['sto_class'], 
                $row['sto_class_ga_cat'], $row['sto_loc_location'], $row['sto_loc_room'], $row['sto_loc_state'],  $row['sto_quantity'], 
                $row['sto_val_nbv'], $row['sto_val_acq'], $row['sto_val_orig'], $row['sto_val_scrap'], $row['sto_valuation_method'], 
                $row['sto_ccc'], $row['sto_ccc_name'], $row['sto_ccc_grandparent'], $row['sto_ccc_grandparentname'], $row['sto_wbs'], 
                $row['sto_fund'], $row['sto_responsible_ccc'], $row['sto_mfr'], $row['sto_inventory'], $row['sto_inventno'], 
                $row['sto_serialno'], $row['sto_site_no'], $row['sto_grpcustod'], $row['sto_plateno'], $row['sto_date_lastinv'], 
                $row['sto_date_cap'], $row['sto_loc_latitude'], $row['sto_loc_longitude'],
                $row['genesis_cat'], $row['res_create_date'], $row['res_create_user'], $row['res_fingerprint'], $row['res_reason_code'], $row['res_rc_desc'], $row['res_comment'],
                $row['res_asset_id'], $row['res_assetdesc1'], $row['res_assetdesc2'], $row['res_assettext'], $row['res_class'], 
                $row['res_class_ga_cat'], $row['res_loc_location'], $row['res_loc_room'], $row['res_loc_state'],  $row['res_quantity'], 
                $row['res_val_nbv'], $row['res_val_acq'], $row['res_val_orig'], $row['res_val_scrap'], $row['res_valuation_method'], 
                $row['res_ccc'], $row['res_ccc_name'], $row['res_ccc_grandparent'], $row['res_ccc_grandparent_name'], $row['res_wbs'], 
                $row['res_fund'], $row['res_responsible_ccc'], $row['res_mfr'], $row['res_inventory'], $row['res_inventno'], 
                $row['res_serialno'], $row['res_site_no'], $row['res_grpcustod'], $row['res_plateno'], $row['res_date_lastinv'], 
                $row['res_date_cap'], $row['res_loc_latitude'], $row['res_loc_longitude']

            );
            $stmt   ->execute();
        }
    }
}
*/
?>