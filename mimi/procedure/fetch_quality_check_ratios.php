<!-- 
Title: Fetch Status PHP
Author: Mimi Pieper
Date: 01/12/2023
Description: This is the PHP which connects to the MySQL backend. It provides a JSON with all the statuses for the database
-->

<?php
// No user input :)
// As such, this code is not vulnerable to SQL injection attacks

// load database configuration
require_once '/home/SOU/pieperm/dbconfig.php'; 

// configure error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$dbconnect = new mysqli($hostname, $username, $password, $schema);

// handle database connection errors
if ($dbconnect->connect_error) {
    die("Database connection failed: " . $dbconnect->connect_error);
}

// HTML header and CSS link
// being HTML output for the page
echo '<html>';
echo '<head>';
echo '<title>Product Type Quality Check Ratios</title>';
echo '<link rel="stylesheet" type="text/css" href="style.css">';
echo '</head>';
echo '<body>';

// call the stored procedure
$result = $dbconnect->query("CALL p_GetQualityCheckRatio()");

if ($result) {
    // start the table structure to display the results
    echo "<table>";
    echo "<tr><th>Product Type</th><th>Pass/Fail Ratio</th></tr>";

    // iterate through each row of results
    // output each row as a table row
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["Product Type"] . "</td><td>" . $row["Pass/Fail Ratio"] . "</td></tr>";
    }
    echo "</table>";
    // end table structure
} else {
    echo "No data found";
}

echo '</body>';
echo '</html>';
// end HTML output

$dbconnect->close();
?>
