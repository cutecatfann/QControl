<!-- 
Title: Update Users PHP
Author: Mimi Pieper
Date: 01/12/2023
Description: This is the PHP frontend which connects to the MySQL backend. It allows for updates of users, and is affected by the trigger after_usr_update, which logs all the changes into the usr_log table for an audit trail.
-->

<?php
// This code is not vulnerable to SQL injection attacks
// It uses prepared statements to separate the SQL command from the data
// The PHP variables are parameter bound to the SQL query, and ssss specifes that they are strings only
require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Result</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
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
    $original_usr_name = $_POST['original_usr_name'];
    $usr_name = $_POST['usr_name'];
    $usr_role = $_POST['usr_role'];
    $pword_hash = $_POST['pword_hash'];
    $user_email = $_POST['user_email'];

    if (!isValidInput($usr_name) || !isValidInput($usr_role) || !isValidInput($pword_hash) || !isValidInput($user_email)) {
        die("Invalid input detected. Please avoid using disallowed words.");
    }
    
    $query = "UPDATE usr SET ";
    $queryParams = [];
    $paramTypes = '';

    if (!empty($usr_name)) {
        $query .= "usr_name = ?, ";
        array_push($queryParams, $usr_name);
        $paramTypes .= 's';
    }
    if (!empty($usr_role)) {
        $query .= "usr_role = ?, ";
        array_push($queryParams, $usr_role);
        $paramTypes .= 's';
    }
    if (!empty($pword_hash)) {
        $query .= "pword_hash = ?, ";
        array_push($queryParams, $pword_hash);
        $paramTypes .= 's';
    }
    if (!empty($user_email)) {
        $query .= "user_email = ?, ";
        array_push($queryParams, $user_email);
        $paramTypes .= 's';
    }

    // Remove trailing comma and space
    $query = rtrim($query, ', ');

    // Add the WHERE clause
    $query .= " WHERE usr_name = ?";
    array_push($queryParams, $original_usr_name);
    $paramTypes .= 's';

    $stmt = $dbconnect->prepare($query);
    $stmt->bind_param($paramTypes, ...$queryParams);

    if (!$stmt->execute()) {
            printf('An error occurred. Your data has not been submitted.  ');
            die("Error: " . $dbconnect->error);
        } else {
            echo "User updated successfully.<br> Below is the log of your most recent change. <br>";

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
</body>
</html>
