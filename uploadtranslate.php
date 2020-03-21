<?php
echo <<<_END
<html>
<head>
<title>Upload Translate</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
  <div class="container">
    <div id="branding">
      <h1><span class="highlight">Lame</span> Translate</h1>
    </div>
    <nav>
          <ul>
            <li><a href="home.php">Home</a></li>
            <li class="current"><a href="continue.php">Landing</a></li>
          </ul>
    </nav>
  </div>
</header>
<section id="main">
<div class="container">
  <article id="main-col">
    <h1 class="page-title">Upload Translate</h1>
    <ul id="services">
      <li>
        <h3>Upload File</h3>
        <form action="" method="POST" enctype="multipart/form-data" >
            <input type="file" name="file" /><br><br>
            <input type="submit" value='Submit'  />
    </form>
      </li>
    </ul>
  </article>
</body>
</html>
_END;

function executeFile()
{
  if (isset($_FILES['file'])) {
    extractFile();
  }
}


function extractFile()
{
  $file = $_FILES['file'];
  $file_name = $file['name'];
  $file_name = strtolower(preg_replace("[^A-Za-z0-9.]", "", $file_name));
  $file_tmp = $file['tmp_name'];
  $file_error = $file['error'];
  $file_type = explode('.', $file_name);
  $file_type = strtolower(end($file_type));
  $allowed = array('txt');

  if (in_array($file_type, $allowed)) {
    if ($file_error === 0) {
      $filename_new = 'newfile' . '.' . $file_type;
      $file_destination = $filename_new;
      if (move_uploaded_file($file_tmp, $file_destination)) {
        read2($file_destination);
        echo "File Uploaded Successfully";
      } else {
        echo "file unable to be moved" . "<br>";
      }
    } else {
      echo "Error when uploading file" . "<br>";
    }
  } else {
    echo "File must be of type txt" . "<br>";
  }
}

function read2($filename)
{
  require_once('login.php');
  $conn = new mysqli($hn, $un, $pw, $db);
  if ($conn->connect_error) die("Connection Error");
  $fn = fopen($filename, "r") or die("Unable to open your file");
  while (!feof($fn)) {
    $result = fgets($fn);
    $result = filter_var($result, FILTER_SANITIZE_STRING);  // santizes string
    $result = str_replace("\n", ' ', $result);
    $result = str_replace("\r", '', $result);
    $index = strpos($result, ":");
    $input_word = trim(substr($result, 0, $index));
    $output_word = trim(substr($result, $index + 2, strlen($result)));
    $query = "INSERT into translation_model values('$input_word', '$output_word')";
    $result = $conn->query($query);
    if (!$result) die();
  }
  fclose($fn);
  $conn->close();
}


function get_post($conn, $var)
{
  return $conn->real_escape_string($_POST[$var]);
}

executeFile();
