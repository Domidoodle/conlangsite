<!DOCTYPE html>
<?php
include 'nav.php';

$words = $conn->query("SELECT * FROM words WHERE id=" . $_GET["w"]);
$word = $words->fetch_assoc();

$conlangs = $conn->query("SELECT * FROM conlangs WHERE id=" . $word["conlang_id"]);
$conlang = $conlangs->fetch_assoc();

?>

<body>
  <div class="page">
    <head>
      <link rel="stylesheet" href="css/table.css">
      <script>
      //delete() word
      //delete word
      function deleteWord() {
        if(confirm("Are you sure you want to delete this word?")) {
          request({
            method: "POST",
            url: "processor.php",
            params: { //params object is made into string, is very nice
              request: "delete",
              w: params.get("w"),
            }
          })
            .then(function(result) {
              window.location.replace(result); //php returns the page to redirect to, the dictionary page for the language
            });
        }
      }

      loadScript(<?php print $conlang["script_id"] ?>);
    </script>
    </head>
    <div class="wrapper">
      <div class="pageHeader">
        <b><?php
        if($conlang["name_romanised"] == "") {
          print $conlang["name"] . "'s Dictionary";
        } else {
          print $conlang['name_romanised'] . "'s Dictionary";
        }
        ?></b>
      </div>
      <div class="section">
        <div class="span">
          <h1 style="flex-grow: 1; font-family: <?php print "f" . $conlang['script_id']; ?>"><?php print $word['name'];?></h1>
          <?php if(checkUserPerms($conn, $conlang["id"])) { ?>
          <a href="wordedit.php?w=<?php print $_GET["w"]?>">edit</a>
          <a onclick="deleteWord()">delete</a>
        <?php } ?>
        </div>
        <h3><?php print $word['name_romanised'];?></h3>
        <h3 style="color: #3C99DC">[<?php print $word['pronunciation'];?>]</h3>

        <?php
        $pos = array("noun", "verb", "adjective", "pronoun", "numeral");  //OI: this order should probably come from somewhere else rather than be built in. Let users define pos for conlang?

        foreach($pos as $class) {
          $meanings = $conn->query("SELECT * FROM meanings WHERE word_id=" . $_GET["w"] . " AND pos='" . $class . "'"); //finds meanings of certain part of speech

          if($meanings->num_rows > 0) { //checks to see if there are any meanings of that class
            print "<h3>" . $class . "</h3>";

            $count = 0;
            $englishList = array();
            while($meaning = $meanings->fetch_assoc()) {
              $count++;

              $englishList[] = $meaning["english"]; //for Synonyms

              print "
              <ul class=\"spand\">
                <div class=\"meaning\">" . $count . ". <span class=\"highlight\">" . $meaning["english"] . "</span>";

              if(strlen($meaning["meaning"]) > 0) {
                print " - " . $meaning["meaning"] . "</div>";
              } else {
                print "</div>";
              }

              if(strlen($meaning["example"]) > 0) { //need example tags?
                print "
                <p class=\"example\">" . $meaning["example"] . "<br />
                " . $meaning["example_english"] . "</p>
                </ul>";
              } else {
                print "</ul>";
              }
            }
          }
        }
        ?>

      </div>
      <div class="section">
        <h2 >Synonyms</h2>
        <ul>
          <?php
          if(isset($englishList)) { //incase word has no meanings
            $englishListFormatted = join("\" OR english=\"", $englishList);
            $englishListFormatted = "english=\"" . $englishListFormatted . "\"";
            $otherWords = $conn->query("SELECT * FROM meanings LEFT JOIN words ON meanings.word_id=words.id WHERE conlang_id=" . $word["conlang_id"] . " AND " . $englishListFormatted . "AND NOT word_id=" . $word["id"]);
            if($otherWords->num_rows > 0) {
              while($otherWord = $otherWords->fetch_assoc()) {
                print("<a href=\"word.php?w={$otherWord["id"]}\" style=\"font-family: f{$conlang["script_id"]}\">{$otherWord["name"]}</a><br />");
              }
            } else {
              print("None");
            }
          } else {
            print("None");
          }
          ?>
        </ul>
        <h2>Homophones</h2>
        <ul>
          <?php
          $otherWords = $conn->query("SELECT * FROM words WHERE pronunciation=\"{$word["pronunciation"]}\" AND NOT id={$word["id"]}");
          if($otherWords->num_rows > 0) {
            while($otherWord = $otherWords->fetch_assoc()) {
              print("<a href=\"word.php?w={$otherWord["id"]}\" style=\"font-family: f{$conlang["script_id"]}\">{$otherWord["name"]}</a><br />");
            }
          } else {
            print("None");
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
</body>
