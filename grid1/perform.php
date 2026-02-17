<?php
header('Access-Control-Allow-Origin: *');
// lanching: Sequential process found in perform.proc
// 26: const process_list
session_start();
$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
$pidArray = [1,2,3,4,5,6];
if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
	header ('location: start.php');
	die();
} 

if (isset($_REQUEST['fn'])) {
    $fn= $_REQUEST['fn'];
} else {
    die("Error: Please provide a SQL file name using the 'fn' parameter.");
}

$file_content = file_get_contents("Team-$pid/$fn");
// $file_content = file_get_contents($fn);
//var_dump($file_content); 

//$file_content = file_get_contents($fn);

if ($file_content === false) {
	echo "File: $command_lines is missing.";
/*	
	$dateTime = date('Y-m-d_H-i-s');
//	$fileName = "Team-$pid/testfile_" . $dateTime .".txt";
	$fileName = 'testfile_' . $dateTime . '.txt';
	$content = "ERROR"; ;
	file_put_contents($fileName, $content);
*/	
	die(3);
} /* else {
	$dateTime = date('Y-m-d_H-i-s');
//	$fileName = "Team-$pid/testfile_" . $dateTime .".txt";
	$fileName = "Team-$pid/testfile_" . $dateTime . '.txt';
	$content = $file_content ;
	file_put_contents($fileName, $content); // , FILE_APPEND
} 
*/

$process = explode("\n", $file_content);
$js_array = json_encode($process);
//var_dump($js_array ); die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sequential Process</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        textarea { width: 100%; height: 400px; margin-top: 10px; border-radius:10px; line-height:24px; }
    </style>
</head>
<body>
    <h1>Automatic Sequential Process</h1>
    <textarea id="logArea" readonly></textarea>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logArea = document.getElementById('logArea');

/* Array of URLs to process.
            const process_list = [
                'http://localhost/afeka/40246/GRID/master/export_table_to_json.php?db=calls_billing&table=price_plan&jsonfile=temp1_export.json',
                'get_file.php?fileurl=../master/temp1_export.json&filelocal=temp2_export.json',
                // '../server/error_page.php',
            ];
*/            
            const process_list = <?php echo $js_array;?>;

            // Helper function to append messages to the log area
            const log = (message) => {
                logArea.value += message + '\n';
                logArea.scrollTop = logArea.scrollHeight;
            };

            // Main function to perform the sequential fetches
            const fetchSequentially = async () => {
                logArea.value = '';
                log('🚀 Starting automatic sequential fetch process...');

                for (const url of process_list) {
                    log(`▶️ Starting fetch for: ${url}`);
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP status ${response.status}`);
                        }
                       log(`✅ Completion for: ${url}`);
                    } catch (error) {
                        log(`❌ Failed for: ${url} - Error: ${error.message}`);
                    }
                }

               log('🎉 Process complete!');
            };

            // Call the function directly on page load
            fetchSequentially();
        });
    </script>
</body>
</html>