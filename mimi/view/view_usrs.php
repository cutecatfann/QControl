<?php
require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$mysqli = mysqli_connect($hostname, $username, $password, $schema);

if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_error());
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<header>
    <img src="QControl.png" alt="QControl Logo"> 
    <h1>QControl Database System</h1>
</header>
<body>
<?php
echo "Connected successfully  <br>  <br>";

// build query string
$sql = 'SELECT * FROM v_UserRole';  

// execute query using the connection created above
$retval = mysqli_query($mysqli, $sql);  

// if more than 0 rows were returned fetch each row and echo values of interest
if (mysqli_num_rows($retval) > 0) {  
    while ($row = mysqli_fetch_assoc($retval)) {  
        echo "User ID : {$row['UserID']}  <br> " .  
             "Name : {$row['Name']} <br> " .  
             "Role : {$row['Role']} <br> " .  
             "Status : {$row['Status']} <br> " .  
             "--------------------------------<br>";  
    }
} else {  
    echo "No results found";  
}  

// free result set
mysqli_free_result($retval);

// close connection
mysqli_close($mysqli); 
?>
