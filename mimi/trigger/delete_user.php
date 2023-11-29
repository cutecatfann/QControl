<?php
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
    <title>Delete Result</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
if (isset($_POST['submit'])) {
    $usr_name_to_delete = $_POST['usr_name_to_delete'];

    // prepare and bind
    $stmt = $dbconnect->prepare("DELETE FROM usr WHERE usr_name = ?");
    $stmt->bind_param("s", $usr_name_to_delete);

    if (!$stmt->execute()) {
            printf('An error occurred. Your data has not been submitted.  ');
            die("Error: " . $dbconnect->error);
        } else {
            echo "User deleted successfully.<br> Below is the log of your most recent change. <br>";

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
