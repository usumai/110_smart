<?php

ini_set('max_execution_time', 0);


$hostname = "localhost";
$username = "root";
$password = "";
$dbname="smartdb";

// max_execution_time=3000000
// memory_limit=3000000M
// post_max_size=3000000M
// upload_max_filesize=3000000M
//ini_set('display_errors','Off');


$mysqlExecutable = "C:\\xampp\\mysql\\bin\\mysqld.exe"; // Update this to the actual path of mysql.exe

function isMySQLRunning() {
    $output = [];
    exec("tasklist", $output);
    foreach ($output as $line) {
        if (stripos($line, "mysqld.exe") !== false) { // Check for MySQL process
            return true;
        }
    }
    return false;
}

function startMySQL($mysqlExecutable) {
    $command = "start /B {$mysqlExecutable} --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\""; // Provide path to your MySQL configuration file
    $output = [];

    exec($command, $output, $status);
    return $status === 0;
}


if (!isMySQLRunning()) {
	
    if (!startMySQL($mysqlExecutable)) {
        die ("Failed to start MySQL server. Please check the executable path and configuration file.\n");
    }
	
}

// Create connection
$con = new mysqli($hostname, $username, $password);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} 


// Test if the device is connected to the internet


//$is_conn = is_connected();


$sql = "SELECT count(*) as dbexists FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'smartdb'";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dbexists = $row["dbexists"];
	}
}

if ($dbexists==0) {
     
    $con->query("CREATE DATABASE $dbname;"); 

	if(!$con->error){
     	$con=new mysqli($hostname, $username, $password, $dbname);
		if ($con->connect_error) {
	    	die("Connection failed: " . $con->connect_error);
		} 
	}

	header("Location: 05_action.php?act=sys_initialise");
}else{
	$con=new mysqli($hostname, $username, $password, $dbname);
	if ($con->connect_error) {
    	die("Connection failed: " . $con->connect_error);
	} 
}


function is_connected()
{
    $connected = @fsockopen("www.example.com", 80);//website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
        $is_conn = "connected";
    }else{
        $is_conn = false; //action in connection failure
        $is_conn = "not connected";
    }
    return $is_conn;
}


?>