<?php
// load database configuration settings
require_once '/home/SOU/pieperm/dbconfig.php'; 

// Since there is no user input being used to construct the SQL query, there is no direct opportunity for SQL injection.
// As such, the code is hardened to basic SQL injection attacks.

// Configure error reporting to display errors
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$mysqli = mysqli_connect($hostname, $username, $password, $schema);

// check for connection errors and display them
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_error());
    exit();
}

// insert HTML header and text to the PHP page
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
    <p><strong>Author: </strong> Mimi </p>
    <p><strong>Type of SQL Object: </strong>View</p>
    <p><strong>Description: </strong> View the user information in the system and shows only data important to managers, simplified. It will show UserID, Name, Role, Status for all users. </p>
    <p><strong>Justification: </strong> This view will show which users have which roles, in order to see which users should be assigned to what tasks. This will also see which users in which roles have appropriate statuses. 
    
    Managers will use this in order to see what users have access to the database, their access level, and if they are active.</p>
    <p><strong>This code is hardened to SQL injections, there is no user input. Example: </strong></p>
    <p><strong>Expected Values: </strong> This should return all current users in the system in a list.</p>
</body>
<?php
//echo "Connected successfully  <br>  <br>";

// build query string to fetch user role data
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
