<?php
// load database configuration settings
require_once '/home/SOU/pieperm/dbconfig.php'; 

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
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<header>
    <img src="QControl.png" alt="QControl Logo"> 
    <h1>QControl Database System</h1>
</header>
<body>
    <p><strong>Author: </strong> Mimi </p>
    <p><strong>Type of SQL Object: </strong>View</p>
    <p><strong>Description: </strong> View the user information in the system and shows only data important to managers, simplified. It will show UserID, Name, Role, Status for all users. </p>
    <p><strong>Justification: </strong> This view will show which users have which roles, in order to see which users should be assigned to what tasks. This will also see which users in which roles have which permissions level. 
    
    Managers will use this in order to see what users have access to the database, their access level, and if they are active. They want a quick and effective way to see all their employees.</p>
    <p><strong>This code is hardened to SQL injections, there is no user input. Example: </strong></p>
    <p><strong>Expected Values: </strong> This should return all current users in the system in a table. As an example you will see: User ID : 2 | Name : Mimi | Role : q_manager | Status : Activ</p>
    <!-- <p>User ID : 2</p>
    <p>Name : Mimi</p>
    <p>Role : q_manager</p>
    <p>Status : Active</p> -->
    <p><strong>User Roles and Permissions</strong></p>
    <table>
        <tr>
            <th>User Role</th>
            <th>Create</th>
            <th>Read</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
        <tr>
            <td>Quality Manager</td>
            <td>✔</td>
            <td>✔</td>
            <td>✔</td>
            <td>✔</td>
        </tr>
        <tr>
            <td>Quality Technician</td>
            <td>✔</td>
            <td>✔</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Quality Lead</td>
            <td>✔</td>
            <td>✔</td>
            <td>✔</td>
            <td></td>
        </tr>
    </table>
    <p>  </p>
    <p>  </p>
    <p><strong>All Users in the Database  </strong> </p>
</body>
<?php

// build query string to fetch user role data
$sql = 'SELECT * FROM v_UserRole';  

// execute query using the connection created above
$retval = mysqli_query($mysqli, $sql);  

// start the table before the loop
echo "<table>";
echo "<tr><th>User ID</th><th>Name</th><th>Role</th><th>Status</th></tr>";

// modified loop to output data in table rows
if (mysqli_num_rows($retval) > 0) {  
    while ($row = mysqli_fetch_assoc($retval)) {
        echo "<tr>";
        echo "<td>" . $row['UserID'] . "</td>";
        echo "<td>" . $row['Name'] . "</td>";
        echo "<td>" . $row['Role'] . "</td>";
        echo "<td>" . $row['Status'] . "</td>";
        echo "</tr>";  
    }
} else {  
    echo "<tr><td colspan='4'>No results found</td></tr>";  
}  
echo "</table>";

mysqli_free_result($retval);

mysqli_close($mysqli); 
?>
