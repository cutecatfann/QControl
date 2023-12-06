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
$rows = mysqli_query($mysqli,
    'SELECT chck_id, batch_id, pt_name, chck_value, lower_bound, upper_bound, pass FROM v_BatchChcks order by chck_id limit 1000;');
// insert HTML header and text to the PHP page
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Pass/Fail</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<header>
    <img src="QControl.png" alt="QControl Logo"> 
    <h1>QControl Database System</h1>
</header>
<body>
    <p><strong>Author: </strong> Chandler </p>
    <p><strong>Type of SQL Object: </strong>Function</p>
    <p><strong>Description: </strong> Returns whether the check with a given ID passed. </p>
    <p><strong>Justification: </strong> Necessary to enable other functionality, such as calculating pass ratios. </p>
    <p><strong>This code is hardened to SQL injections, because it takes no input.  </p>
    <p><strong>Look at this view and observe that the checks have a pass/fail column. This is generated with the function.</strong></p>
    <?php
    if (mysqli_num_rows($rows) > 0) {
        echo "<table>" . "<tr>" .
            "<td> Check ID</td>" .
            "<td>Batch ID</td>" .
            "<td>Product Name</td>" .
            "<td>Check Value</td>" .
            "<td>Check Lower Bound</td>" .
            "<td>Check Upper Bound</td>" .
            "<td>Check Pass/Fail</td>" .
            "</tr>";

        while ($row = mysqli_fetch_assoc($rows)) {
            echo "<tr><td> {$row['chck_id']}  </td>> " .
                "<td>{$row['batch_id']} </td> " .
                "<td>{$row['pt_name']} </td> " .
                "<td>{$row['chck_value']} </td> " .
                "<td>{$row['lower_bound']} </td> " .
                "<td>{$row['upper_bound']} </td> " .
                (($row['pass']) ?
                    "<td style=\"background-color:green\"> Pass </td>" : "<td style=\"background-color:red\"> Fail </td>") .
                    "</td> </tr>" ;

        }
        echo "</table>";
    } else {
        echo "No results found";
    }
    ?>

>

</body>
</html>
