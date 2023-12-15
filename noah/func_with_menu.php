<!DOCTYPE html>
<html>
<head>
</head>
<body>
        <p><b>TimeSinceBatchCreation -- Function</b></p>
        <p><b>Author: </b>Noah Mogensen</p>
        <p><b>Description: </b>Display the number of days since a batch was first created.</p>
        <p><b>Justification: </b>Allows for the identification for old or inactive batches.</p>
<?php
require_once '../../proj_config1.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');
$mysqli = mysqli_connect($dbhost,$dbusrname,$dbpass,$dbname);

// Check connection
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_errno());
    exit();
}
$query = "select batch_id from batch";
$retval = mysqli_query($mysqli, $query);

if (mysqli_num_rows($retval) > 0) {
        echo "<form action=\"p4_impl_func.php\" method=\"POST\">";
        echo "<h4>Select batch by ID</h4>";
        echo "<select name='batch'>";

        // creates an option in the select drop-down for each id returned from the query on line 33
        while ($row = $retval->fetch_assoc()) {
                unset($id); // clears previous value
                $id = $row['batch_id']; //sets new value to the next id returned
            echo '<option value="'.$id.'">'.$id.'</option>'; //list new value as option in select element

        }
        echo "</select>"; // ends the select drop down box
        echo "</select>"; // ends the select drop down box
        echo "<input type=\"submit\" value=\"Submit\" name=\"submit\">"; // creates a submit button
        echo "</form>"; // end the formf (mysqli_num_rows($retval) > 0) {
        echo "</body></html>";
}
?>

