<?php
// load database configuration settings
require_once '/home/SOU/campbellr/dbconfig.php';
//require_once '../../dbconfig.php';

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
    <title>Record New Quality Check</title>
    <link rel="stylesheet" type="text/css" href="../../style.css">
</head>
<header>
    <img src="../../homepage/QControl.png" alt="QControl Logo">
    <h1>QControl Database System</h1>
</header>
<body>
    <p><strong>Author: </strong> Chandler</p>
    <p><strong>Type of SQL Object: </strong>Procedure</p>
    <p><strong>Description: </strong> This procedure allows a user to safely create a new batch in a coherent state, that is to say, with status in-process and in the first stage of production.</p>
    <p><strong>Justification: </strong> This will be used daily by Quality Leads to create records of the day's work. Doing it manually with an insert statement could allow batches to be created in inconsistent states. </p>
    <p><strong>This code is hardened to SQL injections, try it out! Example: </strong>Try to put something nasty in the POST request</p>
    <h1>Record a New Batch</h1>
       <form action="batch_insert.php" method="POST">
           <label>Select Product Type</label>
           <select name="pt_name" required>
               <?php
                    $arr = mysqli_query($mysqli, "select pt_name from product_type;");
                    while ($name = mysqli_fetch_array($arr)):
               ?>
               <option value="<?php echo $name["pt_name"];?>"> <?php echo $name['pt_name']?> </option>
               <?php
                    endwhile;
                ?>
           </select>
        <input type="submit" value="Submit" name="submit">
    </form>

    <script>
        // Fetch and populate statuses from the server
        window.addEventListener('load', function () {
            fetch('batch_insert.php').then(response => response.json()).then(data => {
                const selectElement = document.querySelector('select[name="status"]');
                data.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status;
                    option.textContent = status;
                    selectElement.appendChild(option);
                });
            });
        });
    </script>
</body>
</html>

<?php



// close connection
mysqli_close($mysqli);
?>
