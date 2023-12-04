<!-- 
Title: Insert Users PHP
Author: Mimi Pieper
Date: 01/12/2023
Description: This is the PHP frontend which connects to the MySQL backend. It allows for insertions of new users, and is affected by the trigger after_usr_insert, which logs all the changes into the usr_log table for an audit trail.
-->

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

function isValidInput($input) {
    // blacklist of disallowed words
    $blacklist = ['select', 'drop', 'insert', 'delete', 'fuck', 'shit', 'damn','cunt','bitch','update','nigger','penis','pussy','cock','dick','fucker','motherfucker','tits'];

    $inputLower = strtolower($input);

    // check if input contains any blacklisted word
    foreach ($blacklist as $word) {
        if (strpos($inputLower, $word) !== false) {
            return false; // disallowed word found
        }
    }
    return true; 
}

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
    
    if (!isValidInput($usr_name) || !isValidInput($usr_role) || !isValidInput($pword_hash) || !isValidInput($user_email)) {
        die("Invalid input detected. Please avoid using disallowed words.");
    }
    
    if (!$stmt->execute()) {
            printf('An error occurred. Your data has not been submitted.  ');
            die("Error: " . $dbconnect->error);
        } else {
            echo "User added successfully.<br><br>";

            // Query the latest_usr_audit_entry view
            $result = $dbconnect->query("SELECT * FROM latest_usr_audit_entry");

            if ($result->num_rows > 0) {
                // Output data in a table
                echo "<table><tr><th>Audit ID</th><th>User Name</th><th>Timestamp</th><th>User Role</th><th>User Email</th><th>Action Performed By</th><th>Action Type</th></tr>";
                // fetch associative array
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>".$row["audit_id"]."</td><td>".$row["usr_name"]."</td><td>".$row["timestamp"]."</td><td>".$row["usr_role"]."</td><td>".$row["user_email"]."</td><td>".$row["action_performed_by"]."</td><td>".$row["action_type"]."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
        }

    $stmt->close();
}

mysqli_close($dbconnect);
?>
