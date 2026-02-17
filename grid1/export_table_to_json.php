<?php
// Exports a table to json file
session_start();
$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
$pidArray = [1,2,3,4,5,6];
if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
	echo 'USAGE: message.php?pid= &msg=<br>
	The page inserts a msg to a specific process id (pid)';
	header ('location: start.php');
	die();
} 

if (!(isset($_REQUEST['db']))) {
	echo 'USAGE: export_table_to_json.php?db=&table=&jsonfile=<br> 
	The page opens the database and reads the table, creating a JSON file. '; 
	die();
}

$dbname  = $_REQUEST['db'];
$tablename = $_REQUEST['table'];
$json_file = $_REQUEST['jsonfile'];

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$result_create_table = $conn->query("SHOW CREATE TABLE `" . $tablename . "`");
if ($result_create_table->num_rows > 0) {
    $row = $result_create_table->fetch_assoc();
    $create_table_sql = $row['Create Table'];
} else {
    die("Table not found.");
}

// Get table size
$result_table_size = $conn->query("SELECT table_schema, table_name, round(((data_length + index_length) / 1024), 2) AS `size_kb` FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = '" . $tablename . "'");
$table_size = ($result_table_size->num_rows > 0) ? $result_table_size->fetch_assoc()['size_kb'] : 0;

// Get table data
$result_data = $conn->query("SELECT * FROM `" . $tablename . "`");
$table_data = [];
while ($row = $result_data->fetch_assoc()) {
    $table_data[] = $row;
}

// Create the JSON structure
$export_data = [
    'table_name' => $tablename,		
    'create_sql' => $create_table_sql,
    'size_kb' => $table_size,
    'data' => $table_data
];

// Convert to JSON and save to a file
$json_output = json_encode($export_data, JSON_PRETTY_PRINT);
file_put_contents($json_file, $json_output); 

// When called using AJAX echo teh result
//echo $json_output;
echo "Table structure, size, and data exported to " . $json_file;

$conn->close();
// header("location: https://grid.afeka.com");
?>