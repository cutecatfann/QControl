<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<p><b>BatchItems -- View</b></p>
	<p><b>Author: </b>Noah Mogensen</p>
	<p><b>Description: </b>View the serial number of all items side-by-side with that items current stage of production.</p>
	<p><b>Justification: </b>Allows for a bulk analysis of individual items and their current production stages.</p>
</body>
</html>
<?php
include_once('../../proj_config1.php');

// Turn error reporting on
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

// Create connection using procedural interface
$mysqli = mysqli_connect($dbhost, $dbusrname, $dbpass, $dbname);

// Check connection (and exit if it fails)
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_errno());
    exit();
}
echo "Connected successfully  <br>  <br>";

    $sql = "select * from v_BatchItems";
// execute query using the connection created above
$retval = mysqli_query($mysqli, $sql);  

// if more than 0 rows were returned fetch each row and echo values of interest
if(mysqli_num_rows($retval) > 0){  
 while($row = mysqli_fetch_assoc($retval)) {  
    echo "SERIAL NUMBER :{$row['serial_number']}  <br> ".  
         "PRODUCTION STAGE : {$row['item_stage']} <br> ".  
         "--------------------------------<br>";  
 } 
} else {  
	echo "No results found";  
}  

// free result set
mysqli_free_result($retval);
//close connection
mysqli_close($mysqli); 

?> 
