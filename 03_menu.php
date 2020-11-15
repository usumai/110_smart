<?php
$current_row=0;
if(array_key_exists("current_row",$_POST)){
	$current_row=$_POST["current_row"];
}elseif(array_key_exists("current_row",$_GET)){
	$current_row=$_GET["current_row"];
}


$opt_stk = '';
$sql = "SELECT stkm_id, stk_id, stk_name FROM smartdb.sm13_stk WHERE stk_include=1";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stkm_id	= $row["stkm_id"];
        $stk_id		= $row["stk_id"];
		$stk_name	= $row["stk_name"];
		$opt_stk   .= "<option value='".$stkm_id."'>".$stk_id.". ".$stk_name."</option>";
}}

?>


<script>
  
$(function(){
    $(document).on('click', '.btnHelp', function(){ 
		let helpWords = $(this).val();
		$('#areaHelpModal').html(helpWords)
	})
});



//This is where you left off: use this to move all of these options into the menu as required and set their vis using v-if

// menuRR  = "<div class='dropdown-divider'></div><h6 class='dropdown-header'>Raw Remainder</h6><span class='dropdown-item'>Assets loaded: "+sys["sett"][0]["rr_count"]+"</span>"
// // console.log("sys")
// // console.log(sys)
// helpContents=btnArchives+btnReset+btnInverColor
// btnVAction  = "<div id='areaVersionAction'><button type='button' class='dropdown-item btn' id='btnCheckForUpdates'>Check for updates</button></div>"
// styleUpdateAvailable =""
// if (sys["sett"][0]["versionLocal"]<sys["sett"][0]["versionRemote"]){
//     btnVAction  = "<button type='button' class='dropdown-item btn text-danger' data-toggle='modal' data-target='#modal_confirm_update'>Update available</button>"
//     styleUpdateAvailable = " text-danger "
// }
// menuUpdate  = "<div class='dropdown-divider'></div><h6 class='dropdown-header'>Software version<span class='float-right'>v"+sys["sett"][0]["versionLocal"]+"</span></h6>"+btnVAction


// menuAdd = ""
// $("#menuSearch").hide();
// if(system_stk_type=="stocktake"){
//     $(".initiateBTN").html("<a href='10_stk.php' class='nav-link text-success' >Summary</a>");
//     $("#menuSearch").show();
//     $("#tags").focus();
//     helpContents += btnImages+btnCreateTemplate
//     menuAdd = "<a class='nav-link dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='menuFF'>First found</a><div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdown_adds'>"+btnFF+"<div class='dropdown-divider'></div><h6 class='dropdown-header'>Templates <a href='21_templates.php' class='float-right'>Edit</a></h6><div id='areaTemplates'></div></div>"
// }else if(system_stk_type=="impairment"){
//     $(".initiateBTN").html("<a href='15_impairment.php' class='nav-link text-success' >Summary</a>");
//     helpContents += btnBackups
// }else{
//     $(".initiateBTN").html("");
// }





// users               = sys["pro"];
// active_profile_id   = sys["sett"][0]["active_profile_id"];
// btnUser = "";
// if (Object.entries(users).length === 0){
//     console.log("No users exist")
// }else{
//     // users       = ["Lucas","Sam","Max"];
//     activeUser  = 1;
//     for (let user in users){

	
//         // console.log("active_profile_id:"+active_profile_id)
//         // console.log("profile_id:"+profile_id)
//         if (active_profile_id==profile_id){
//             btnUser  += "<button type='button' class='dropdown-item btn text-success btnUser' data-toggle='modal' data-target='#modal_add_user' data-profile_id='"+profile_id+"' data-profile_name='"+profile_name+"' data-profile_phone_number='"+profile_phone_number+"'>"+profile_name+"</button>";
//         }else{
//             btnUser  += "<button type='button' class='dropdown-item btn btnUser' data-toggle='modal' data-target='#modal_add_user' data-profile_id='"+profile_id+"' data-profile_name='"+profile_name+"' data-profile_phone_number='"+profile_phone_number+"'>"+profile_name+"</button>";
//         }
//     }
// }
// btnAddUser  = "<button type='button' class='btn btnUser' data-toggle='modal' data-target='#modal_add_user' data-profile_id='0'>+ Add new user</button>"
// menuUser    = "<div class='dropdown-divider'></div><h6 class='dropdown-header'>User management</span></h6>"+btnUser+btnAddUser

// menuHelp    = "<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#'data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'>Help</a><div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp' >"+helpContents+menuRR+menuUpdate+menuUser+"</div>"
// $("#menuHelp").html(menuHelp);
// $("#menuAdd").html(menuAdd);







// 	// fnDo("get_templates","LoadTemplates",0)

  	$(document).on('click', '.btnInitTemplate', function(){ 
		let ass_id = $(this).val();
		$("#ass_id").val(ass_id);
		// console.log(ass_id)
	})


	
//   	$(document).on('click', '.btnUser', function(){ 
// 		// let profile_id        	= $('#profile_id').val();
// 		profile_id			= $(this).data("profile_id");
// 		profile_name		= $(this).data("profile_name");
// 		profile_phone_number= $(this).data("profile_phone_number");
// 		if(profile_id==0){
// 			$("#btnDeleteUser").hide();
// 		}else{
// 			$("#btnDeleteUser").show();
// 		}
// 		edit_profile_id = profile_id;
// 		$("#profile_name").val(profile_name);
// 		$("#profile_phone_number").val(profile_phone_number);
// 	})	


// 	$(document).on('click', '#btnDeleteUser', function(){ 
// 		$.post( {
// 			url: "05_action.php",
// 			data: {
// 				act: "save_delete_user_profile",
// 				edit_profile_id
// 			},
// 			success: function( data ) {
// 				// console.log(data)
// 				// Refresh menu
// 				fnDo("get_system","SetMenu",1)
// 				// Dismiss modal
// 				$('#modal_add_user').modal('toggle')
// 			}
// 		});
// 	})

	
//   	$(document).on('click', '#btnSaveUser', function(){ 
// 		let profile_name        = $('#profile_name').val();
// 		let profile_phone_number= $('#profile_phone_number').val();

// 		// Save details
// 		$.post( {
// 			url: "05_action.php",
// 			data: {
// 				act: "save_edit_user_profile",
// 				edit_profile_id,
// 				profile_name,
// 				profile_phone_number,
// 			},
// 			success: function( data ) {
// 				// console.log(data)
// 				// Refresh menu
// 				fnDo("get_system","SetMenu",1)
// 				// Dismiss modal
// 				$('#modal_add_user').modal('toggle')
// 			}
// 		});
// 		// Set active_profile_id as new user

// 	})	

  
	

//     $(document).on('click', '#btnCheckForUpdates', function(e){ 
// 		e.stopPropagation();
// 		if ($('.dropdown').find('#dropdownHelp').is(":hidden")){
// 			$('#dropdownHelp').dropdown('toggle');
// 		}
// 		$('#areaVersionAction').html("<span class='dropdown-item text-warning'>Checking server version<br><div class='spinner-border text-center' role='status'><span class='sr-only'>Loading...</span></div></span>");
// 		let nextAction = fnDo("save_check_version","CheckUpdates",0)
//     })

//     $( "#tags" ).autocomplete({
//         source: function( request, response ) {
//             search_term = request.term;
//             $.ajax( {
//                 url: "05_action.php",
//                 data: {
//                     act: "get_asset_list",
//                     search_term: request.term
//                 },
//                 success: function( data ) {
//                     json = JSON.parse(data)
//                     response(json);
//                 }
//             });
//         },
//         select: function( event, ui ) {
//             // console.log("Selected: " + ui.item.value + " aka " + ui.item )
//             if(ui.item.Asset=="Raw remainder results"){
//               window.location.href = "14_rr.php?search_term="+search_term;
//             }else{
//               window.location.href = "11_ass.php?ass_id="+ui.item.value;
//             }
//         }
//     })
//     .autocomplete( "instance" )._renderItem = function( ul, item ) {
//       if (item.Asset == "Raw remainder results"){
//         row = "<div><h3>Raw remainder count:</h3>"+item.Subnumber+"</div>" 
//       }else{
//         row = "<div><b>"+item.Asset+"-"+item.Subnumber+"</b>:"+item.AssetDesc1+"<br>"+item.status_compl+" InventNo["+item.InventNo+"] Serial["+item.SNo+"] Location["+item.Location+""+item.Room+"]</div>"
//       }
//       return $( "<li>" )
//         .append(row)
//         .appendTo( ul );
//     };

// });
</script>


<div id="appmenu">
	<body class="d-flex flex-column h-100">
	<header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="index.php"><h3><b>smartM</b></h3></a>
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
						<a class='nav-link dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='menuFF'><b>First Found</b></a>
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
						<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'><b>IS tools</b></a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>
							<a class='dropdown-item' href='19_toggle.php'>Toggle primary/backup</a>
						</div>
					</li>

					<li class="nav-item dropdown" v-if="sysd.act_type=='ga_stk'">
						<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#'data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'><b>GA Tools</b></a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>
							<h6 class='dropdown-header'>Raw Remainder</h6>
							<span class='dropdown-item'>Assets loaded: {{ rwrd.rr_rowcount }}</span>
						</div>
					</li>


<!-- System Dropdown Menu -->
					<li class="nav-item dropdown">
						<a class='nav-link dropdown-toggle "+styleUpdateAvailable+"' href='#'data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='headingHelp'
						:class="{'text-danger':sysd.versionLocal<sysd.versionRemote}"><b>System</b></a>
						<div class='dropdown-menu' aria-labelledby='dropdown01' id='dropdownHelp'>

							<h6 class='dropdown-header'>System settings</h6>
							<button type='button' class='dropdown-item btn btn-danger' data-toggle='modal' data-target='#modal_confirm_reset'>Reset all data</button>
							<a class='dropdown-item' href='05_action.php?act=save_invertcolors'>Invert Colour Scheme</a>
							<!-- <a class='dropdown-item' href='05_action.php?act=sys_open_image_folder'>Image folder</a> -->

							<div class='dropdown-divider'></div>
							<h6 class='dropdown-header'>Installed software version<span class='float-right'>v{{ sysd.versionLocal }}</span></h6>
							<h6 class='dropdown-header'>Newest available version<span class='float-right'>v{{ sysd.versionRemote }}</span></h6>
							<h6 class='dropdown-header'>Last checked<span class='float-right'>{{ sysd.date_last_update_check }}</span></h6>
							<button type='button' v-if="sysd.versionLocal==sysd.versionRemote" class='dropdown-item btn' v-on:click='save_check_version()'>Check for new version</button>
							<span v-if="vcheck==2" class='dropdown-item'>You need to be connected to the internet to check for a new version</span>

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
				<a :href="'10_stk.php?current_row='+ <?=$current_row ?>" class='nav-link text-success' v-if="sysd.act_type=='ga_stk'">Summary</a>
				<a :href="'15_impairment.php?current_row='+ <?=$current_row ?>" class='nav-link text-success' v-if="sysd.act_type=='is_audit'" >Summary</a>
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
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Initiate asset template</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			
				<form method='post' action='05_action.php'>
					<div class="modal-body">  
						<p class="lead">Please select a stocktake to initiate this template into</p>
						<select name='stkm_id' class='form-control' v-if='actvd'>
							<option v-for='(activity, index) in actvd' :value="activity.stkm_id">{{activity.stk_id}}. {{activity.stk_name}}</option>
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




	<!-- Update App Version Dialog -->
	<div class="modal fade" id="modal_confirm_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Update to latest version</h5>
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
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Push this version to the master</h5>
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

	<!-- System Reset Dialog -->
	<div class="modal fade" id="modal_confirm_reset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Delete all data</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">  
			<p class="lead">Reseting SMARTm will delete all data on this device.<br><br>Are you sure you want to proceed?</p>  
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<a class="btn btn-danger" href='05_action.php?act=sys_reset_data'>Reset</a>
			<a class="btn btn-danger" href='05_action.php?act=sys_reset_data_minus_rr'>Reset excluding RR</a>
		</div>
		</div>
	</div>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="modal_confirm_reset_minus_rr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Delete all data except for raw remainder data</h5>
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
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Create a new user</h5>
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
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Help</h5>
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
		userProfiles:[],
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
		}
    },
    created() {
		this.refresh_sys()
		this.get_rr_stats()
	},
	mounted(){		
		this.setUserProfiles();
	},
    methods:{
		setUserProfiles(){
			getUserProfiles(result=>{

				for(profile in result){
					if(result[profile].profile_id==this.sysd.active_profile_id){
						result[profile].current=true;
						this.setCurrentUserProfile(result[profile], profile);
					}
				}
				this.userProfiles=result;
			}, errors=>{});
		},
		setCurrentUserProfile(profile, index){
			console.log('**** setCurrentProfile **************');
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
            payload     = {'act':'get_system'}
            json   		= fnapi(payload)
            this.sysd   = json[0]
            console.log("sysd")
			console.log(this.sysd)
			if (this.sysd.act_type=="ga_stk"){
        		this.get_stk_templates()
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
            this.actvd	= fnapi(payload)
			console.log(this.actvd)
			this.template_ass_id = template.ass_id
			$('#modal_initiate_template').modal('show')
		}, 
		get_rr_stats(){
            payload     = {'act':'get_rr_stats'}
			json		= fnapi(payload)
			this.rwrd 	= json[0]
			console.log(this.rwrd)
		},

		save_check_version(){
            payload     = {'act':'save_check_version'}
			json		= fnapi(payload)
			this.vcheck	= json[0]['test_results']
			console.log(this.vcheck)
			if(this.vcheck==1){
				this.get_system()
			}
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