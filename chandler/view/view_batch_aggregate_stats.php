<?php
// load database configuration settings
//require_once '/home/SOU/pieperm/dbconfig.php';
require_once '../../dbconfig.php';

// Since there is no user input being used to construct the SQL query, there is no direct opportunity for SQL injection.
// As such, the code is hardened to basic SQL injection attacks.

// Configure error reporting to display errors
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

$mysqli = mysqli_connect($hostname, $username, $password, $schema);

// check for connection errors and display them
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_error());
    exit();
}

// insert HTML header and text to the PHP page
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Batch Aggregate Stats</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<header>
    <img src="QControl.png" alt="QControl Logo"> 
    <h1>QControl Database System</h1>
</header>
<body>
    <p><strong>Author: </strong> Chandler </p>
    <p><strong>Type of SQL Object: </strong>View</p>
    <p><strong>Description: </strong> This displays all the batches in the system, and aggregate stats about them.</p>
    <p><strong>Justification: </strong> This would be useful to managers to view  </p>
    <p><strong>This code is hardened to SQL injections, because here is no user input.</strong></p>
    <p><strong>Expected Values: </strong> This will return a list of batches, the percent of checks on those batches which passed, the total number of checks, and the status of the batch.</p>
</body>
<?php
//echo "Connected successfully  <br>  <br>";

// build query string to fetch user role data
$sql = 'SELECT * FROM v_BatchQualityStatus';

// execute query using the connection created above
$retval = mysqli_query($mysqli, $sql);  

// if more than 0 rows were returned fetch each row and echo values of interest
if (mysqli_num_rows($retval)) {
    echo "<table>" . "<tr>" .
        "<td> Batch ID</td>" .
        "<td>Batch Status</td>" .
        "<td>Product Type</td>" .
        "<td>Check Count</td>" .
        "<td>Pass Ratio</td>" .
        "</tr>";

    while ($row = mysqli_fetch_assoc($retval)) {  
        echo "<tr><td> {$row['batch_id']}  </td>> " .
             "<td>{$row['batch_status']} </td> " .
             "<td>{$row['pt_name']} </td> " .
             "<td>{$row['check_count']} </td> " .
             "<td>{$row['pass_ratio']} </td> </tr>" ;
    }
    echo "</table>";
} else {  
    echo "No results found";  
}  

// free result set
mysqli_free_result($retval);

// close connection
mysqli_close($mysqli); 
?>
