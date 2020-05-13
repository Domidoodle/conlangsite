<!DOCTYPE html>
<?php
if (is_null($_GET["l"])) {
  header("Location: index.php");
}

include 'nav.php';
include 'setup.php';
?>

<head>
  <link rel="stylesheet" href="css/table.css">
</head>
<body>
  <div class="page">
    <div class="wrapper">
      <div class="pageHeader">
        <b>
          <?php
          $conlangs = $conn->query("SELECT * FROM conlangs WHERE id=" . $_GET["l"]);
          $conlang = $conlangs->fetch_assoc();
          print $conlang['name_romanised'] . "'s Dictionary";
          ?>
        </b>
      </div>

      <table>
        <?php
        $words = $conn->query("SELECT * FROM words WHERE conlang_id=" . $_GET["l"]);

        if ($words->num_rows > 0) {
            // output data of each row
            while($word = $words->fetch_assoc()) {

              $meanings = $conn->query("SELECT * FROM meanings WHERE word_id=" . $word["id"]);
              $pos = "";
              $english = "";
              $meaning = $meanings->fetch_assoc();
              $pos = $meaning["pos"];
              $english = $meaning["english"];

              print "<tr>
                      <th><a  href=\"word.php?w=" . $word["id"] . "\" style=\"font-family:" . $conlang["script"] . "\"><b>" . $word["name"] . "</b></a></th>
                      <th>" . $word["name_romanised"] . "</th>
                      <th>" . $pos . "</th>
                      <th>" . $english . "</th>
                    </tr>";


            }

        } else {
            echo "<p style=\"color: red;\">ERROR: No words found</p>";
        }
        ?>
      </table>
    </div>
  </div>
</body>