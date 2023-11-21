<?php
require_once '/home/SOU/pieperm/dbconfig.php'; 

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

$query = "SELECT DISTINCT usr_role FROM usr";
$result = mysqli_query($dbconnect, $query);
$roles = [];

while ($row = mysqli_fetch_assoc($result)) {
    array_push($roles, $row['usr_role']);
}

echo json_encode($roles);

mysqli_close($dbconnect);
?>
