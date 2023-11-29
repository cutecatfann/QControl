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
        echo 'An error occurred. The user has not been deleted.';
    } else {
        echo "User deleted successfully.";
    }

    $stmt->close();
}

mysqli_close($dbconnect);
?>
</body>
</html>
