<?php

require_once '/home/SOU/pieperm/dbconfig.php'; 

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

// HTML Header and CSS Link
echo '<html>';
echo '<head>';
echo '<title>Product Type Quality Check Ratios</title>';
echo '<link rel="stylesheet" type="text/css" href="style.css">';
echo '</head>';
echo '<body>';

// Calling the stored procedure
$result = $dbconnect->query("CALL p_GetQualityCheckRatio()");

if ($result) {
    echo "<table>";
    echo "<tr><th>Product Type</th><th>Pass/Fail Ratio</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["Product Type"] . "</td><td>" . $row["Pass/Fail Ratio"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No data found";
}

echo '</body>';
echo '</html>';

$dbconnect->close();
?>
