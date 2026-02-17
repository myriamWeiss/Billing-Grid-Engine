<?php
// Version 1.02
// Imports a table from json file
// The database must exist!
// use create to drop first or append to continue adding records
session_start();

if (!(isset($_REQUEST['db']))) {
	echo 'USAGE: import_json_to_table.php?db=&jsonfile=&method=create[append]<br> 
	The page reads the JSON file created using export_table_to_json.php into the database (db) into the table, using the<br>method: <br> 
	The database must exist!<br>
	1. "create" to recreate the table if it exists<br> 
	2. "append" - add records if the table exists.<br> '; 
	die();
}
$dbname  = $_REQUEST['db'];
$tablename = $_REQUEST['table'];	// the target table name
$json_file = $_REQUEST['jsonfile'];
$method = (isset($_REQUEST['method']) ? $_REQUEST['method'] : "append");

$servername = "localhost";
$username = "root";
$password = "";
//$dbname = "calls_billing";
//$tablename = "price_plan";
//$json_file = "price_plan.json";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the JSON file
$json_data = file_get_contents($json_file);
$import_data = json_decode($json_data, true);

if ($import_data === null) {
    die("Error decoding JSON file.");
}

$create_sql = $import_data['create_sql'];
$table_data = $import_data['data'];

if ($method == "create")
{	
    // Drop the table if it exists
    $conn->query("DROP TABLE IF EXISTS `" .$tablename. "`"); // drop existing
    $conn->query($create_sql); // create the table
} 
else
{	
    // append
    // check if the table exists
    $result = $conn->query('SHOW TABLES LIKE  "'.$tablename.'%"');
    if ($result->num_rows === 0)	// dose not exist
    {   
        if (!$conn->query($create_sql))		// 	If the table does not exist, create it
        {
            echo "Error creating table: " . $conn->error . "<br>";
            $conn->close();
            exit();
        }
    }
}

// Prepare the INSERT statement
if (!empty($table_data)) {
    $first_row = reset($table_data);
    $columns = array_keys($first_row);
    $column_list = "`" . implode("`, `", $columns) . "`";
    $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    $types = str_repeat("s", count($columns)); // Assuming all are strings for simplicity

    $stmt = $conn->prepare("INSERT INTO `" . $tablename . "` (" . $column_list . ") VALUES (" . $placeholders . ")");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    foreach ($table_data as $row) {
        // Create an array of references to the values
        $params = array_values($row);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }
    echo count($table_data) . " rows inserted successfully.";
} else {
    echo "No data to insert.";
}

$conn->close();
?>