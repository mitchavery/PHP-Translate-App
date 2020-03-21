<?php
    echo <<<_END
<html>
<head>
<title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
<body>
    <div class="headertop">
        <h1>CS 174 Final</h1>
    </div>
    <div class="loginbox">
    <img src="avatar.png" class="avatar">
        <h1>Login</h1>
        <form action="" method="POST" enctype="multipart/form-data" >
            <p>Username</p>
            <input type="text" name="user" placeholder="Enter Username">
            <p>Password</p>
            <input type="password" name="password" placeholder="Enter Password">
            <input type="submit"  value="LOGIN">
            <a href="newuser.php">Don't have an account?</a>
        </form>
    </div>
</body>
</head>
</html>
_END;

function executeFile() {
    require_once('login.php');
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die("Connection Error");
    if (isset($_POST['user']) && isset($_POST['password']))
    {
        $user = mysql_entities_fix_string($connection, $_POST['user']);
        $password = mysql_entities_fix_string($connection, $_POST['password']);
        $query = "SELECT * FROM users WHERE username='$user'";
	    $result = $connection->query($query);
        if (!$result) die();
		elseif ($result->num_rows) 
		{
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();
            $salt1 = $row[4];
            $salt2 = $row[5];
            $token = hash('ripemd128', "$salt1$password$salt2");
			if ($token == $row[3]) {
                session_start();
				$_SESSION['username'] = $user;
				$_SESSION['password'] = $password;
				$_SESSION['forename'] = $row[0];
                $_SESSION['surname'] = $row[1];
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
				echo "$row[0] $row[1] : Hi $row[0], you are now logged in as '$row[2]'";
				die(header('location:continue.php'));
            }
			else die("<h3 align=center>Invalid username/password combination</h3>");
		}
		else die("<h3 align=center>Invalid username/password combination</h3>");
    } else {
        echo "<h3 align=center>Please enter both values</h3> ";
    }
    $connection->close();
}


function mysql_fix_string($conn, $string) 
{
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return ($conn->real_escape_string($string));
}

function mysql_entities_fix_string($conn, $string)
{
	return htmlentities(mysql_fix_string($conn, $string));
}


executeFile();