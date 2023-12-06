<!-- 
Title: Fetch Status
Author: Mimi Pieper
Date: 01/12/2023
Description: This is the PHP frontend which connects to the MySQL backend. It gets a list of current statuses in the data base which are used to automatically populate drop down menus for the user roles so that if there are database changes they are automatically updated.
-->

<?php
// This code is not vulnerable to SQL injection since there is no user input
// load database configuration settings
require_once '/home/SOU/pieperm/dbconfig.php'; 

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

// handle database connection errors
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

$query = "SELECT DISTINCT batch_status FROM batch"; 
$result = mysqli_query($dbconnect, $query);

// arry to hold statuses
$statuses = [];

// fetch each row as an associative array and add the status to the statuses array
while ($row = mysqli_fetch_assoc($result)) {
    array_push($statuses, $row['batch_status']); // Corrected to 'batch_status'
}

// output statuses array as a JSON string
echo json_encode($statuses);

mysqli_close($dbconnect);
?>
