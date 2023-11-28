<?php
// This code is not vulnerable to basic SQL injection attacks.
// It uses prepared statements with parameter bindings which means the user input is treated as a parameter and is data only
// The query is parameterized with a placeholder ? 
// It also binds the $batch_id as ain integer and enforces types. It means someone cannot shove something in there.
// It also has errror handling and closes the database connection after use.

// load database configuration settings
require_once '/home/SOU/pieperm/dbconfig.php'; 

// configure PHP to report errors
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

// check for database connection errors and report them
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

// add HTML styling to the PHP page
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
    // query uses a placeholder for the batch_id
    $stmt = $dbconnect->prepare("SELECT f_AverageCheckValue(?) AS avg_check_value");

    // bind batch_id as an integer to the prepared statement
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // fetch data as an associative array
    $row = $result->fetch_assoc();

    // check if the query returned a result
    if ($row) {
        echo "Average Check Value for Batch ID $batch_id: " . $row["avg_check_value"];
    } else {
        echo "No data found for Batch ID $batch_id";
    }

    $stmt->close();
}

$dbconnect->close();
?>
