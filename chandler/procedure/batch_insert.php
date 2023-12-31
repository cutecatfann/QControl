<?php
// This code is not vulnerable to SQL Injection attacks
// It uses prepared statements with parameter bindings which means the inputs are treated strictly as parameters, not as part of the SQL command
// The call to my stored procedure is parameterized with ? which means that user input cannot get to SQL structures
// Variables are bound to type iisss for integers and strings which means that inputs are treated as the expected type
// There is error handling for the database connetion and it closes the database connetion after use

// load database configuration
//require_once '../../dbconfig.php';
require_once '/home/SOU/pieperm/dbconfig.php';

// configure error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

// handle connection errors
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

if (isset($_POST['submit'])) {
    // get data from the POST request
    $pt_name = $_POST['pt_name'];

    // prepared statement to protect against SQL injection
    $stmt = $dbconnect->prepare("CALL p_CreateBatch(?)");

    // bind parameters to the prepared statements
    $stmt->bind_param("s", $pt_name, );
    $stmt->execute();

    // check if it affected any rows to see if there was success
    if ($stmt->affected_rows > 0) {
        echo "New batch record added successfully.";
    } else {
        echo "Failed to add the record: " . $dbconnect->error;
    }

    $stmt->close();
}

$dbconnect->close();
?>
