const STATUS_ERROR='Error';
const STATUS_PROCESS='Processing';
const STATUS_COMPLETE='Completed';
const API_ENDPOINT='api.php';
pendingTasks=[];


function upload(uploadData, progressCallback, completeCallback, errorCallback) {
	if (uploadData.type == 'ga_stk') {
		uploadGaStocktake(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'raw remainder v2'){
		uploadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'ga_rr') {
		uploadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'is_audit') {
		uploadIsAudit(uploadData, progressCallback, completeCallback, errorCallback);
	}	
}


function uploadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback){
	if((!uploadData.abbrevs)||(uploadData.abbrevs.length<=0))
	{
		errorCallback([{code: 1, info: 'Raw Remainder JSON file is missing abbrev list'}]);
		return;
	}
	
	if((!uploadData.assetRows)||(uploadData.assetRows.length<=0))
	{
		errorCallback([{code: 1, info: 'Raw Remainder JSON file is missing asset list'}]);
		return;
	}	
	
	clearGaRawRemainder(
		progressCallback, 
		re =>{ 		
			createGaAbbrs(uploadData.abbrevs, 
				progressCallback, 
				result=>{
					createGaRawRemainders(uploadData.assetRows, 
						progressCallback, 
						ok=>{
							var settings={
								smartm_id: 1,
								rr_count: uploadData.assetRows.length,
								rr_extract_user: uploadData.extract_user,
								rr_extract_date: uploadData.extract_date
							}
							updateSettings(settings, progressCallback, completeCallback, errorCallback);
						}, 
						errorCallback);
				}, 
				errorCallback
			);
		},
		errorCallback
	);
}

function uploadGaStocktake(uploadData, progressCallback, completeCallback, errorCallback) {
	let assetList=uploadData.assetlist;
	delete uploadData.assetlist;


	createGaStocktake (
		uploadData,
		progressCallback,
		(stocktakeRec)=>{
			createGaAssets (
				stocktakeRec.stocktakeId, 
				assetList,
				progressCallback,
				completeCallback,
				errorCallback 
			); 

		},
		errorCallback
	);                

}

function uploadIsAudit(uploadData, progressCallback, completeCallback, errorCallback) {
	
	let impairmentList=[];
	if(!uploadData.impairments){
		if(!uploadData.results){
			errorCallback([{code: -1, info: 'Empty impairment list'}]);
			return;
		}else{
			impairmentList=uploadData.results;
			delete uploadData.results;
		}
	}else{
		impairmentList=uploadData.impairments;
		delete uploadData.impairments;
	}
	 
	
	

	createIsAudit (
		uploadData,
		progressCallback,
		(stocktakeRec)=>{
			createIsImpairments (
				stocktakeRec.stocktakeId, 
				impairmentList,
				progressCallback,
				completeCallback,
				errorCallback 
			); 
		},
		errorCallback
	);   
}

function createGaAbbrs(abbrList, progressCallback, completeCallback, errorCallback){
	apiRequestParallel('create_ga_abbrs', abbrList, {}, 10, 
					   'GA abbr records', 
					   progressCallback, completeCallback, errorCallback);
}

function createGaRawRemainders(rrList, progressCallback, completeCallback, errorCallback){
	apiRequestParallel('create_ga_raw_remainders', rrList, {}, 1000, 
					   'GA raw remainder asset records', 
					   progressCallback, completeCallback, errorCallback);
}

function clearGaRawRemainder(progressCallback, completeCallback, errorCallback){
	apiRequest('clear_ga_rr', {}, 'Clear GA raw remainder data', progressCallback, completeCallback, errorCallback);
}

function updateSettings(settingRec, progressCallback, completeCallback, errorCallback){
	apiRequest('update_settings', {settings: settingRec}, 'updated  GA raw remainder settings', progressCallback, completeCallback, errorCallback);
}

function createIsAudit (stocktake, progressCallback, completeCallback, errorCallback) {
	if(progressCallback){
		progressCallback(0,1,STATUS_PROCESS,'creates IS activity');
	}
	axios.post(API_ENDPOINT, 
		{
			action: 'create_is_audit',
			data: stocktake
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			if(progressCallback){
				progressCallback(0,1,STATUS_ERROR,'creates IS activity');
			}
			errorCallback(response.data.errors);
		}else{
			if(progressCallback){
				progressCallback(1,1,STATUS_COMPLETE,'creates IS activity');
			}
			completeCallback(response.data.result);
		}
	});
}

function createIsImpairments (stocktakeId, impairmentList, progressCallback, completeCallback, errorCallback) {
	apiRequestParallel('create_is_impairments',  
						impairmentList, {stocktakeId: stocktakeId}, 50, 
					   'IS impairment records',	progressCallback, 
					   completeCallback, errorCallback);
}

function getIsRecords(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, {
			action: 'get_is_records', 
			data : {}
	})
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}



function createGaStocktake (stocktake, progressCallback, completeCallback, errorCallback) {
	progressCallback(0, 1, STATUS_PROCESS, 'creates GA activity');
	axios.post(API_ENDPOINT, 
		{
			action: 'create_ga_stocktake',
			data: stocktake
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			progressCallback(0, 1, STATUS_ERROR, 'creates GA activity');
			errorCallback(response.data.errors);
		}else{
			progressCallback(1, 1, STATUS_COMPLETE, 'creates GA activity');
			completeCallback(response.data.result);
		}
	});
}

function createGaAssets (stocktakeId, assetList, progressCallback, completeCallback, errorCallback) {

	apiRequestParallel('create_ga_assets',  
					assetList, {stocktakeId: stocktakeId}, 50, 
					'GA asset records', progressCallback, 
					completeCallback, errorCallback);
}

function getMilisEnableFindingIDs(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'get_milis_finding_ids',
			data: {}
		}
	)
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}

function getUserProfiles(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'get_user_profiles',
			data: {}
		}
	)
	.then(response=> {
		processResponse(response, completeCallback, errorCallback);
	});
}

function saveUserProfile(profileId, userName, userPhone, completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'save_user_profile',
			data: {
				profile_id: profileId,
				profile_name: userName,
				profile_phone_number: userPhone
			}
		}
	)
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}
function updateSoftware(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'update_software',
			data: {
			}
		}
	)
	.then(
		response=> {
			processResponse(response,completeCallback, errorCallback);
		}
	);
}

function deleteUserProfile(profileId, completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'delete_user_profile',
			data: {
				profile_id: profileId
			}
		}
	)
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}

function getSm19Cats(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'get_sm19_cat',
			data: {}
		}
	)
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}

function getIsImpairments(completeCallback, errorCallback){
	axios.post(API_ENDPOINT, 
		{
			action: 'get_is_impairments',
			data: {}
		}
	)
	.then(response=> {
		processResponse(response,completeCallback, errorCallback);
	});
}

function processResponse(httpResponse, completeCallback, errorCallback){
	if(httpResponse.data && httpResponse.data.status) {
		if((httpResponse.data.status=='ERROR')&&(errorCallback)) {
			errorCallback(httpResponse.data.errors ? httpResponse.data.errors : [{code: -1, info: 'A system error occured on server'}]);
		}else if((httpResponse.data.status=='OK')&&(completeCallback)){
			completeCallback(httpResponse.data.result ? httpResponse.data.result : {});
		}
	}else{
		if(completeCallback) {
			completeCallback(httpResponse.data ? httpResponse.data : {});
		}
	}
}

function apiRequest(apiName, requestData, progressMessage, progressCallback, completeCallback, errorCallback){
	if(progressCallback){
		progressCallback(0,1,STATUS_PROCESS, progressMessage);
	}
	
	axios.post(API_ENDPOINT, 
		{
			action: apiName,
			data: requestData			
		}
	).then(httpResponse=> {
		if(httpResponse.data && httpResponse.data.status) {
			if(httpResponse.data.status=='ERROR') {
				if(progressCallback){
					progressCallback(0,1,STATUS_ERROR, progressMessage);
				}
				errorCallback(httpResponse.data.errors);
			}else if(httpResponse.data.status=='OK'){
				if(progressCallback){
					progressCallback(1,1,STATUS_COMPLETE, progressMessage);
				}
				
				if(completeCallback) {
					completeCallback(httpResponse.data.result);
				}
			}
		}else{
			if(completeCallback) {
				completeCallback(httpResponse.data ? httpResponse.data : {});
			}
		}
	});
}


function apiRequestParallel(apiName, records, params, chunkSize, progressMessage, 
							progressCallback, completeCallback, errorCallback) 
{

	var total=records.length;
	var chunkBuff=[];
	var chunkCounter=0;
	var n=0;
	var completed = 0;
	
	if(progressCallback){
		progressCallback(completed, total, STATUS_PROCESS, progressMessage);	
	}
	
	for(var i in records) {
		
		chunkBuff[n++]=records[i]; 
		
		if(n>=chunkSize) {
		
			var taskNum='SID:'+(++chunkCounter);
			
			pendingTasks[taskNum]=n;
			
			var requestData={
				action: apiName, 
				data : {					 
					taskId: taskNum,
					records : chunkBuff
				}
			}
			if((params)&&(Object.keys(params).length>0)){
				for(key in params){
					requestData.data[key]=params[key];
				}
			}
			axios.post(API_ENDPOINT, requestData)
			.then(
				httpResponse => {

					if (httpResponse.data.status=='ERROR') {
	
						if( progressCallback){
							progressCallback(completed, total, STATUS_ERROR, progressMessage);
						}
						
						errorCallback(httpResponse.data.errors);
					
					} else if(httpResponse.data.status=='OK') {
					
						completed += httpResponse.data.result.processed;
						delete pendingTasks[httpResponse.data.result.taskId];
						var remain=Object.keys(pendingTasks).length;
						
						if(remain==0) {		
							if(progressCallback){
								progressCallback(completed, total, STATUS_COMPLETE, progressMessage);		
							}						
							completeCallback({processed: completed});
						}else{
							if(progressCallback){
								progressCallback(completed, total, STATUS_PROCESS, progressMessage);
							}
						}
					}
				}
			);
			n=0;
			chunkBuff=[];
		}   		
	}
	
	if(chunkBuff.length > 0){
		var taskNum='SID:'+(++chunkCounter);
		
		pendingTasks[taskNum]=n;
		var requestData={
			action: apiName, 
			data : {					 
				taskId: taskNum,
				records : chunkBuff
			}
		}
		if((params)&&(Object.keys(params).length>0)){
			for(key in params){
				requestData.data[key]=params[key];
			}
		}		
		axios.post(API_ENDPOINT, requestData) 
		.then(
			httpResponse => {

				if(httpResponse.data.status=='ERROR') {
				
					if( progressCallback){
						progressCallback(completed, total, STATUS_ERROR, progressMessage);

					}
					
					errorCallback(httpResponse.data.errors);
				
				}else if(httpResponse.data.status == 'OK'){
				
					completed += httpResponse.data.result.processed;
					delete pendingTasks[httpResponse.data.result.taskId];
					var remain=Object.keys(pendingTasks).length;
					
					if(remain==0) {		
						if(progressCallback){
							progressCallback(completed, total, STATUS_COMPLETE, progressMessage);		
						}						
						completeCallback({processed: completed});
					}else{
						if(progressCallback){
							progressCallback(completed, total, STATUS_PROCESS, progressMessage);
						}
					}
				}
			}
		);
		n=0;
		chunkBuff=[];

	}
}

const XSLX_ACTIVITY_COL_HEADER = [
	{
        value: 'type',
        type: 'string'
    },{
        value: 'file_version',
        type: 'string'
    },{
    	value: 'stk_name',
    	type: 'string'
    },{
    	value: 'dpn_extract_date',
    	type: 'string'
    },{
    	value: 'dpn_extract_user',
    	type: 'string'
    },{
    	value: 'smm_extract_date',
    	type: 'string'
    },{
    	value: 'smm_extract_user',
    	type: 'string'
    },{
    	value: 'unique_file_id',
    	type: 'string'
    }
];

const XSLX_GA_ASSET_COL_HEADER = [ 
	{
		value: 'ass_id',
		type: 'string'
	},
	{
		value: 'stkm_id',
		type: 'string'
	},
	{
		value: 'stk_include',
		type: 'string'
	},
	{
		value: 'rr_id',
		type: 'string'
	},
	{
		value: 'ledger_id',
		type: 'string'
	},
	{
		value: 'create_date',
		type: 'string'
	},
	{
		value: 'create_user',
		type: 'string'
	},
	{
		value: 'delete_date',
		type: 'string'
	},
	{
		value: 'sto_asset_id',
		type: 'string'
	},
	{
		value: 'sto_assetdesc1',
		type: 'string'
	},
	{
		value: 'sto_assetdesc2',
		type: 'string'
	},
	{
		value: 'sto_assettext',
		type: 'string'
	},
	{
		value: 'sto_class',
		type: 'string'
	},
	{
		value: 'sto_class_ga_cat',
		type: 'string'
	},
	{
		value: 'sto_loc_location',
		type: 'string'
	},
	{
		value: 'sto_loc_room',
		type: 'string'
	},
	{
		value: 'sto_loc_state',
		type: 'string'
	},
	{
		value: 'sto_quantity',
		type: 'string'
	},
	{
		value: 'sto_val_nbv',
		type: 'string'
	},
	{
		value: 'sto_val_acq',
		type: 'string'
	},
	{
		value: 'sto_val_orig',
		type: 'string'
	},
	{
		value: 'sto_val_scrap',
		type: 'string'
	},
	{
		value: 'sto_valuation_method',
		type: 'string'
	},
	{
		value: 'sto_ccc',
		type: 'string'
	},
	{
		value: 'sto_ccc_name',
		type: 'string'
	},
	{
		value: 'sto_ccc_grandparent',
		type: 'string'
	},
	{
		value: 'sto_ccc_grandparentname',
		type: 'string'
	},
	{
		value: 'sto_wbs',
		type: 'string'
	},
	{
		value: 'sto_fund',
		type: 'string'
	},
	{
		value: 'sto_responsible_ccc',
		type: 'string'
	},
	{
		value: 'sto_mfr',
		type: 'string'
	},
	{
		value: 'sto_inventory',
		type: 'string'
	},
	{
		value: 'sto_inventno',
		type: 'string'
	},
	{
		value: 'sto_serialno',
		type: 'string'
	},
	{
		value: 'sto_site_no',
		type: 'string'
	},
	{
		value: 'sto_grpcustod',
		type: 'string'
	},
	{
		value: 'sto_plateno',
		type: 'string'
	},
	{
		value: 'sto_revodep',
		type: 'string'
	},
	{
		value: 'sto_date_lastinv',
		type: 'string'
	},
	{
		value: 'sto_date_cap',
		type: 'string'
	},
	{
		value: 'sto_date_pl_ret',
		type: 'string'
	},
	{
		value: 'sto_date_deact',
		type: 'string'
	},
	{
		value: 'sto_loc_latitude',
		type: 'string'
	},
	{
		value: 'sto_loc_longitude',
		type: 'string'
	},
	{
		value: 'genesis_cat',
		type: 'string'
	},
	{
		value: 'res_create_date',
		type: 'string'
	},
	{
		value: 'res_create_user',
		type: 'string'
	},
	{
		value: 'res_fingerprint',
		type: 'string'
	},
	{
		value: 'res_reason_code',
		type: 'string'
	},
	{
		value: 'res_rc_desc',
		type: 'string'
	},
	{
		value: 'res_comment',
		type: 'string'
	},
	{
		value: 'res_asset_id',
		type: 'string'
	},
	{
		value: 'res_assetdesc1',
		type: 'string'
	},
	{
		value: 'res_assetdesc2',
		type: 'string'
	},
	{
		value: 'res_assettext',
		type: 'string'
	},
	{
		value: 'res_class',
		type: 'string'
	},
	{
		value: 'res_class_ga_cat',
		type: 'string'
	},
	{
		value: 'res_loc_location',
		type: 'string'
	},
	{
		value: 'res_loc_room',
		type: 'string'
	},
	{
		value: 'res_loc_state',
		type: 'string'
	},
	{
		value: 'res_quantity',
		type: 'string'
	},
	{
		value: 'res_val_nbv',
		type: 'string'
	},
	{
		value: 'res_val_acq',
		type: 'string'
	},
	{
		value: 'res_val_orig',
		type: 'string'
	},
	{
		value: 'res_val_scrap',
		type: 'string'
	},
	{
		value: 'res_valuation_method',
		type: 'string'
	},
	{
		value: 'res_ccc',
		type: 'string'
	},
	{
		value: 'res_ccc_name',
		type: 'string'
	},
	{
		value: 'res_ccc_grandparent',
		type: 'string'
	},
	{
		value: 'res_ccc_grandparent_name',
		type: 'string'
	},
	{
		value: 'res_wbs',
		type: 'string'
	},
	{
		value: 'res_fund',
		type: 'string'
	},
	{
		value: 'res_responsible_ccc',
		type: 'string'
	},
	{
		value: 'res_mfr',
		type: 'string'
	},
	{
		value: 'res_inventory',
		type: 'string'
	},
	{
		value: 'res_inventno',
		type: 'string'
	},
	{
		value: 'res_serialno',
		type: 'string'
	},
	{
		value: 'res_site_no',
		type: 'string'
	},
	{
		value: 'res_grpcustod',
		type: 'string'
	},
	{
		value: 'res_plateno',
		type: 'string'
	},
	{
		value: 'res_revodep',
		type: 'string'
	},
	{
		value: 'res_date_lastinv',
		type: 'string'
	},
	{
		value: 'res_date_cap',
		type: 'string'
	},
	{
		value: 'res_date_pl_ret',
		type: 'string'
	},
	{
		value: 'res_date_deact',
		type: 'string'
	},
	{
		value: 'res_loc_latitude',
		type: 'string'
	},
	{
		value: 'res_loc_longitude',
		type: 'string'
	}
];

const XSLX_IS_IMPAIRMENT_COL_HEADER = [
	{
		value: 'storage_id',
		type: 'string'
	},
	{
		value: 'res_parent_storage_id',
		type: 'string'
	},	
	{
		value: 'actType',
		type: 'string'
	},		
	{
		value: 'targetID',
		type: 'string'
	},
	{
		value: 'DSTRCT_CODE',
		type: 'string'
	},
	{
		value: 'WHOUSE_ID',
		type: 'string'
	},	
	{
		value: 'BIN_CODE',
		type: 'string'
	},
	{
		value: 'STOCK_CODE',
		type: 'string'
	},
	{
		value: 'TRACKING_REFERENCE',
		type: 'string'
	},	
	{
		value: 'ITEM_NAME',
		type: 'string'
	},	
	{
		value: 'data_source',
		type: 'string'
	},
	{
		value: 'SOH',
		type: 'string'
	},	
	{
		value: 'findingID',
		type: 'string'
	},
	{
		value: 'finalResult',
		type: 'string'
	},
	{
		value: 'finalResultPath',
		type: 'string'
	},	
	{
		value: 'res_comment',
		type: 'string'
	},
	{
		value: 'res_evidence_desc',
		type: 'string'
	},
	{
		value: 'res_unserv_date',
		type: 'string'
	},
	{
		value: 'checked_to_milis',
		type: 'string'
	}, 		
	{
		value: 'isID',
		type: 'string'
	},
	{
		value: 'targetItemID',
		type: 'string'
	},
	{
		value: 'SUPPLY_CUST_ID',
		type: 'string'
	},

	{
		value: 'SC_ACCOUNT_TYPE',
		type: 'string'
	},
	{
		value: 'TRACKING_IND',
		type: 'string'
	},
	{
		value: 'STK_DESC',
		type: 'string'
	},
	{
		value: 'INVENT_CAT',
		type: 'string'
	},
	{
		value: 'INVENT_CAT_DESC',
		type: 'string'
	},

	{
		value: 'SUPPLY_ACCT_METH',
		type: 'string'
	},

	{
		value: 'isBackup',
		type: 'string'
	},
	{
		value: 'serviceableFlag',
		type: 'string'
	},	
	{
		value: 'sample_flag',
		type: 'string'
	},
	{
		value: 'ExtractDate',
		type: 'string'
	},	
	{
		value: 'last_mod_date',
		type: 'string'
	},	
	{
		value: 'res_update_user',
		type: 'string'
	},		    
	{
		value: 'res_create_date',
		type: 'string'
	},
	{
		value: 'res_create_user',
		type: 'string'
	},	     
	{
		value: 'create_date',
		type: 'string'
	},
	{
		value: 'create_user',
		type: 'string'
	},
	{
		value: 'delete_date',
		type: 'string'
	},
	{
		value: 'delete_user',
		type: 'string'
	},
	{
		value: 'fingerprint',
		type: 'string'
	}		
];

function createExcelRow(headers, rec){
	var row=[];
	
	var i=0;
	headers.forEach(
		(col, index)=>{
			row[i++]={value: rec[col.value], type: col.type};
		}
	);
	return row;
}