<?php
// run_sql_file.php
// &#1502;&#1512;&#1497;&#1509; &#1514;&#1502;&#1497;&#1491; &#1488;&#1514; &#1492;&#1511;&#1493;&#1489;&#1509; give_a_name.sql &#1506;&#1500; &#1489;&#1505;&#1497;&#1505; &#1492;&#1504;&#1514;&#1493;&#1504;&#1497;&#1501;

$servername = "localhost";
$username   = "root";
$password   = "";
$sql_file   = "create_db_to_grid.sql"; 

  // &#1511;&#1493;&#1489;&#1509; SQL &#1511;&#1489;&#1493;&#1506;

// &#1495;&#1497;&#1489;&#1493;&#1512; &#1500;&#1502;&#1505;&#1491; &#1504;&#1514;&#1493;&#1504;&#1497;&#1501;
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// &#1511;&#1512;&#1497;&#1488;&#1492; &#1513;&#1500; &#1492;&#1511;&#1493;&#1489;&#1509;
$sql = file_get_contents($sql_file);
if ($sql === false) {
    die("Could not read SQL file: $sql_file");
}

// &#1492;&#1512;&#1510;&#1492; &#1513;&#1500; &#1492;&#1508;&#1511;&#1493;&#1491;&#1493;&#1514;
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "SQL file $sql_file executed successfully.";
} else {
    echo "Error executing SQL: " . $conn->error;
}

$conn->close();
?>
