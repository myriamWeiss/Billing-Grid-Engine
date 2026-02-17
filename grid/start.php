<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afeka GRDI computing course</title>
    <style>
        :root {
            --primary-color: #007BFF;
            --secondary-color: #5d9cec;
            --background-color: #f4f7f6;
            --text-color: #333;
            --accent-color: #28a745;
        }

        body {
            font-family: Calibri Light;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            margin: 0;
            overflow: hidden;
            text-align: center;
            position: relative;
        }

        .container {
            position: relative;
            z-index: 10;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            width: 90%;
            transition: transform 0.3s ease-in-out;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        h2 {
            color: var(--text-color);
            font-weight: 400;
            font-size: 1.2rem;
            margin-top: 0;
            opacity: 0.7;
        }

        .button {
            display: inline-block;
            padding: 1rem 2rem;
            margin-top: 2rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            background-color: var(--accent-color);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }

        .button:hover {
            background-color: #218838;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
        }

        .button:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }

        /* Animation Background */
        .animation-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
</style>
<script>
function isNumeric(value) {
	const numericRegex = /^\d+$/;
	return numericRegex.test(value);
}
function startProcess()
{
	let v1 = document.getElementById("Process-ID").value;
	let v2 = document.getElementById("Password-ID").value;
	if ((v1 != "") && (isNumeric(v1)) &&(v2 != "") )
		startsession.submit();
	else 
		alert("Your Password or Process ID is missing.");		
}
</script>
</head>
<body>
    <form name="startsession" method="post" action="startsession.php">
   <h1>Afeka GRID Computing System</h1><br>
    <div class="container">
    <div style="font-size:24px">
    <span>Admin Password</span>&nbsp;&nbsp; <input  style="font-size:24px; width:200px; padding-left:5px" id="Password-ID" name="Password-ID" placeholder="Course password" value="">
    <br><br>
    <span>Your Process ID</span>&nbsp;&nbsp; <input  style="font-size:24px; width:24px; padding-left:5px" id="Process-ID" name="Process-ID" value="Z">
    </div><br>
             <h2>Gideon Koch 2025</h2>
        <a href="#" class="button" onclick="startProcess()">Start performing GRID Process</a>
    </div>
    </form>

</body>
</html>