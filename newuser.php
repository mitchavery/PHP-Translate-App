<?php
echo <<<_END
<html>
    <head>
    <title>New User</title>
        <link rel="stylesheet" type="text/css" href="style2.css">
        <script>
        function showUsernameError()
        {
            var x = document.getElementById("signup-username");
            if(x.style.display === "none"){
                x.style.display = "block";
            }else{
                x.style.display = "none";
            }
            document.write("Error");
        }
        function showEmailError()
        {
            var x = document.getElementById("signup-email");
            if(x.style.display === "none"){
                x.style.display = "block";
            }else{
                x.style.display = "none";
            }
        }     
        function validate(form)
        {
            fail = validateUsername(form.username.value);
            fail += validatePassword(form.password.value);
            if (fail == "") return true
            else { alert(fail); return false}
        }
        function validateEmail(field)
        {
            if(field == "") return " JavaScript: No Email was entered. \n"
            else if(!((field.indexOf(".")>0) && (field.indexOf("@")>0)) || /[^a-zA-Z0-9.@_-]/.test(field))
                return "JavaScript: The Email address is invalid. \n"
            return ""
        }
        function validateUsername(field)
        {
            if (field == "")
                return "No username was entered. \n"
            else if(field.length<5)
                return "Javascript: Username must be at least 5 characters. \n"
            else if(/[^a-zA-Z0-9_-]/.test(field))
                return "Javascript: Only a-z, A-Z, 0-9, - and _ allowed in Username"
            return ""
        }
        function validatePassword(field)
        {
            if (field == "") 
                return "No Password was entered. \n";
            else if(field.length<6)
                return "Passwords must be at least 6 characters.\n"
            else if(!/[a-z]/.test(field) || ! /[A-Z]/.test(field) ||
                    !/[0-9]/.test(field))
                return "Password require one each of a-z, A-Z and 0-9. \n"
            return ""
        }
    </script>
    </head>
    </head>
    <body>
    <div class="headertop">
        <h1>CS 174 Final</h1>
    </div>
        <div class="loginbox">
                <form action="" method="POST" onsubmit="return validate(this)" enctype="multipart/form-data" >
                    <h1>Create Account</h1>
                    <p>First Name</p>
                    <input type="text" name="firstname" placeholder="First Name" />
                    <p>Last Name</p>
                    <input type="text" name="lastname" placeholder="Last Name" />
                    <p>Email</p>
                    <input type="email" name="email" placeholder="Email" />
                    <p>Age</p>
                    <input type="number" name="age" placeholder="Age" />
                    <p>Username</p>
                    <input type="text" name="user" placeholder="Username" />
                    <p>Password</p>
                    <input type="password" name="password" placeholder="Password" />
                    <input type="submit"  value="Create New User">
                    <a href="home.php">Login</a>
                </form>
        </div>
    </body>
    </html>
_END;
$username_valid = TRUE;
$email_valid = TRUE;

function executeFile()
{
    require_once('login.php');
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Connection Error");
    $salt1 = randomSalt();
    $salt2 = randomSalt();
    if (isset($_POST['user']) && isset($_POST['password']) && isset($_POST['firstname']) && isset($_POST['lastname'])) {
        $user = mysql_entities_fix_string($conn, $_POST['user']);
        $password = mysql_entities_fix_string($conn, $_POST['password']);
        $firstname = mysql_entities_fix_string($conn, $_POST['firstname']);
        $lastname = mysql_entities_fix_string($conn, $_POST['lastname']);
        $email = mysql_entities_fix_string($conn, $_POST['email']);
        $age = mysql_entities_fix_string($conn, $_POST['age']);
        $email_message = validate_email($email);
        $fail = $email_message;
        $username_message = validate_username($user);
        $fail .= $username_message;
        $password_message = validate_password($password);
        $fail .= $password_message;
        if($fail == "")
        {
            $query = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($query);
            $query2 = "SELECT * FROM users WHERE email='$email'";
            $result2 = $conn->query($query2);
            if($result -> num_rows > 0) //Username already exists
            { 
                $username_valid = FALSE;
            }
            else if($result2 -> num_rows > 0) //Email already exists
            {
                $email_valid = FALSE;
            }
            else
            {
                $token = hash('ripemd128', "$salt1$password$salt2");
                $result = add_user($conn, $firstname, $lastname, $user, $token, $password, $salt1, $salt2, $email, $age);
            }
        }
    }
    $conn->close();
}

function randomSalt($length=4)
{
    return substr(str_shuffle(
        "qwerasdfasldjflasjldfkjakskdltyuiopasdfgh*&#^%!&#&!^!&#*!jklzxcvbnm"
    ), 0, $length);
}

function add_user($connection, $fn, $sn, $un, $pw, $realpassowrd, $salt1, $salt2, $email, $age)
{
    $query = "INSERT INTO users VALUES('$fn', '$sn', '$un', '$pw', '$salt1', '$salt2', '$email', '$age')";
    $result = $connection->query($query);
    if (!$result) die("<h3 align=center>Connection Error</h3>");
    else echo "<h2 align=center> User created successfully<br><br>Username:  . $un . <br>Password: . $realpassowrd </h2> ";
    return $result;
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

//Server side validation
function validate_email($field)
{
   if(!((strpos($field, ".")>0) && (strpos($field, "@")>0)) || preg_match("/[^a-zA-Z0-9.@_-]/", $field))
            return "The Email address is invalid. <br>";
    return "";
}
function validate_username($field)
{
    if(strlen($field) < 5)
        return "Usernames must be at least 5 characters <br>";
    else if(preg_match("/[^a-zA-Z0-9_-]/", $field))
        return "Only letters, numbers, - and _ allowed in Username <br>";
    return "";
}
function validate_password($field)
{
    if (strlen($field) < 6)
        return "Passwords must be at least 6 characters <br>";
    else if(!preg_match("/[a-z]/", $field)||
            !preg_match("/[A-Z]/", $field)|| 
            !preg_match("/[0-9]/", $field))
        return "Passwords require 1 each of a-z, A-Z, and 0-9 <br>";
    return "";
}

if(!$username_valid)
{
   echo '<script type="text/javascript">',
     'showUsernameError();',
     '</script>';
}
if(!$email_valid)
{
    echo '<script type="text/javascript">',
     'showEmailError();',
     '</script>';
} 

executeFile();
