<?php
// rename_table.php?db=calls_billing1&old=calls1&new=calls

header('Access-Control-Allow-Origin: *');
session_start();

if (!(isset($_REQUEST['db'], $_REQUEST['old'], $_REQUEST['new']))) {
    echo 'USAGE: rename_table.php?db=&old=&new=<br>';
    die();
}

$dbname   = $_REQUEST['db'];
$oldTable = $_REQUEST['old'];
$newTable = $_REQUEST['new'];

$servername = "localhost";
$username   = "root";
$password   = "";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Run RENAME TABLE
$sql = "RENAME TABLE `$oldTable` TO `$newTable`";
if ($conn->query($sql) === TRUE) {
    echo "Table renamed from `$oldTable` to `$newTable` successfully.";
} else {
    echo "Error renaming table: " . $conn->error;
}

$conn->close();
?>
