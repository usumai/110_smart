Vue.component('fileupload',
{
	data() {
		return {
			message: '',
			status:'',
			total:0,
			current:0
		}
	},
	props: ['completed'],
	methods: {
        openUploadDlg(){

        	this.$refs.upload_file.value='';
            this.$refs.upload_file.click();

        },
        uploadData(){

            this.$refs.btn_open_progress.click();

            let file=this.$refs.upload_file.files[0]

            let reader = new FileReader();
            reader.onload = event => {
				try{
	                let uploadData=JSON.parse(event.target.result);
	                upload( uploadData, this.onUploadProgress,				
	                    (result)=>{
	                    	if(this.completed){
	                        	this.completed();
	                        }
					    },
	                    (errors)=>{
	                        this.status='Error';
	                        for( i in errors) {                            
	                            this.message=errors[i].code + ' - ' + errors[i].info;
	                        }
	                        console.log(this.message);
	                    }
	                );
				}catch(ex){		
					this.status='Error';
					this.message="Invalid file format. Make sure only uploading SMARTM JSON file created by DPN SMART";
					console.log(ex);
				}
            };
            reader.readAsText(file);
        },
        onUploadProgress (current, total, status, message){
            this.current=current;
            this.total=total;
            this.status=status;
            this.message=message;
            
            var percentage=(current/total)*100;
            
            $('#progress_value')
            .text(percentage.toFixed(1)+'%');                     
            
            $('#progress_bar')
            .width(percentage.toFixed(1)+'%')
            .attr('aria-valuenow',percentage.toFixed(1)); 
        }
	},
	template: '\
	<div>\
		<button type="button" class="btn btn-primary" @click="openUploadDlg">Upload<i class="fa fa-upload ml-2"></i></button>\
		<input hidden type="file" ref="upload_file" @change="uploadData" class="form-control-file">\
	    <button hidden type="button" ref="btn_open_progress"  class="btn btn-info btn-lg" data-toggle="modal" data-target="#dlg_progress" >Open Progress Dlg</button>\
	    <div class="modal fade" id="dlg_progress" role="dialog">\
	        <div class="modal-dialog">\
	            <div class="modal-content">\
	                <div class="modal-header" style="background-color: #5a95ca;">\
	                    <h5 class="modal-title" style="color: whitesmoke">File Upload</h5>\
	                    <button type="button" class="close" data-dismiss="modal">&times;</button>\
	                </div>\
	                <div class="modal-body">\
	                    <div class="container" style="width:100%">\
	                        <div v-if="status == \'Processing\'" class="alert alert-info"><strong>{{status}}!</strong> {{message}}</div>\
	                        <div v-if="status == \'Completed\'" class="alert alert-success"><strong>{{status}}!</strong> {{message}}</div>\
	                        <div v-if="status == \'Error\'" class="alert alert-danger"><strong>{{status}}!</strong> {{message}}</div>\
	                        <div class="progress">\
	                            <div id="progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">\
	                                <span id="progress_value">0%</span>\
	                            </div>\
	                        </div>\
	                        <div></div>\
	                        <div style="width: 100%; padding-top: 10px; display: flex;">\
	                            <span style="width: 50%">Current: {{current}}</span>\
	                            <span style="width: 50%">Total: {{total}}</span>\
	                        </div>\
	                    </div>\
	                </div>\
	                <div class="modal-footer">\
	                    <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>\
	                </div>\
	            </div>\
	        </div>\
	    </div>\
	</div>'
});