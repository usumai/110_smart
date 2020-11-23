let STATUS_ERROR='Error';
let STATUS_PROCESS='Processing';
let STATUS_COMPLETE='Completed';

function getMilisEnableFindingIDs(completeCallback, errorCallback){
	axios.post('api.php', 
		{
			action: 'get_milis_finding_ids',
			data: {}
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});
}
function getUserProfiles(completeCallback, errorCallback){
	axios.post('api.php', 
		{
			action: 'get_user_profiles',
			data: {}
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});
}
function saveUserProfile(profileId, userName, userPhone, completeCallback, errorCallback){
	axios.post('api.php', 
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
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});	
}
function deleteUserProfile(profileId, completeCallback, errorCallback){
	axios.post('api.php', 
		{
			action: 'delete_user_profile',
			data: {
				profile_id: profileId
			}
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});	
}
function getSm19Cats(completeCallback, errorCallback){
	axios.post('api.php', 
		{
			action: 'get_sm19_cat',
			data: {}
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});	
}
function getIsImpairments(completeCallback, errorCallback){
	axios.post('api.php', 
		{
			action: 'get_is_impairments',
			data: {}
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			errorCallback(response.data.errors);
		}else{
			completeCallback(response.data.result);
		}
	});	
}

function upload(uploadData, progressCallback, completeCallback, errorCallback) {
	if (uploadData.type == 'ga_stk') {
		loadGaStocktake(uploadData, progressCallback, completeCallback, errorCallback);
	}else if (uploadData.type == 'raw remainder v2'){
		//fnUpload_rawremainder($arr, 0);
	}else if (uploadData.type == 'ga_rr') {

	}else if (uploadData.type == 'is_audit') {
		loadIsAudit(uploadData, progressCallback, completeCallback, errorCallback);
	}	
}

function loadRawRemainder(uploadData, progressCallback, completeCallback, errorCallback){

}

function loadIsAudit(uploadData, progressCallback, completeCallback, errorCallback){
	let impairmentList=uploadData.results;
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
function createIsAudit (stocktake, progressCallback, completeCallback, errorCallback) {
	progressCallback(0,1,STATUS_PROCESS,'creates IS activity');
	axios.post('api.php', 
		{
			action: 'create_is_audit',
			data: stocktake
		}
	)
	.then(response=> {
		if(response.data.status=='ERROR') {
			progressCallback(0,1,STATUS_ERROR,'creates IS activity');
			errorCallback(response.data.errors);
		}else{
			progressCallback(1,1,STATUS_COMPLETE,'creates IS activity');
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
	progressCallback(current,total,STATUS_PROCESS,'creates IS impairment');
	for(var rec in impairmentList) {
		impairmentList[rec].STK_DESC = impairmentList[rec].STK_DESC ? impairmentList[rec].STK_DESC.replace("\n","") : impairmentList[rec].STK_DESC;
		impairmentList[rec].ITEM_NAME = impairmentList[rec].ITEM_NAME ? impairmentList[rec].ITEM_NAME.replace("\n","") : impairmentList[rec].ITEM_NAME;

		
		batchBuff[n++]=impairmentList[rec]; 
		current++;

		if(n>=batchSize) {
			axios.post('api.php', {
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
					if(current>=total) {	
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
		axios.post('api.php', {
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

function createGaStocktake (stocktake, progressCallback, completeCallback, errorCallback) {
	progressCallback(0, 1, STATUS_PROCESS, 'creates GA activity');
	axios.post('api.php', 
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
			axios.post('api.php', {
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
		axios.post('api.php', {
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

