<?php
// Database must exist
session_start();
$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
$pidArray = [1,2,3,4,5,6];
if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
	echo 'USAGE: message.php?pid= &msg=<br>
	The page inserts a msg to a specific process id (pid)';
	header ('location: start.php');
	die();
} 

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calls_billing".$pid;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the filename from the URL parameter
// Example URL: http://yourwebsite.com/yourscript.php?sqlfilename=my_queries.sql
if (isset($_REQUEST['fn'])) {
    $sql_file = $_REQUEST['fn'];
} else {
    die("Error: Please provide a SQL file name using the 'fn' parameter.");
}

// Check if the file exists and is a valid .sql file to prevent security risks
$file_info = pathinfo($sql_file);
if (!file_exists($sql_file) || strtolower($file_info['extension']) !== 'sql') {
    die("Error: Invalid or non-existent SQL file!");
}

// Read the SQL file content
$sql_commands = file_get_contents($sql_file);

// Split the commands by semicolon
$commands = explode(';;', $sql_commands);

// Execute each command
foreach ($commands as $command) {
    // Trim whitespace and check if the command is not empty
    $command = trim($command);
    if (!empty($command)) {
        if ($conn->query($command) === TRUE) {
            echo "Command executed successfully: " . htmlspecialchars($command) . "<br>";
        } else {
            echo "Error executing command: " . htmlspecialchars($command) . " - " . $conn->error . "<br>";
        }
    }
}

// Close connection
$conn->close();
?>