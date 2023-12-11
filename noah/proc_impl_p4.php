<!DOCTYPE html>
<html>
<head></head>
<body>
<table border="1" align="center">
<p>Updated batch with production stage</p>
<tr>
  <td>Batch ID</td>
  <td>Production Stage</td>
</tr>

<?php
   /// Call procedure via PHP and view data to check results.

   require_once '../../proj_config1.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');
$mysqli = mysqli_connect($dbhost,$dbusrname,$dbpass,$dbname);

// Check connection
if (mysqli_connect_errno()) {
    printf("Connection failed: " . mysqli_connect_errno());
    exit();
}

// if someone posts data
if(isset($_POST['submit'])) {
  $batch=$_POST['batch'];
  $stage=$_POST['stage'];
  $query = "call updateBatch('$batch', '$stage')";
  mysqli_query($mysqli, $query);
  
	$select_query = "select batch_id, stage_id from batch where batch_id = '$batch'";
  	$retval = mysqli_query($mysqli, $select_query);

    // if one or more rows were returned
    if (mysqli_num_rows($retval) > 0) {
        while($row = mysqli_fetch_assoc($retval)) {
            echo "<tr><td>{$row['batch_id']}</td><td>{$row['stage_id']}</td></tr>\n";
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
