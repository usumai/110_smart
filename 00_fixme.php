<?php

include "php/common/common.php";

execWithErrorHandler(

function() {
   
	$servername = "";
	$username   = "root";
	$password   = "";

	$con = new mysqli($servername, $username, $password);
	
	// Check connection
	if ($con->connect_error) {
	    throw new Exception("Connection failed: " . $con->connect_error);
	} 

	
	
	$sql_save = "DROP DATABASE smartdb;";
	mysqli_multi_query($con,$sql_save); 
	
	$addr_git= ' "\Program Files\Git\bin\git"  ';
	$output[]  = shell_exec($addr_git.' init 2>&1'); 
	$output[]  = shell_exec($addr_git.' remote set-url https://github.com/usumai/110_smart.git'); 
	$output[]= shell_exec($addr_git.' clean  -d  -f .');
	$output[]= shell_exec($addr_git.' reset --hard');  
	$output[]= shell_exec($addr_git.' pull https://github.com/usumai/110_smart.git');
	$result = ["info" => $output];
	echo json_encode(new ResponseMessage("OK",$result));
});

?>