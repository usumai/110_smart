<!doctype html>
<html lang="en" class="h-100">

<?php
include "01_dbcon.php";

header("Cache-Control: no-store, max-age=0");
$theme_type=0;

$sql = "SELECT * FROM smartdb.sm10_set";
$result = $con->query($sql);
if ((! $con->error) &&($result->num_rows > 0)) {
    while($row = $result->fetch_assoc()) {
        $theme_type    = $row["theme_type"];
    }
}
$scheme_color="";

if ($theme_type==1) {
    $scheme_color = "
    body{
        background-color: #282923;
        color: white!important;
    }

    .card{
        background-color: #282923;
        border-color: white;
        color: white;
    }
    .text-muted{
        color: coral!important;
    }
    .form-control{
        background-color: #282923!important;
        color: white!important;
    }
    select{
        background-color: #282923;
        color: white!important;
    }
    b{
        color: coral!important;
    }
    table{
        color: white!important;
    }
    .btn-outline-dark{
        color: white!important;
        border-color: white!important;
    }
    input:disabled {
        background-color: #282923!important;
        border-color: #000!important;
    }
    ";
}
// $scheme_color = "";
$icon_spot_green    = "<span class='octicon octicon-primitive-dot text-success' style='font-size:30px'></span>";
$icon_spot_grey     = "<span class='octicon octicon-primitive-dot text-secondary' style='font-size:30px'></span>";
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<title>SMART Mobile</title>
	<link rel="icon" href="includes/favicon.ico">
	<link rel="stylesheet" href="includes/jquery-ui.css">
	<link rel="stylesheet" href="includes/bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">	
	<link rel="stylesheet" href="includes/datatables/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="includes/octicons/octicons.min.css">
	<link rel="stylesheet" href="includes/fontawesome-free-5.8.2-web/css/all.css" rel="stylesheet"> 
	<link rel="stylesheet" href="includes/css/global.css">       
    <style type="text/css">
        <?=$scheme_color?>
    </style>	


	
	<script src="includes/jquery-3.4.1.min.js"></script>
	<script src="includes/jquery.validate.min.js"></script>
	<script src="includes/jquery-ui.js"></script> 
	<script src="includes/datatables/jquery.dataTables.min.js"></script>
	<script src="includes/datatables/dataTables.bootstrap4.min.js"></script>
	<script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>	
	<script src="includes/vue.js"></script>
	<script src="includes/axios/axios.min.js" ></script>            
	<script src="includes/api.js" > </script>
	<script src="09_scripts.js"></script>  
	
</head>
	
<body class="d-flex flex-column mt-5 h-100">

<?php include "03_menu.php"; ?>