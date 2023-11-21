<?php

require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

if (isset($_POST['submit'])) {
    $batch_id = $_POST['batch_id'];
    $check_type_id = $_POST['check_type_id'];
    $check_value = $_POST['check_value'];
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    // Prepared statement to protect against SQL injection
    $stmt = $dbconnect->prepare("CALL p_RecordQualityCheck(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $batch_id, $check_type_id, $check_value, $user_id, $status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "New quality check record added successfully.";
    } else {
        echo "Failed to add the record: " . $dbconnect->error;
    }

    $stmt->close();
}

$dbconnect->close();
?>
