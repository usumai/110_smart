<?php
$current_row=0;
if(array_key_exists("current_row",$_POST)){
	$current_row=$_POST["current_row"];
}elseif(array_key_exists("current_row",$_GET)){
	$current_row=$_GET["current_row"];
}

?>
<script src="includes/axios/axios.min.js" ></script>     
<script>
  
	$(function(){
	    $(document).on('click', '.btnHelp', function(){ 
			let helpWords = $(this).val();
			$('#areaHelpModal').html(helpWords)
		})
	});


  	$(document).on('click', '.btnInitTemplate', function(){ 
		let ass_id = $(this).val();
		$("#ass_id").val(ass_id);
	})



</script>


<div id="appmenu">
	<body class="d-flex flex-column h-100">
	<header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark lead">
			<a class="navbar-brand" href="index.php">smartM</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item initiateBTN"></li>
					<li class='nav-item' id='menuSearch'  v-if="sysd.act_type=='ga_stk'">
						<div class='ui-widget'>
							<input id='tags' class='form-control' autofocus v-model="menu_search" @input='get_search_results'>
						</div>
					</li>

					<li class="nav-item dropdown" v-if="sysd.act_type=='ga_stk'">
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='menuFF'>First Found</a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdown_adds'>
							<h6 class='dropdown-header'>First found</h6>
							<a class='dropdown-item' href='12_ff.php'>Add First Found</a>
							<div class='dropdown-divider'></div>
							<h6 class='dropdown-header'>Templates</h6>
							<button class='dropdown-item' v-for="(template, tidx) in templd"
								v-on:click="init_template(template)">
								<strong>{{template.res_reason_code}}</strong> {{template.res_assetdesc1}}</button>
						</div>
					</li>


					<li class="nav-item dropdown" v-if="sysd.act_type=='is_audit'">
						<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'>IS tools</a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>
							<a class='dropdown-item' href='19_toggle.php'>Toggle primary/backup</a>
						</div>
					</li>

					<li class="nav-item dropdown" v-if="sysd.act_type=='ga_stk'">
						<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#'data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'>GA Tools</a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>
							<h6 class='dropdown-header'>Raw Remainder</h6>
							<span class='dropdown-item'>Assets loaded: {{ rwrd.rr_rowcount }}</span>
						</div>
					</li>


<!-- System Dropdown Menu -->
					<li class="nav-item dropdown">
						<a class='nav-link dropdown-toggle' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'
						:class="{'text-danger':sysd.versionLocal<sysd.versionRemote}">System</a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>

							<h6 class='dropdown-header'>System settings</h6>
							<button type='button' class='dropdown-item btn btn-danger' @click="initResetData()" data-toggle='modal' data-target='#modal_confirm_reset'>Reset all data</button>
							<a class='dropdown-item' href='05_action.php?act=save_invertcolors'>Invert Colour Scheme</a>
							<!-- <a class='dropdown-item' href='05_action.php?act=sys_open_image_folder'>Image folder</a> -->

							<div class='dropdown-divider'></div>
							<h6 class='dropdown-header'>Installed Version<span class='float-right'>v{{ sysd.versionLocal }}{{sysd.versionLocalRevision? ('.'+sysd.versionLocalRevision) : ''}}</span></h6>
							<h6 class='dropdown-header'>Available Version<span class='float-right'>v{{ sysd.versionRemote }}{{sysd.versionRemoteRevision? ('.'+sysd.versionRemoteRevision.substring(0,7)) : ''}}</span></h6>
							<h6 class='dropdown-header'>Last checked<span class='float-right'>{{ sysd.date_last_update_check }}</span></h6>
							<button type='button' v-if="sysd.versionLocal==sysd.versionRemote" class='dropdown-item btn' @click='checkAvailableSoftwareVersion()'>Check for new version</button>
							<button type="button" class="dropdown-item btn" data-toggle="modal" @click="initSoftwareUpdate()" data-target="#update_confirm_dlg"><i class="fas fa-cloud-download-alt ml-2"></i> Force Software Update</button>

							<button type='button' v-if="sysd.versionLocal<sysd.versionRemote" class='dropdown-item btn text-danger' data-toggle='modal' data-target='#modal_confirm_update'>Update available v{{ sysd.versionRemote }}</button>
							
							<span v-if='true'>
								<div class='dropdown-divider'></div>
								<h6 class='dropdown-header'>User management</span></h6>
								<tr v-for='(actv, actvidx) in actvd'  v-if='!actv.smm_delete_date||actv.smm_delete_date&&show_deleted'>
								<div v-if="userProfiles.length==0">No users exist</div>
								<button 
									v-if="userProfiles.length>0"
									v-for="(profile, index) in userProfiles" 
									@click="setCurrentUserProfile(profile, index)"
									type='button' 
									class='dropdown-item btn text-success btnUser'
									data-toggle='modal' 
									data-target='#modal_add_user'>{{ profile.current ? '*'+profile.profile_name : profile.profile_name}}</button>

								<button 
									@click="setCurrentUserProfile({profile_id:0,profile_name:'',profile_phone_number:''}, -1)"
									type='button' 
									class='btn btnUser' 
									data-toggle='modal' 
									data-target='#modal_add_user'>+ Add new user</button>
							</span>


						</div>

					</li>


				</ul>
 				<div v-if="errorInfo !== ''" class="container">
            		<div class="alert alert-danger"><strong>Error: </strong>{{errorInfo}}</div>     
        		</div>
        		
				<a href="10_stk.php?current_row=<?=$current_row ?>" class='nav-link text-success' v-if="sysd.act_type=='ga_stk'">Summary</a>
				<a href="15_impairment.php?current_row=<?=$current_row ?>" class='nav-link text-success' v-if="sysd.act_type=='is_audit'" >Summary</a>
				<div style="font-style: italic; color: #007bff; border: yellow solid 1px; padding: 0px 7px; border-radius: 10px; "><i class="fa fa-user"></i> {{user.name}}</div>
			</div>
		</nav>
		<hr>
		<ul v-if="menu_search">
			<a 	:href="'11_ass.php?ass_id='+result.ass_id" 
				class="list-group-item list-group-item-action" 
				v-if='!show_search_prompt'
				v-for="(result, ridx) in search_resd" >
					[
						<span v-if="result.res_reason_code">{{result.res_reason_code}}</span>
						<span v-if="!result.res_reason_code" class="text-danger">NYC</span>
					] 
					{{result.res_asset_id}}: {{result.res_assetdesc1}} - {{result.sto_assetdesc2}} </a>
			<a 	:href="'14_rr.php?search_term='+menu_search" 
				v-if='!show_search_prompt'
				class="list-group-item list-group-item-action"  >Raw remainder search result: {{rr_search_res}}</a>
			<li v-if='show_search_prompt'
				class="list-group-item list-group-item-action">Please type more than three characters to conduct search</li>
		</ul>
		
	</header>


<!-- Initiate Asset Template Dialog -->
<div class="modal fade" id="modal_initiate_template" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #5a95ca;">
				<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Initiate asset template</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		
			<form method='post' action='05_action.php'>
				<div class="modal-body">  
					<p class="lead">Please select a stocktake to initiate this template into</p>
					<select name='stkm_id' class='form-control' v-if='actvd'>
						<option v-for='(activity, index) in actvd' :value="activity.stkm_id">{{activity.stk_id }}. {{activity.stk_name}}</option>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-outline-danger">Initiate</button>
					<input type='hidden' name='act' value='save_initiate_template'>
					<input type='hidden' name='ass_id' v-model="template_ass_id">
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Force Update Modal dialog -->
<div class="modal fade" id="update_confirm_dlg" data-backdrop="static" width='500px' height='300px'   tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #5a95ca;">
        <h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Software Update</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<table width='100%' height='100%'>
			<tr>
			  <td align='center'>
			      <p style="text-align: left;"><strong>Warning:</strong></p>
			      <p style="text-align: justify;"><i style="color: red">SmartM Software update and data reset. This will delete all application data stored on this device, so make sure you backup everything before attempting this.</i></p>
			      <p style="text-align: left;">Please make sure you are connected to the internet.</p>
			  </td>
			</tr>
			<tr>
				<td ref="update_spinner" style="text-align: center">
					<div class="spinner-border text-info" role="status">
					  <span class="sr-only">Updating...</span>
					</div>				
				</td>
				<td v-if="(updateResponse) && (updateResponse.length>0) && (!updateError)">
					<div  class="alert alert-info"><strong>Update Completed!</strong>
						<div v-if="updateRevision != ''"><strong>revision: </strong>{{updateRevision}}</div>
						<div v-for='info in updateResponse'><i>{{info}}</i></div>
					</div>   
				</td>
				<td v-if="(updateResponse) && (updateResponse.length>0) && (updateError)">
					<div class="alert alert-danger">
						<strong>Update Failed!</strong>
						<div v-for='error in updateResponse'><i>{{error.code}}-{{error.info}}</i></div>
					</div>    
				</td>				
			</tr>
		</table>
      </div>
      <div class="modal-footer">
      	<button type="button" ref="update_ok" class="btn btn-outline-dark" @click="forceUpdateToLatest()">Update</button>
        <button type="button" ref="update_close" class="btn btn-outline-dark" data-dismiss="modal" @click="refreshPage()">Close</button>       
      </div>
    </div>
  </div>
</div>


<!-- Update App Version Dialog -->
<div class="modal fade" id="modal_confirm_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header" style="background-color: #5a95ca;">
			<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Update to latest version</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<p class="lead">Updating to the latest version will delete all data on this device. Are you Sure you want to proceed with the update?<br><br>Please keep device connected to the internet until the update is finished.</p>     
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<a type="button" class="btn btn-danger" href='05_action.php?act=sys_pull_master'>Update</a>
		</div>
		</div>
	</div>
</div>

	<!-- Push App Version Dialog -->
	<div class="modal fade" id="modal_confirm_push" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header" style="background-color: #5a95ca;">
			<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Push this version to the master</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">  
			<p class="lead">This will overwrite the existing master file. Only do this if you are a guru developer.<br><br>Please keep device connected to the internet until the update is finished.</p>  
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<a type="button" class="btn btn-danger" href='05_action.php?act=sys_push_master'>Update</a>
		</div>
		</div>
	</div>
	</div>

<!-- Reset Data Dialog -->
<div class="modal fade" id="modal_confirm_reset" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #5a95ca;">
				<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Data Resets</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">  
				<p class="lead">
					<p style="text-align: left;"><strong>Warning: </strong></p>
					<p style="text-align: justify;"><i style="color: red">You are about to wipe all SMARTm data on this device.</i></p>
					<p style="text-align: left;">Are you sure you want to proceed?</p>
				</p>
				
				<div ref="elResetDataSpinner" hidden class="spinner-border text-info" role="status" style="margin-left: 50%; left: -2rem">
				  <span class="sr-only">SMARTM data resetting...</span>
				</div>
				<div ref="elResetDataStatus" hidden>
					<div  v-if="resetDataError==''" class="alert alert-info">
						<i>Data reset completed</strong>
					</div>  
					<div  v-if="resetDataError!=''" class="alert alert-danger">
						<strong>Error!</strong>
						<i>{{resetDataError}}</i>
					</div> 	 
				</div>	  
			</div>
			<div class="modal-footer">
				<button ref="elReset" 			type="button" class="btn btn-danger" @click="resetData(false)">Reset</a>
				<button ref="elResetExcludedRR" type="button" class="btn btn-danger" @click="resetData(true)">Reset excluding RR</a>
				<button ref="elResetClose" 		type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>			
			</div>
		</div>
	</div>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="modal_confirm_reset_minus_rr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #5a95ca;">
					<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Delete all data except for raw remainder data</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">  
					<p class="lead">Reseting SMARTm will delete all data on this device.<br><br>Are you sure you want to proceed?</p>  
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>	
				</div>
			</div>
		</div>
	</div>


	<!-- Add User Profile Dialog -->
	<div class="modal fade" id="modal_add_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #5a95ca;">
					<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Create a new user</h5>
					<button ref="add_user_dlg_btn_close" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body"> 
					<div v-if="user.error != ''" class="alert alert-danger"><strong>Error!</strong> {{user.error}}</div>
					<p class="lead">
					User name:
					<input type='text' class='form-control' v-model='user.name' name='profile_name'>
					User contact number:
					<input type='text' class='form-control' v-model='user.phone' ame='profile_phone_number'>
					<br><button 
							v-if="user.name != ''" 
							@click="deleteUser()"
							type="button" class="btn btn-danger" id='btnDeleteUser'>Delete</button>
					</p>  
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id='btnSaveUser' @click="saveUser()">Save</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>					
				</div>
			</div>
		</div>
	</div>


	<!-- Help Dialog -->
	<div class="modal fade" id="modal_help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
		<div class="modal-header" style="background-color: #5a95ca;">
			<h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Help</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">  
			<p class="lead" id='areaHelpModal'></p>  
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			
		</div>
		</div>
	</div>







</div>


</div>







<style scoped>
.modal-mask {
	position: fixed;
	z-index: 9998;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, .5);
	display: table;
	transition: opacity .3s ease;
}
.modal-wrapper {
	display: table-cell;
	vertical-align: middle;
}
</style>
<script>
let vm_menu = new Vue({
    el: '#appmenu',
    data: {
        errorInfo: '<?=(array_key_exists("error_info",$_GET)?$_GET["error_info"]:"")?>',
		userProfiles:[],
		currentLogonUser: '<?= get_current_user() ?>',
        sysd:{},
		templd:{},
		actvd:{},
		rwrd:{},
		vcheck:{},
		search_resd:{},
		template_ass_id:0,
		menu_search:'',
		rr_search_res:0,
		show_search_prompt:false,
		user: {
			index:0,
			id: 0,
			name:'',
			phone:'',
			error:''		
		},
		updateResponse:[],
		updateRevision:'',
		updateError: false,
		resetDataError:''
    },
    created() {
		this.refresh_sys()
		this.get_rr_stats()
	},
	mounted(){		
		this.setUserProfiles();
	},
    methods:{
    	refreshPage(){
    		window.location.reload();
    	},
		setUserProfiles(){
			getUserProfiles(
				profiles=>{
					for(idx in profiles){
						if(profiles[idx].profile_id==this.sysd.active_profile_id){
							profiles[idx].current=true;
							this.setCurrentUserProfile(profiles[idx], idx);
						}
					}
					this.userProfiles=profiles;
				}, 
				errors=>{
					
				}
			);
		},
		setCurrentUserProfile(profile, index){
			console.log(profile);
			this.user.index=index;
			this.user.id=profile.profile_id;
			this.user.name=profile.profile_name;
			this.user.phone=profile.profile_phone_number;
		},
		saveUser(){
			this.user.error='';
			saveUserProfile(this.user.id,this.user.name,this.user.phone,
			result=>{
				this.$refs.add_user_dlg_btn_close.click();
				this.setUserProfiles();
			},
			errors=>{
				if(errors.length>0){
					this.user.error=errors[0].info;
				}
			});
		},
		deleteUser(){
			this.user.error='';
			deleteUserProfile(this.user.id,
			result=>{
				this.$refs.add_user_dlg_btn_close.click();
				this.setUserProfiles();
			},
			errors=>{
				if(errors.length>0){
					this.user.error=errors[0].info;
				}
			});
		},
        refresh_sys(){
			this.get_system();
		},
		send_sys(){
			return this.sysd
		},
        get_system(){
            payload     = {'act':'get_system'};
            json   		= fnapi(payload);
            if(json.length>0){
	            this.sysd   = json[0];
				if (this.sysd.act_type=="ga_stk"){
	        		this.get_stk_templates()
				}
            }
		}, 
        get_stk_templates(){
            payload     = {'act':'get_stk_templates'}
            this.templd	= fnapi(payload)
            console.log("templd")
            console.log(this.templd)
		}, 
		init_template(template){
            payload     = {'act':'get_activities'}
            json		= fnapi(payload)
            this.actvd	= json["result"]
			this.template_ass_id = template.ass_id
			$('#modal_initiate_template').modal('show')
		}, 
		get_rr_stats(){
            payload     = {'act':'get_rr_stats'}
			json		= fnapi(payload)
			this.rwrd 	= json[0]
			console.log(this.rwrd)
		},
		initResetData(){
			this.resetDataError='';
			this.$refs.elReset.hidden=false;
			this.$refs.elResetExcludedRR.hidden=false;
			this.$refs.elResetClose.hidden=false;	
			this.$refs.elResetDataStatus.hidden=true
			this.$refs.elResetDataSpinner.hidden=true;
		},
		resetData(excludeRawRemainder){
			this.$refs.elResetDataSpinner.hidden=false;
			this.$refs.elReset.hidden=true;
			this.$refs.elResetExcludedRR.hidden=true;
			this.$refs.elResetClose.hidden=true;
			apiRequest('reset_data', {excludedRawRemainder: excludeRawRemainder}, null, null, 
					ok=>{
						this.get_system();
						this.$refs.elResetDataSpinner.hidden=true;
						this.$refs.elResetDataStatus.hidden=false;						
						this.refreshPage();
					}, 
					errors=>{
						this.resetDataError=errors[0].info;
						this.$refs.elResetDataSpinner.hidden=true;
						this.$refs.elResetDataStatus.hidden=false;
						this.$refs.elResetClose.hidden=false;
					});			
		},
		checkAvailableSoftwareVersion(){
			apiRequest('check_available_software_version', null, null, null, 
				ok=>{
					this.get_system();
				}, 
				errors=>{
					this.errorInfo=errors[0].info;
				});
			
		},
		initSoftwareUpdate(){
			this.updateResponse=[];
			this.updateError=false;
			this.$refs.update_ok.hidden=false;
			this.$refs.update_spinner.hidden=true;
		},
		forceUpdateToLatest(){
			this.$refs.update_ok.hidden=true;
			this.$refs.update_spinner.hidden=false;
			
			updateSoftware(
				success=>{
					this.$refs.update_spinner.hidden=true;
					this.updateResponse=success.info; 
					this.updateRevision=success.revision;
				},
				errors=>{
					console.log(errors);
					this.$refs.update_spinner.hidden=true;
					this.updateError=true;
					this.updateResponse=errors;
				}
			);
		},
		get_search_results() {
			if (this.menu_search.length>3){
				this.show_search_prompt = false
				payload     	= {'act':'get_search_results', 'search_term':this.menu_search}
				this.search_resd={}
				this.search_resd= fnapi(payload)
				console.log(this.search_resd)

				payload     	= {'act':'get_rwr_search_results', 'search_term':this.menu_search}
				json 			= fnapi(payload)
				console.log(json)
				this.rr_search_res= json[0]['rr_search_count']
			}else{
				this.show_search_prompt = true
			}
		},
		
    }
})
</script>