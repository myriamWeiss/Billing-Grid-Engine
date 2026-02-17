<?php
	session_start();
	$pid1 = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
	$pidArray = [1,2,3,4,5,6];
	if (($pid1 != "") and !(in_array((int)$pid1 , $pidArray))) {
		header ('location: start.php');
		die();
	}
	// $pid1 the directory

// Initialize a status message array
$messages = [];
$uploaded_count = 0;
$pid = null;

// --- 1. Process the File Upload ---

// Check if a PID was submitted
if (!isset($_POST['pid']) || empty($_POST['pid'])) {
    $messages[] = ['type' => 'error', 'text' => 'Error: Process ID (PID) is required.'];
} else {
    // Sanitize the PID
    $pid = basename($_POST['pid']);
    $_SESSION['pid'] = $pid;
    
    $base_dir = __DIR__ . "/team-$pid/";
    $upload_dir = $base_dir; //  . $pid . "/";

    // Check if files were uploaded
    if (empty($_FILES['files']['name'][0])) {
        $messages[] = ['type' => 'error', 'text' => 'Error: No files were selected for upload.'];
    } else {
        // Create the directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            if (mkdir($upload_dir, 0777, true)) {
                $messages[] = ['type' => 'success', 'text' => "Directory for PID **{$pid}** created successfully."];
            } else {
                $messages[] = ['type' => 'error', 'text' => 'Error: Failed to create the target directory.'];
            }
        }

        // Loop through each uploaded file
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['files']['name'][$key];
            $safe_file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', basename($file_name));
            $target_file = $upload_dir . $safe_file_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $messages[] = ['type' => 'success', 'text' => "The file **" . htmlspecialchars($safe_file_name) . "** has been uploaded."];
                $uploaded_count++;
            } else {
                $messages[] = ['type' => 'error', 'text' => "Sorry, there was an error uploading the file **" . htmlspecialchars($safe_file_name) . "**."];
            }
        }
    }
}

// --- 2. Display the Results with HTML and CSS ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Results</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            text-align: left;
        }
        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .success {
            background-color: #e6f7ec;
            color: #27ae60;
            border: 1px solid #d4f0df;
        }
        .error {
            background-color: #fcebeb;
            color: #c0392b;
            border: 1px solid #f5d4d4;
        }
        .summary {
            font-size: 16px;
            font-weight: bold;
            color: #34495e;
            margin-top: 20px;
        }
        .return-btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }
        .return-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
         <h1>GRID Computing System</h1>
        <h2>by Gideon Koch</h2>
        <h2>File Upload Results</h2>
        <ul class="message-list">
            <?php foreach ($messages as $message): ?>
                <li class="message <?php echo $message['type']; ?>">
                    <?php echo $message['text']; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($uploaded_count > 0): ?>
            <p class="summary">
                ✅ **Summary:** <?php echo $uploaded_count; ?> file(s) uploaded successfully for PID: <?php echo htmlspecialchars($pid); ?>
            </p>
        <?php endif; ?>

        <a href="files_load.php" class="return-btn">Return to Upload Form</a>
    </div>
</body>
</html>