<!-- 
Title: Delete Users PHP
Author: Mimi Pieper
Date: 01/12/2023
Description: This is the PHP frontend which connects to the MySQL backend. It allows for deletion users, and is affected by the trigger after_usr_delete, which logs all the changes into the usr_log table for an audit trail.
-->

<?php
require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

// connect to the database
$dbconnect = mysqli_connect($hostname, $username, $password, $schema);

// turn on errors
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Result</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
if (isset($_POST['submit'])) {
    $usr_name_to_delete = $_POST['usr_name_to_delete'];

    // Check if user exists
    $checkStmt = $dbconnect->prepare("SELECT * FROM usr WHERE usr_name = ?");
    $checkStmt->bind_param("s", $usr_name_to_delete);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $userExists = $result->num_rows > 0;
    $checkStmt->close();

    if ($userExists) {
        // user exists, proceed to delete
        $stmt = $dbconnect->prepare("DELETE FROM usr WHERE usr_name = ?");
        $stmt->bind_param("s", $usr_name_to_delete);

        if (!$stmt->execute()) {
            printf('An error occurred. Your data has not been submitted.  ');
            die("Error: " . $dbconnect->error);
        } else {
            echo "User deleted successfully.<br> Below is the log of your most recent change. <br>";

            // query the latest_usr_audit_entry view
            $result = $dbconnect->query("SELECT * FROM latest_usr_audit_entry");

            if ($result->num_rows > 0) {
                // output data in a table
                echo "<table><tr><th>Audit ID</th><th>User Name</th><th>Timestamp</th><th>User Role</th><th>User Email</th><th>Action Performed By</th><th>Action Type</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>".$row["audit_id"]."</td><td>".$row["usr_name"]."</td><td>".$row["timestamp"]."</td><td>".$row["usr_role"]."</td><td>".$row["user_email"]."</td><td>".$row["action_performed_by"]."</td><td>".$row["action_type"]."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
        }
        $stmt->close();
    } else {
        // user does not exist
        echo "Error: User not found in the database.";
    }
}

mysqli_close($dbconnect);
?>
</body>
</html>
