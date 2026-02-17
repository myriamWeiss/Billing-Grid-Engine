<?php
session_start();

$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "");
$pidArray = [1, 2, 3, 4, 5, 6];

if (($pid != "") && !(in_array((int)$pid, $pidArray))) {
    echo 'USAGE: message.php?pid= &msg=<br>
    The page inserts a msg to a specific process id (pid)';
    header('Location: start.php');
    die();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calls_billing" . $pid;

// Create connection to MySQL server
$mysqli = new mysqli($servername, $username, $password);

// Check for connection error
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the database exists
$sql = "SELECT SCHEMA_NAME FROM information_schema.schemata WHERE SCHEMA_NAME = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $dbname);
    $stmt->execute();
    $stmt->store_result();
    
    // If the database doesn't exist, create it. Otherwise, drop and recreate it.
    if ($stmt->num_rows === 0) {
        $mysqli->query("CREATE DATABASE `$dbname`");
    } else {
        $mysqli->query("DROP DATABASE `$dbname`");
        $mysqli->query("CREATE DATABASE `$dbname`");
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $mysqli->error;
    $mysqli->close();
    die();
}

// Close the initial connection to the server
$mysqli->close();

// Now connect to the specific database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection to the database
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

// Get the filename from the URL parameter
if (!isset($_REQUEST['fn'])) {
    die("Error: Please provide a SQL file name using the 'fn' parameter.");
}

$sql_file = "Team-$pid/" . $_REQUEST['fn'];

// Check if the file exists and is a valid .sql file
$file_info = pathinfo($sql_file);
if (!file_exists($sql_file) || (isset($file_info['extension']) && strtolower($file_info['extension']) !== 'sql')) {
    die("Error: Invalid or non-existent SQL file!");
}

// Read the SQL file content
$sql_commands = file_get_contents($sql_file);

// Split the commands by semicolon
$commands = explode(';', $sql_commands);

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
echo "Doen.";
?>