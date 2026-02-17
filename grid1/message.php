<?php
	session_start();
	$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
	$pidArray = [1,2,3,4,5,6];
	if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
		echo 'USAGE: message.php?pid= &msg=<br>
		The page inserts a msg to a specific process id (pid)';
		header ('location: start.php');
		die();
	} 

// message.php?pid=&msg=
$msg = $_REQUEST['msg'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grid";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	echo "Check if you have datbase: grid and table message<br>";
	echo "CREATE TABLE message ( rid int NOT NULL AUTO_INCREMENT,  pid int NOT NULL,  ts int NOT NULL COMMENT 'PHP:microtime(true);',  msg varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,  PRIMARY KEY (rid)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Messages from servers'<br>";
	die("Connection failed: " . $conn->connect_error);
}


$ts = microtime(true);
/* 
hrtime() (High-Resolution Time) function. 
This function returns a nanosecond-level time value, which is useful for benchmarking and measuring very short durations.
1 millisecond (ms) is equal to 1,000 microseconds.
1 microsecond (µs) is equal to 1,000 nanoseconds.
1 nanosecond (ns) is equal to 1,000 picoseconds.

There are 1,000,000 nanoseconds in a millisecond

 $ts = hrtime(true);		// The true argument returns the time as a single float in nanoseconds.
 */
$conn->query("INSERT INTO message (pid, ts, msg) VALUES ($pid,$ts,'$msg')");
$conn->close();
?>
