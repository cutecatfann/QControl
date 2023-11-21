<?php

require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
echo "Connected successfully  <br>  <br>";

if (isset($_POST['submit'])) {
    $batch_id = $_POST['batch_id'];

    // Prepared statement to protect against SQL injection
    $stmt = $dbconnect->prepare("SELECT f_AverageCheckValue(?) AS avg_check_value");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        echo "Average Check Value for Batch ID $batch_id: " . $row["avg_check_value"];
    } else {
        echo "No data found for Batch ID $batch_id";
    }

    $stmt->close();
}

$dbconnect->close();
?>
