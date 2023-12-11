<!DOCTYPE html>
<html>
<head></head>
<body>
<table border="1" align="center">
<tr>
  <td>Batch</td>
  <td>Days since batch creation</td>
</tr>

<?php
   /// CALL PSEUDO-FUNCTION FROM PHP
   /// I was unable to create the function from SQL connection. All it would do is return the result the query used here, so this is semantically equivalent.
require_once '../../proj_config.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');
$mysqli = mysqli_connect($dbhost,$dbusrname,$dbpass,$dbname);

// Check connection
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_errno());
    exit();
}
echo "Connected successfully  <br><br>";

// if someone posts data
if(isset($_POST['submit'])) {
  $batch=$_POST['batch'];
  $query = "select DATEDIFF(CURRENT_DATE(), creation_date) as DaysSinceCreated from batch where batch_id = '$batch'";
  $retval = mysqli_query($mysqli, $query);

    // if one or more rows were returned
    if (mysqli_num_rows($retval) > 0) {
        while($row = mysqli_fetch_assoc($retval)) {
            echo "<tr><td>{$batch}</td><td>{$row['DaysSinceCreated']}</td></tr>\n";
        }
	echo "</table>";
    } else {
       echo "</table>No results found";
    }
}

mysqli_free_result($retval);
mysqli_close($mysqli);
?>
</body>
</html>
