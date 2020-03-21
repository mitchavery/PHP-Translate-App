<?php
echo <<<_END
<html>
    <head>
    <title>Translate</title>
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
        <h1 class="page-title">Translate</h1>
        <ul id="services">
          <li>
        <form action="" method="POST" enctype="multipart/form-data" >
            <h2>Input Sentence</h2>
            <input type="text" name="sentence" placeholder="Translate me!">  <br><br>
            <input type="submit" value='Translate'/>
        </form>
        </li>
        </ul>
      </article>
    </div>
</html>
_END;
function executeFile()
{

  require_once('login.php');
  $conn = new mysqli($hn, $un, $pw, $db);
  if ($conn->connect_error) die($conn->connect_error);
  $correct_output = '';
  if (isset($_POST['sentence'])) {
    $sentence = mysql_entities_fix_string($conn, $_POST['sentence']);
    $words = explode(" ", $sentence);
    $query = "SELECT * from translation_model";
    $result = $conn->query($query);
    if (!$result) die("Wrong Translation");
    $rows = $result->num_rows;
    if ($rows != 0) {
      foreach ($words as $word) {
        $query = "SELECT translated_word from translation_model where input_word = '$word'";
        $result = $conn->query($query);
        if (!$result) die("Wrong Translation");
        $rows = $result->num_rows;
        $output_word = '';
        for ($j = 0; $j < $rows; ++$j) {
          $result->data_seek($j);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $output_word = $row['translated_word'];
        }
        $result->close();
        $correct_output = $correct_output . " " . $output_word;
      }
    } else {
      foreach ($words as $word) {
        $correct_output = $correct_output . " " . $word;
      }
    }
  }
  $conn->close();
  echo "<h2 align=center>$correct_output</h2> ";
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