<?php 
include "02_header.php"; 

$ass_id	= $_GET["ass_id"];

?>

  <style>
    .img-container {
      position: relative;
      width: 100%;
      height: 100%;
    }

    .img-container video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .camera-switch-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 10;
      background-color: transparent;
      border: none;
      color: white; /* or any color that contrasts with the image */
      font-size: 1.5rem;
      padding: 0.5rem;
      cursor: pointer;
    }

	.camera-switch-btn:hover {
      color: #ffc107; /* optional hover effect */
    }
  </style>
<div id="app">
	<canvas ref="canvas" width="800" height="600" hidden></canvas>

    <div class='container-fluid'>
        <table width="100%">
        	<tr>
        		<td width="20%">
        			<form method="post" action="05_action.php" >
        				<input type="hidden" name="act" value="save_photo">
        				<input type="hidden" name="ass_id" value="<?=$ass_id?>">
        				<input type="hidden" name="res_img_data" :value="photoUrl">
        				<button v-if="mode=='p'" type="submit" class="btn btn-success btn_acceptphoto" id="btn_acceptphoto"><span class='octicon octicon-check' style='font-size:30px'></span></button>
						<br><br><br>
        	        	<a href="11_ass.php?ass_id=<?=$ass_id?>" class="btn btn-danger" id="btn_cancelphoto"><span class='octicon octicon-x' style='font-size:30px'></span></a>
        			</form><br><br><br><br><br>
        		</td>
        		<td width="60%">
        			<div v-if="mode=='p'" class="row"  @click="savePhoto()">
						<img  :src="photoUrl" width='100%' height='90%'/>
        			</div>
        			<div v-if="mode=='v'" class="row" >
						<div class="col alert alert-info">Resolution: {{settings.width}}x{{settings.height}}</div>
        				<div class="img-container">
							<video  ref="video" width="100%" height="100%" @click="capturePhoto()" autoplay ></video>
        					<button class="btn btn-primary camera-switch-btn" @click="switchCamera()">
								<i class="fa fa-camera"></i>
							</button>
						</div>
					</div>
        		</td>
        	
        		<td width="20%" align="right">
        			<form method="post" action="05_action.php" >
        				<input type="hidden" name="act" value="save_photo">
        				<input type="hidden" name="ass_id" value="<?=$ass_id?>">
        				<input type="hidden" name="res_img_data" :value="photoUrl">
        	        	<button v-if="mode=='p'" type="submit" class="btn btn-success btn_acceptphoto" id="btn_acceptphoto"><span class='octicon octicon-check' style='font-size:30px'></span></button><br>
        				<br><br>
        	        	<a href="11_ass.php?ass_id=<?=$ass_id?>" class="btn btn-danger" id="btn_cancelphoto"><span class='octicon octicon-x' style='font-size:30px'></span></a>
        			</form><br><br><br><br><br>
        		</td>
        
        	</tr>
        </table>

	</div>
</div>



<!-- <canvas id="canvas" width="1600" height="1200"></canvas> -->
<!-- <div id="test"></div> -->



<script>
// Grab elements, create settings, etc.
//var video = document.getElementById('video');


// Elements for taking the snapshot
//var canvas = document.getElementById('canvas');
//var context = canvas.getContext('2d');
//var video = document.getElementById('video');

// Trigger photo take
// document.getElementById("snap").addEventListener("click", function() {
// 	context.drawImage(video, 0, 0, 640, 480);
// 	var res_img_data = canvas.toDataURL();
// 	alert(res_img_data);
// 	$("#test").text(res_img_data);
// });

/*
$(document).ready(function() {
	$("#canvas").hide();
	$("#area_photo").hide();
	$(".btn_acceptphoto").hide();
	$("#video").click(function(){
		context.drawImage(video, 0, 0, 800, 600);
		var res_img_data = canvas.toDataURL();
		$("#area_video").hide();
		$("#area_photo").show();
		$(".btn_acceptphoto").show();

		// $("#test").text(res_img_data);
		$("#res_img_data").val(res_img_data);
		$("#res_img_data2").val(res_img_data);
		$("#area_photo").html("<img src='"+res_img_data+"' width='100%' height='90%'/>");
	});

	$("#area_photo").click(function(){
		$("#area_video").show();
		$("#area_photo").hide();
		$(".btn_acceptphoto").hide();
	});
});
*/



</script>
<script>
const app = new Vue({
    el: '#app',
    data: {
		cameras: [],
		currentCamera:0,
		settings: {},
		mode: 'v',
		photoUrl:""
    },
    created() {
		
    },
    mounted () {
        navigator.mediaDevices.enumerateDevices().then(
			devices=>{
				this.cameras=devices.filter(d=>d.kind=="videoinput");	
				this.switchCamera();				
			}
		);
    } ,
    methods:{
		capturePhoto(){
			this.mode = 'p';
			var context=this.$refs.canvas.getContext('2d');			
			this.$refs.canvas.height=this.settings.height;
			this.$refs.canvas.width=this.settings.width;
			context.drawImage(this.$refs.video, 0, 0, this.settings.width, this.settings.height);


			var res_img_data = this.$refs.canvas.toDataURL();
			this.photoUrl=res_img_data;
		},
		savePhoto(){
			this.mode='v';
		},
		nextCamera(){
			this.currentCamera++;
			if(this.currentCamera>=this.cameras.length){
				this.currentCamera=0;
			}
		},
		async switchCamera(){
			this.nextCamera();
			var cap=this.cameras[this.currentCamera].getCapabilities();
			const constraints = {
				video: { 
					facingMode: { exact: cap.facingMode[0] },
					width: { ideal: 3840 },
      				height: { ideal: 2160 }
				}
			};
			try {
				const stream = await navigator.mediaDevices.getUserMedia(constraints);
				
				this.$refs.video.srcObject = stream;
				var tracks=stream.getVideoTracks();
				this.settings=tracks[0].getSettings();

			} catch (err) {
				console.error("Camera access error:", err);
			}
		}
	}
});
</script>
<?php include "04_footer.php"; ?>