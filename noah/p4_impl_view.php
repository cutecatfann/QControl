
<?php
/// Execute view logic via PHP.
/// Unable to execute view via PHP. This simply calls the select statement that the view contained.
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

    $sql = "select serial_number, (select stage_id from batch where item.batch_id = batch.batch_id) as item_stage from item";
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
