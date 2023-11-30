<?php
// This code is not vulnerable to SQL injection attacks
// It uses prepared statements to separate the SQL command from the data
// The PHP variables are parameter bound to the SQL query, and ssss specifes that they are strings only

// load database configuration
require_once '/home/SOU/pieperm/dbconfig.php'; 

// configure error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

// handle database connection errors 
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

// display HTML styling and headers to the page
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
//echo "Connected successfully  <br>  <br>";

if (isset($_POST['submit'])) {
    // prepare and bind
    // use placeholders to prevent SQL injection
    $stmt = $dbconnect->prepare("INSERT INTO usr (usr_name, usr_role, pword_hash, user_email) VALUES (?, ?, ?, ?)");

    // bind PHP variables to the prepared statement as strings
    $stmt->bind_param("ssss", $usr_name, $usr_role, $pword_hash, $user_email);

    // Set parameters and execute
    $usr_name = $_POST['usr_name'];
    $usr_role = $_POST['usr_role'];
    $pword_hash = $_POST['pword_hash'];
    $user_email = $_POST['user_email'];
    
    if (!$stmt->execute()) {
        printf('An error occurred. Your data has not been submitted.  ');
        die("Error: " . $dbconnect->error);
    } else {
        echo "User added successfully.";
    }

    $stmt->close();
}

mysqli_close($dbconnect);
?>
