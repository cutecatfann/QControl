<?php
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
