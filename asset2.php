
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<title>SMART Mobile</title>
        <link rel="icon" href="includes/favicon.ico">
		<link rel="stylesheet" href="includes/bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="includes/octicons/octicons.min.css">
        <link rel="stylesheet" href="includes/fontawesome-free-5.8.2-web/css/all.css" rel="stylesheet"> <!--load all styles -->
        <link rel="stylesheet" href="includes/jquery-ui.css">
        <!-- <link rel="stylesheet" href="includes/datatables/dataTables.bootstrap4.min.css"> -->
        <script src="includes/jquery-3.4.1.min.js"></script>
	    <script src="includes/jquery.validate.min.js"></script>
        <script src="includes/jquery-ui.js"></script>   
        <style type="text/css">
            body {
                padding-top: 1rem;
                overflow-y: scroll;
            }
                    </style>
	</head><script src="includes/vue.js"></script>
<script src="includes/vuejs-datepicker.min.js"></script>

<script>
$( function() {

  if("stocktake"!="stocktake"){
    $("#tags").hide()
  }


    $( "#tags" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: "05_action.php",
                data: {
                    act: "get_asset_list",
                    search_term: request.term
                },
                success: function( data ) {
                    console.log(data);
                    json = JSON.parse(data)

                    response(json);
                    console.log(json)
                }
            });
        },
        select: function( event, ui ) {
            // console.log("Selected: " + ui.item.value + " aka " + ui.item.id )
            window.location.href = "11_ass.php?ass_id="+ui.item.value;
        }
    })

               // $arr["AssetDesc1"]       = $row["AssetDesc1"];
               // $arr["AssetDesc2"]       = $row["AssetDesc2"];
               // $arr["InventNo"]         = $row["InventNo"];
               // $arr["SNo"]              = $row["SNo"];
               // $arr["Location"]         = $row["Location"];
               // $arr["Room"]             = $row["Room"];
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( 
            "<div><b>"+item.Asset+"-"+item.Subnumber+"</b>:"+item.AssetDesc1+
            "<br>"+item.status_compl+" InventNo["+item.InventNo+"] Serial["+item.SNo+"] Location["+item.Location+""+item.Room+"]</div>" )
        .appendTo( ul );
    };

});
</script>


<body class="d-flex flex-column h-100">
<header>
  <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="index.php">smartM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a href='10_stk.php' class='nav-link text-success'>Stocktake</a></li>
                <li class="nav-item"><a href='12_ff.php' class='nav-link text-info'>Add First Found</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">v7</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01"><h6 class='dropdown-header'>Last checked for updates: 2019-09-30 11:51:20</h6><span class='dropdown-item'>Up to date as of 2019-08-30</span></div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Help</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class='dropdown-item' href='05_action.php?act=sys_open_image_folder'>Image folder</a>
                        <a class='dropdown-item' href='06_admin.php'>Archived Stocktakes</a>
                        <button type='button' class='dropdown-item btn btn-danger' data-toggle='modal' data-target='#modal_confirm_reset'>Reset all data</button>
                        <a class="dropdown-item" href="05_action.php?act=save_invertcolors">Invert Colour Scheme</a>
                                                <div class='dropdown-divider'></div><h6 class='dropdown-header'>Raw remainder</h6><span class='dropdown-item'>Not loaded</span>                        
                    </div>
                </li>
                <li class="nav-item">
                    <div class="ui-widget">
                        <input id="tags" class='form-control'>
                    </div>
                </li>
            </ul>
            <a href='10_stk.php' class='nav-link text-success'>Stocktake</a>




        </div>



    </nav>
</header>


<!-- <br><br><br> -->


<!-- Modal -->
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

<!-- Modal -->
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

<!-- Modal -->
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
        <a type="button" class="btn btn-danger" href='05_action.php?act=sys_reset_data'>Reset</a>
      </div>
    </div>
  </div>
</div>
<br><br>


<script>
    let arr = [{"ass_id":"1","create_date":"2019-09-05 00:00:00","create_user":"aline.hennessy","delete_date":null,"delete_user":null,"stkm_id":"1","storage_id":"125379","stk_include":"0","Asset":"708431","Subnumber":null,"impairment_code":null,"genesis_cat":"Original from storage","first_found_flag":null,"rr_id":null,"fingerprint":null,"res_create_date":null,"res_create_user":null,"res_reason_code":"ND10","res_reason_code_desc":null,"res_impairment_completed":null,"res_completed":"1","res_comment":null,"AssetDesc1":"ELECTRONIC KEY SAFE","AssetDesc2":"631680-0","AssetMainNoText":"ELECTRONIC KEY SAFE","Class":"5200","classDesc":"OP&E","assetType":"OPE","Inventory":"SNSW ST 2155","Quantity":"1","SNo":null,"InventNo":"SNSW000306","accNo":"3304","Location":"3304\/A0001","Room":"GF-20","State":"ACT","latitude":null,"longitude":null,"CurrentNBV":null,"AcqValue":"500.00","OrigValue":null,"ScrapVal":null,"ValMethod":"COST","RevOdep":"-500","CapDate":"2012-05-30 00:00:00","LastInv":"2016-04-28 00:00:00","DeactDate":null,"PlRetDate":null,"CCC_ParentName":"ESTATE AND INFRASTRUCTURE GROUP","CCC_GrandparentName":null,"GrpCustod":"CSIG","CostCtr":"681501","WBSElem":null,"Fund":"99998","RspCCtr":"681501","CoCd":"1000","PlateNo":null,"Vendor":null,"Mfr":null,"UseNo":"5","res_AssetDesc1":"STORAGE-SAFE-ELECTRONIC KEY","res_AssetDesc2":null,"res_AssetMainNoText":null,"res_Class":null,"res_classDesc":null,"res_assetType":null,"res_Inventory":null,"res_Quantity":null,"res_SNo":null,"res_InventNo":null,"res_accNo":null,"res_Location":null,"res_Room":"GF-203A","res_State":null,"res_latitude":null,"res_longitude":null,"res_CurrentNBV":null,"res_AcqValue":null,"res_OrigValue":null,"res_ScrapVal":null,"res_ValMethod":null,"res_RevOdep":null,"res_CapDate":null,"res_LastInv":null,"res_DeactDate":null,"res_PlRetDate":null,"res_CCC_ParentName":null,"res_CCC_GrandparentName":null,"res_GrpCustod":null,"res_CostCtr":null,"res_WBSElem":null,"res_Fund":null,"res_RspCCtr":null,"res_CoCd":null,"res_PlateNo":null,"res_Vendor":null,"res_Mfr":null,"res_UseNo":null,"res_isq_5":null,"res_isq_6":null,"res_isq_7":null,"res_isq_8":null,"res_isq_9":null,"res_isq_10":null,"res_isq_13":null,"res_isq_14":null,"res_isq_15":null}];    console.log(arr)
</script>




<!-- Modal -->











</body>
</html>


<script type="text/javascript" language="javascript" src="includes/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="includes/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>



