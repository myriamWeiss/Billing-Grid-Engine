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
    <title>GRID Computing System - File Upload</title>
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
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 5px;
        }
        h2 {
            color: #34495e;
            font-size: 1.5em;
            margin-top: 0;
            margin-bottom: 30px;
        }
        form {
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type=text], input[type=file] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type=file] {
            border: 1px dashed #ccc;
            padding: 15px;
            background-color: #f9f9f9;
            cursor: pointer;
        }
        input[type=submit] {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }
        input[type=submit]:hover {
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
            <h1>GRID Computing System</h1>
        <h2>by Gideon Koch</h2>

            <table style="width: 100%">
			<tr>
				<td>&nbsp;</td>
				<td><h2>Load files to server</h2></td>
				<td class="btn" onclick="location.href='../grid/main.php'"><p>Admin Tools</p></td>
			</tr>
	</table>
      
        <form action="files_upload.php" method="post" enctype="multipart/form-data">
        
     
            <label for="pid">Process ID (PID):</label>
            <input type="text" name="pid" id="pid" required placeholder="Your process ID (PID) 1..6 " value="<?php echo $pid; ?>">
            
            <label for="files">Select files to upload:</label>
            <input type="file" name="files[]" id="files" multiple required>
            
            <input type="submit" value="Upload Files">
        </form>
    </div>
</body>
</html>