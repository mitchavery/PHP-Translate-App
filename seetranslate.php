<?php
echo <<<_END
<html>
    <head>
    <title>See Model</title>
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
        <h1 class="page-title">Translation Model</h1>
        <ul id="services">
          <li>
          <h2>View Translation Model</h2>
          </li>
        </ul>
      </article>
    </html>
_END;


function executeFile()
{
  require_once('login.php');
  $conn = new mysqli($hn, $un, $pw, $db);
  if ($conn->connect_error) die($conn->connect_error);
  $query = "SELECT * from translation_model";
  $result = $conn->query($query);
  if (!$result) die($conn->error);
  $rows = $result->num_rows;
  echo '<table align="left"
    cellspacing="5" cellpadding="8">
    <tr><td align="left"><b>English</b></td>
    <td align="left"><b>Translation</b></td></tr>';
  for ($j = 0; $j < $rows; ++$j) {
    $result->data_seek($j);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    echo '<tr><td align="left">' .
      $row['input_word'] . '</td><td align="left">' .
      $row['translated_word'] . '</td><td align="left">' . '</td><td align="left">';
  }
  echo '</tr>';
  echo '</table>';
  $result->close();
  $conn->close();
}



executeFile();
