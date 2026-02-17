<?php
session_start();
$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
$pidArray = [1,2,3,4,5,6];
if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
	header ('location: start.php');
	die();
} 

if (!(isset($_REQUEST['fileurl']))) {
	echo "get_file.php?fileurl= &filelocal=";
	die();
}

$urlfile = $_REQUEST['fileurl'];
$localfile = $_REQUEST['filelocal'];

//echo $urlfile."<br>";
//echo $localfile."<br>";

// read the file from any web page or server
$fdata = file_get_contents($urlfile,$localfile);

// Check if the file read was successful
if ($fdata === FALSE) {
    // If it failed, you can inspect the HTTP response headers
    if (isset($http_response_header)) {
        echo "Error: Failed to retrieve file." . PHP_EOL;
        echo "Response Headers:" . PHP_EOL;
        print_r($http_response_header);
    } else {
        echo "Error: An unknown error occurred." . PHP_EOL;
    }
} else {
	file_put_contents($localfile,$fdata);
}
?>
