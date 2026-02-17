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
    <title>GRID Computing System - Main Menu</title>
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
        .link-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .link-item {
            margin-bottom: 15px;
            width:95%;
        }
        .link-btn {
            display: block;
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .link-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GRID Admin Tools</h1>
        <h2>Process ID: <?php echo $pid;?></h2>
        <h2 style="font-family: 'Calibri Light'">by Gideon Koch</h2>

        <ul class="link-list">
            <?php
            $json_file = 'main_items.json';

            if (file_exists($json_file)) {
                $json_data = file_get_contents($json_file);
                
                $menu_items = json_decode($json_data, true);

                if ($menu_items && isset($menu_items['menu'])) {
                    foreach ($menu_items['menu'] as $item) {
                        $title = htmlspecialchars($item['title']);
                        $url = htmlspecialchars($item['url']);
                        echo "<li class='link-item'>";
                        echo "<a href='{$url}' class='link-btn'>{$title}</a>";
                        echo "</li>";
                    }
                } else {
                    echo "<li class='link-item'><p style='color:red;'>Error: Invalid JSON format.</p></li>";
                }
            } else {
                echo "<li class='link-item'><p style='color:red;'>Error: menu_items.json not found.</p></li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>