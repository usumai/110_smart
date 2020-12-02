let STATUS_ERROR='Error';
let STATUS_PROCESS='Processing';
let STATUS_COMPLETE='Completed';
let API_ENDPOINT='api.php';
let pendingTasks=[];


function upload(uploadData, progressCallback, completeCallback, errorCallback) {
	if (uploadData.type == 'ga_stk') {
		loadGaStocktake(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'raw remainder v2'){
		loadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'ga_rr') {
		loadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'is_audit') {
		loadIsAudit(uploadData, progressCallback, completeCallback, errorCallback);
	}	
}


function loadGaRawRemainder(uploadData, progressCallback, completeCallback, errorCallback){
	if((!uploadData.abbrevs)||(uploadData.abbrevs.length<=0))
	{
		errorCallback({code: 1, info: 'Raw Remainder JSON file is missing abbrev list'});
		return;
	}
	
	if((!uploadData.assetRows)||(uploadData.assetRows.length<=0))
	{
		errorCallback({code: 1, info: 'Raw Remainder JSON file is missing asset list'});
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

function loadGaStocktake(uploadData, progressCallback, completeCallback, errorCallback) {
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

function loadIsAudit(uploadData, progressCallback, completeCallback, errorCallback){
	let impairmentList=[];
	if(!uploadData.impairments){
		if(!uploadData.results){
			return;
		}else{
			impairmentList=uploadData.results;
		}
	}else{
		impairmentList=uploadData.impairments;
	}
	 
	
	delete uploadData.results;

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
	var current=0;
	var total=abbrList.length;
	var batchSize=100;
	var batchBuff=[];
	var batchCounter=0;
	var n=0;
	var completed = 0;
	progressCallback(current, total, STATUS_PROCESS, 'creates GA abbr record');	
	for(var rec in abbrList) {
		batchBuff[n++]=abbrList[rec]; 
		current++;
		if(n>=batchSize) {
			var xaId='xa_'+(++batchCounter);
			pendingTasks[xaId]=n;
			
			axios.post(API_ENDPOINT, {
				action: 'create_ga_abbrs', 
				data : { 
					correlationId: xaId,
					abbrevs : batchBuff
				}
			})
			.then(response => {
				completed += batchSize;
				if(response.data.status=='ERROR') {
					progressCallback(current, total, STATUS_ERROR, 'creates GA abbr record');
					errorCallback(response.data.errors);
				}else if(response.data.status=='OK'){
					delete pendingTasks[response.data.result.correlationId];
					var remain=Object.keys(pendingTasks).length;
					
					if(remain==0) {		
						progressCallback(completed, total, STATUS_COMPLETE, 'creates GA abbr record');		
						completeCallback(response.data.result);
					}else if(response.data.status=='OK'){
						progressCallback(completed, total, STATUS_PROCESS, 'creates GA abbr record');
					}
				}
			});
			n=0;
			batchBuff=[];
		}   		
	}
	
	if(batchBuff.length > 0){
		var xaId='xa_'+(++batchCounter);
		pendingTasks[xaId]=n;
		
		axios.post(API_ENDPOINT, {
			action: 'create_ga_abbrs', 
			data : { 
				correlationId: xaId,
				abbrevs : batchBuff
			}
		})
		.then(response => {
			completed += batchBuff.length;
			if(response.data.status=='ERROR') {
				progressCallback(completed, total, STATUS_ERROR, 'creates GA abbr record');
				errorCallback(response.data.errors);
			}else if(response.data.status=='OK'){
				delete pendingTasks[response.data.result.correlationId];
				var remain=Object.keys(pendingTasks).length;
			
				if(remain==0) {		
					progressCallback(completed, total, STATUS_COMPLETE, 'creates GA abbr record');		
					completeCallback(response.data.result);
				}else{
					progressCallback(completed, total, STATUS_PROCESS, 'creates GA abbr record');
				}
			}
		});
	}
}

function createGaRawRemainders(rrList, progressCallback, completeCallback, errorCallback){
	var current=0;
	var total=rrList.length;
	var batchSize=1000;
	var batchBuff=[];
	var batchCounter=0;
	var n=0;
	var completed = 0;
	progressCallback(current, total, STATUS_PROCESS, 'creates GA raw remainder asset');	
	
	for(var rec in rrList) {
		batchBuff[n++]=rrList[rec]; 
		current++;
		if(n>=batchSize) {
			var xaId='xa_'+(++batchCounter);
			pendingTasks[xaId]=n;
			
			axios.post(API_ENDPOINT, {
				action: 'create_ga_raw_remainders', 
				data : { 
					correlationId: xaId,
					assetRows : batchBuff
				}
			})
			.then(response => {
				completed += batchSize;
				if(response.data.status=='ERROR') {
					progressCallback(current, total, STATUS_ERROR, 'creates GA raw remainder asset');
					errorCallback(response.data.errors);
				}else if(response.data.status=='OK'){
					delete pendingTasks[response.data.result.correlationId];
					var remain=Object.keys(pendingTasks).length;
					console.log('ACK: '+response.data.result.correlationId+', Remain: '+remain+', Completed: '+completed);
					console.log(Object.keys(pendingTasks));
					
					if(remain==0) {		
						progressCallback(completed, total, STATUS_COMPLETE, 'creates GA raw remainder asset');		
						completeCallback(response.data.result);
					}else{
						progressCallback(completed, total, STATUS_PROCESS, 'creates GA raw remainder asset');
					}
				}
			});
			n=0;
			batchBuff=[];
		}   		
	}
	
	if(batchBuff.length > 0){
		var xaId='xa_'+(++batchCounter);
		pendingTasks[xaId]=n;
		
		axios.post(API_ENDPOINT, {
			action: 'create_ga_raw_remainders', 
			data : { 
				correlationId: xaId,
				assetRows : batchBuff
			}
		})
		.then(response => {
			completed += batchBuff.length;
			if(response.data.status=='ERROR') {
				progressCallback(current, total, STATUS_ERROR, 'creates GA raw remainder asset');
				errorCallback(response.data.errors);
			}else if(response.data.status=='OK'){
				delete pendingTasks[response.data.result.correlationId];
				var remain=Object.keys(pendingTasks).length;
			
				if(remain==0) {		
					progressCallback(completed, total, STATUS_COMPLETE, 'creates GA raw remainder asset');		
					completeCallback(response.data.result);
				}else{
					progressCallback(completed, total, STATUS_PROCESS, 'creates GA raw remainder asset');
				}
			}
		});
	}
		
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
	var current=0;
	var total= impairmentList.length;
	var batchSize=10;
	var batchBuff=[];
	var n=0;
	var completed=0;
	progressCallback(current,total,STATUS_PROCESS,'creates IS impairment');
	for(var rec in impairmentList) {
		impairmentList[rec].STK_DESC = impairmentList[rec].STK_DESC ? impairmentList[rec].STK_DESC.replace("\n","") : impairmentList[rec].STK_DESC;
		impairmentList[rec].ITEM_NAME = impairmentList[rec].ITEM_NAME ? impairmentList[rec].ITEM_NAME.replace("\n","") : impairmentList[rec].ITEM_NAME;

		
		batchBuff[n++]=impairmentList[rec]; 
		current++;

		if(n>=batchSize) {

			axios.post(API_ENDPOINT, {
				action: 'create_is_impairments', 
				data : { 
					stocktakeId: stocktakeId, 
					impairments : batchBuff
				}
			})
			.then(response => {
				completed += batchSize;
				if(response.data.status=='ERROR') {
					progressCallback(current, total, STATUS_ERROR, 'creates IS impairment');
					errorCallback(response.data.errors);
				}else{
					if(completed >= total) {	
						progressCallback(current, total, STATUS_COMPLETE, 'creates IS impairment');
						completeCallback(response.data.result);
					}else{
						progressCallback(current, total, STATUS_PROCESS, 'creates IS impairment');
					}
				}
			});
			n=0;
			batchBuff=[];
		}                    
		
	}
	if(batchBuff.length > 0){
		axios.post(API_ENDPOINT, {
			action: 'create_is_impairments', 
			data : { 
				stocktakeId: stocktakeId, 
				impairments : batchBuff
			}
		})
		.then(response => {
			
			if(response.data.status=='ERROR') {
				progressCallback(current, total, STATUS_ERROR, 'creates IS impairment');
				errorCallback(response.data.errors);
			}else{
				progressCallback(current, total, STATUS_COMPLETE, 'creates IS impairment');
				completeCallback(response.data.result);
			}
		});
	}
	
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
	var current=0;
	var total=assetList.length;
	var batchSize=10;
	var batchBuff=[];
	var n=0;
	progressCallback(current, total, STATUS_PROCESS, 'creates GA asset');	
	for(var rec in assetList) {
		batchBuff[n++]=assetList[rec]; 
		current++;
		if(n>=batchSize) {
			axios.post(API_ENDPOINT, {
				action: 'create_ga_assets', 
				data : { 
					stocktakeId: stocktakeId, 
					assets : batchBuff
				}
			})
			.then(response => {
				if(response.data.status=='ERROR') {
					progressCallback(current, total, STATUS_ERROR, 'creates GA asset');
					errorCallback(response.data.errors);
				}else{
					
					if(current>=total) {		
						progressCallback(current, total, STATUS_COMPLETE, 'creates GA asset');		
						completeCallback(response.data.result);
					}else{
						progressCallback(current, total, STATUS_PROCESS, 'creates GA asset');
					}
				}
			});
			n=0;
			batchBuff=[];
		}                    
		
	}
	if(batchBuff.length > 0){
		axios.post(API_ENDPOINT, {
			action: 'create_assets', 
			data : { 
				stocktakeId: stocktakeId, 
				assets : batchBuff
			}
		})
		.then(response => {
			if(response.data.status=='ERROR') {
				progressCallback(current, total, STATUS_ERROR, 'creates GA asset');
				errorCallback(response.data.errors);
			}else{
				progressCallback(current, total, STATUS_COMPLETE, 'creates GA asset');
				completeCallback(response.data.result);
			}
		});
	}
	
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