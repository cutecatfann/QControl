<?php
/************
//
//  CS 460 Fall 2023
//  Script to fetch user roles for the form
//
*************/

require_once '/home/SOU/pieperm/dbconfig.php'; // Adjust the path to your config file

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
