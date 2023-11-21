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
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
echo "Connected successfully  <br>  <br>";

if (isset($_POST['submit'])) {
    $usr_name = mysqli_real_escape_string($dbconnect, $_POST['usr_name']);
    $user_email = mysqli_real_escape_string($dbconnect, $_POST['user_email']);
    $pword_hash = mysqli_real_escape_string($dbconnect, $_POST['pword_hash']);
    $usr_role = mysqli_real_escape_string($dbconnect, $_POST['usr_role']);

    $query = "INSERT INTO usr (usr_name, usr_role, pword_hash, user_email) VALUES ('$usr_name', '$usr_role', '$pword_hash', '$user_email')";

    if (!mysqli_query($dbconnect, $query)) {
        printf('An error occurred. Your data has not been submitted.  ');
        die("Error: " . $dbconnect->error);
    } else {
        echo "User added successfully.";
    }
}

mysqli_close($dbconnect);
?>
