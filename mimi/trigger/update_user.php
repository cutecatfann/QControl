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
    <title>Update Result</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
if (isset($_POST['submit'])) {
    $original_usr_name = $_POST['original_usr_name'];
    $usr_name = $_POST['usr_name'];
    $usr_role = $_POST['usr_role'];
    $pword_hash = $_POST['pword_hash'];
    $user_email = $_POST['user_email'];

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
        echo 'An error occurred. Your data has not been updated.';
    } else {
        echo "User updated successfully.";
    }

    $stmt->close();
}

mysqli_close($dbconnect);
?>
</body>
</html>
