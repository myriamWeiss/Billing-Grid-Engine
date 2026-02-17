<?php
	session_start();
	$pid = (isset($_SESSION['pid']) ? $_SESSION['pid'] : "" );
	$pidArray = [1,2,3,4,5,6];
	if (($pid != "") and !(in_array((int)$pid , $pidArray))) {
		header ('location: start.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Browser</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
        .current-path {
            font-size: 16px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            word-wrap: break-word;
        }
        .file-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .list-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }
        .list-item:hover {
            background-color: #f8f9fa;
        }
        .icon {
            margin-right: 15px;
            font-size: 1.2em;
        }
        .item-name {
            flex-grow: 1;
            font-size: 16px;
            text-decoration: none;
            color: #333;
        }
        .item-name.dir-link {
            font-weight: bold;
            color: #3498db;
        }
        .item-name.dir-link:hover {
            text-decoration: underline;
        }
        .file-size {
            font-size: 14px;
            color: #777;
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
        .btn p {
		border:1px #888 solid;
		border-radius:20px;
		background-color:#EEE;
		text-align:center;
		height:40px;
		font-size:24px;
		cursor:pointer;
	}
</style>
</head>
<body>
    <div class="container">
    
        <table style="width: 100%">
			<tr>
				<td>&nbsp;</td>
				<td><h1>Directory Explorer</h1></td>
				<td class="btn" onclick="location.href='../grid/main.php'"><p>Admin Tools</p></td>
			</tr>
	</table>
    
        
        <?php
        $base_dir = __DIR__;
        $current_dir = $base_dir;

        // Handle navigation request
        if (isset($_GET['dir'])) {
            $requested_dir = $_GET['dir'];
            $new_path = realpath($base_dir . '/' . $requested_dir);
            
            // Check if the new path is valid and within the base directory to prevent directory traversal
            if ($new_path && strpos($new_path, $base_dir) === 0) {
                $current_dir = $new_path;
            }
        }
        
        $relative_path = substr($current_dir, strlen($base_dir));
        $relative_path = ($relative_path === false) ? '/' : $relative_path;

        echo '<div class="current-path"><strong>Current Directory:</strong> ' . htmlspecialchars($relative_path) . '</div>';
        
        $files = scandir($current_dir);
        
        echo '<ul class="file-list">';
        
        // "Up" button to go to the parent directory
        if ($current_dir != $base_dir) {
            $parent_dir = dirname($relative_path);
            echo '<li class="list-item">';
            echo '<span class="icon">file</span>';
            echo '<a href="?dir=' . urlencode($parent_dir) . '" class="item-name dir-link">.. (Parent Directory)</a>';
            echo '</li>';
        }
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $full_path = $current_dir . '/' . $file;
            $relative_file_path = $relative_path . '/' . $file;
            
            if (is_dir($full_path)) {
                echo '<li class="list-item">';
                echo '<span class="icon">Dir: </span>';
                echo '<a href="?dir=' . urlencode($relative_file_path) . '" class="item-name dir-link">' . htmlspecialchars($file) . '</a>';
                echo '</li>';
            } else {
                echo '<li class="list-item">';
                echo '<span class="icon">File: </span>';
                echo '<span class="item-name">' . htmlspecialchars($file) . '</span>';
                echo '<span class="file-size">' . round(filesize($full_path) / 1024, 2) . ' KB</span>';
                echo '</li>';
            }
        }
        
        echo '</ul>';
        ?>
    </div>
</body>
</html>