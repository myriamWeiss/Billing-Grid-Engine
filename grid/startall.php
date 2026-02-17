<?php
header('Access-Control-Allow-Origin: *');
// lanching: Paralel Process found in paralel_process.txt
session_start();
$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
$pidArray = [1,2,3,4,5,6];
if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
	echo 'USAGE: startasll.php?fn=start.txt';
	header ('location: start.php');
	die();
}

$file_content = file_get_contents("Team-$pid/startall_process.txt");

if ($file_content === false) {
	echo "File: paralel_process.txt is missing.";
	die();
}
$process = explode("\n", $file_content);

// Clean up each URL by removing carriage returns and any extra whitespace
$process = array_map(function($url) {
    return trim($url);
}, $process);

$js_array = json_encode($process);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simultaneous Fetch Process</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color:#F4F7F9; }
        textarea { width: 90%; height: 400px; margin-top: 10px; border-radius:10px; line-height:24px; padding:15px; }
    </style>
</head>
<body>
    <div align="center">
        <h1>GRID Computing System</h1>
        <h2>Paralele Processing for PID:<?php echo $pid; ?> </h2>
        <h2>By Gideon Koch</h2>
        <p>Performing process: Team-<?php echo $pid;?></p>
        <textarea id="logArea" readonly></textarea>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logArea = document.getElementById('logArea');

            // Array of URLs to process.
            const process_list = <?php echo $js_array;?>;
            
            // Log the process list to the console for debugging
//            console.log('URLs to fetch:', process_list);

            // Helper function to append messages to the log area
            const log = (message) => {
                logArea.value += message + '\n';
                logArea.scrollTop = logArea.scrollHeight;
            };

            // Main function to perform simultaneous fetches
            const fetchSimultaneously = async () => {
                logArea.value = '';
                log('🚀 Starting simultaneous fetch process...');

                const fetchPromises = process_list.map(url => {
                    log(`▶️ Call for: ${url}`);
                    return fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP status ${response.status}`);
                            }
                            log(`✅ Completion for: ${url}`);
                            return response;
                        })
                        .catch(error => {
                            log(`❌ Failed for: ${url} - Error: ${error.message}`);
                            return null;
                        });
                });

                try {
                    await Promise.all(fetchPromises);
                    log('🎉 All fetches have been processed!');
                } catch (error) {
                    log('An unexpected error occurred during the process.');
                }
            };

            fetchSimultaneously();
        });
    </script>
</body>
</html>