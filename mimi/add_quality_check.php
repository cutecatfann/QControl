<?php
// add_quality_check.php

require_once '/home/SOU/pieperm/dbconfig.php';

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

if (isset($_POST['submit'])) {
    // Extract and sanitize input
    $input_batch_id = mysqli_real_escape_string($dbconnect, $_POST['batch_id']);
    $input_check_type_id = mysqli_real_escape_string($dbconnect, $_POST['check_type_id']);
    $input_check_value = mysqli_real_escape_string($dbconnect, $_POST['check_value']);
    $input_user_id = mysqli_real_escape_string($dbconnect, $_POST['user_id']);
    $input_status = mysqli_real_escape_string($dbconnect, $_POST['status']);

    // Call the stored procedure
    $query = "CALL p_RecordQualityCheck($input_batch_id, $input_check_type_id, '$input_check_value', $input_user_id, '$input_status')";

    if (!mysqli_query($dbconnect, $query)) {
        echo "Error: " . mysqli_error($dbconnect);
    } else {
        echo "Quality check recorded successfully.";
    }
}

mysqli_close($dbconnect);
?>
