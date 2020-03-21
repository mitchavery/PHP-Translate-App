<?php   // continue.php
echo <<<_END
<html>
<head>
<title>Landing</title>
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
    <h1 class="page-title">What do you want to do?</h1>
    <ul id="services">
      <li>
        <h3>Upload File</h3>
        <p>Click here to upload translation model: <a href="uploadtranslate.php">Upload File</a></p>
      </li>
      <li>
        <h3>View Model</h3>
        <p>Click here to view translation model: <a href="seetranslate.php">View Translation Model</a></p>
      </li>
      <li>
        <h3>Translate</h3>
        <p>Click here to translate: <a href="translate.php">Translate</a></p>
      </li>
    </ul>
  </article>
</html>
_END;
session_start();
if (isset($_SESSION['username'])) {
  if (($_SESSION['ip'] === $_SERVER['REMOTE_ADDR']) && ($_SESSION['ua'] === $_SERVER['HTTP_USER_AGENT'])) {
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $forename = $_SESSION['forename'];
    $surname = $_SESSION['surname'];

    destroy_session_and_data();

    echo "Welcome back $forename.<br> 
            Your full name is $forename $surname.<br> 
            Your username is '$username' and your password is '$password'.";
  } else different_user();
} else echo "Please <a href='home.php'>click here</a> to log in.";

function destroy_session_and_data()
{
  $_SESSION = array();
  setcookie(session_name(), '', time() - 2592000, '/');
  session_destroy();
}

function different_user()
{
  destroy_session_and_data();
  echo "Please login again";
  header('location:home.php');
}
