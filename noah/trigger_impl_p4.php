<!DOCTYPE html>
<html>
<head></head>
<body>
<table border="1" align="center">
<tr>
  <td>USR</td>
  <td>Last Modified</td>
</tr>

<?php
// do use '../' in file path
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
  $hash=$_POST['pword_hash'];
  $query = "update usr set pword_hash='$hash' where usr_id=1";
  $update_result = mysqli_query($mysqli, $query);
  $query = "select usr_id, modified_date from usr where usr_id=1";
  $retval = mysqli_query($mysqli, $query);

    // if one or more rows were returned
    if (mysqli_num_rows($retval) > 0) {
        while($row = mysqli_fetch_assoc($retval)) {
            echo "<tr><td>{$row['usr_id']}</td><td>{$row['modified_date']}</td></tr>\n";
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
