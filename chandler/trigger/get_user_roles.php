<?php
// No user input, this code is not vulnerable to user injection attacks

// load database configuration settings
require_once '/home/SOU/pieperm/dbconfig.php'; 

// esablish a connection to the database
$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

// check for database errors and display them
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

$query = "SELECT DISTINCT usr_role FROM usr";
$result = mysqli_query($dbconnect, $query);

// initialize a array to hold the roles
$roles = [];

// fetch each row as an associative array and add the role to the roles array
while ($row = mysqli_fetch_assoc($result)) {
    array_push($roles, $row['usr_role']);
}

// output the roles array as a JSON string
echo json_encode($roles);

mysqli_close($dbconnect);
?>
