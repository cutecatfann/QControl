<?php
// list_batches.php

require_once '/home/SOU/pieperm/dbconfig.php';

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

$query = "SELECT * FROM batch";
$result = mysqli_query($dbconnect, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Batch ID: " . $row['batch_id'] . ", Status: " . $row['batch_status'] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($dbconnect);
}

mysqli_close($dbconnect);
?>
