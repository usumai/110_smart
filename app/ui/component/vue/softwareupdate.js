Vue.component('softwareupdate',
{
	data() {
		return {
			updateResponse:[],
			updateRevision:'',
			status: '',
		}
	},
	props: ['completed'],
	mounted(){
		this.init();
	},
	methods: {
    	refreshPage(){
    		window.location.reload();
    	},
    	init(){
			this.updateResponse=[];
			this.updateError=false;
			this.$refs.update_ok.hidden=false;
			this.$refs.update_spinner.hidden=true;
		},	
		update(){
			this.$refs.update_ok.hidden=true;
			this.$refs.update_spinner.hidden=false;
			
			updateSoftware(
				success=>{
					this.status='OK';
					this.$refs.update_spinner.hidden=true;
					this.updateResponse=success.info; 
					this.updateRevision=success.revision;
				},
				errors=>{
					console.log(errors);
					this.$refs.update_spinner.hidden=true;
					this.status='ERROR';
					this.updateResponse=errors;
				}
			);
		},		
	},
	template: '\
<div width="500px" height="300px" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">\
	  <div class="modal-dialog" role="document">\
	    <div class="modal-content">\
	      <div class="modal-header" style="background-color: #5a95ca;">\
	        <h5 class="modal-title" id="exampleModalLabel" style="color: whitesmoke">Software Update</h5>\
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
	          <span aria-hidden="true">&times;</span>\
	        </button>\
	      </div>\
	      <div class="modal-body">\
			<table width="100%" height="100%">\
				<tr>\
				  <td align="center">\
				      <p style="text-align: left;"><strong>Warning:</strong></p>\
				      <p style="text-align: justify;"><i style="color: red">SmartM Software update and data reset. This will delete all application data stored on this device, so make sure you backup everything before attempting this.</i></p>\
				      <p style="text-align: left;">Please make sure you are connected to the internet.</p>\
				  </td>\
				</tr>\
				<tr>\
					<td ref="update_spinner" style="text-align: center">\
						<div class="spinner-border text-info" role="status">\
						  <span class="sr-only">Updating...</span>\
						</div>\
					</td>\
					<td v-if="status != \'\'">\
						<div v-if="updateRevision != \'OK\'" class="alert alert-info"><strong>Update Completed!</strong>\
							<div ><strong>revision: </strong>{{updateRevision}}</div>\
							<div v-if="(updateResponse) && (updateResponse.length>0)" v-for="info in updateResponse"><i>{{info}}</i></div>\
						</div>\
						<div v-if="status==\'ERROR\'" class="alert alert-danger">\
							<strong >Update Failed!</strong>\
							<div v-if="(updateResponse) && (updateResponse.length>0)" v-for="error in updateResponse"><i>{{error.code}}-{{error.info}}</i></div>\
						</div>\
					</td>\
				</tr>\
			</table>\
	      </div>\
	      <div class="modal-footer">\
	      	<button type="button" ref="update_ok" class="btn btn-outline-dark" @click="update()">Update</button>\
	        <button type="button" ref="update_close" class="btn btn-outline-dark" data-dismiss="modal" @click="refreshPage()">Close</button>\
	      </div>\
	    </div>\
	  </div>\
</div>'
})