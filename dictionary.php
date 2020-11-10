<!DOCTYPE html>
<?php
include 'nav.php';

if(!(isset($_GET["l"]))) {
  if(isset($_SESSION["l"])) {
    header("Location: dictionary.php?l=" . $_SESSION["l"]);
  } else {
    header("Location: index.php");
  }
} else {
  $_SESSION["l"] = $_GET["l"];
}




?>

<head>
  <link rel="stylesheet" href="css/table.css">
  <script src="js/dictionary.js"></script>
</head>
<body>
  <div class="page">
    <div class="wrapper">
      <div class="pageHeader">
        <b>
          <?php
          $conlangs = $conn->query("SELECT * FROM conlangs WHERE id=" . $_GET["l"]);
          $conlang = $conlangs->fetch_assoc();
          if($conlang["name_romanised"] == "") {
            print $conlang["name"] . "'s Dictionary";
          } else {
            print $conlang['name_romanised'] . "'s Dictionary";
          }


          ?>
        </b>
          <?php
          if(checkUserPerms($conn, $conlang["id"])) {
            print "<a style=\"float: right;\" href=\"wordedit.php?l=" . $conlang['id'] . "\">Add Word</a>";
          }
          ?>
          <script>script = "f<?php print $conlang["script_id"] ?>";
          loadScript(<?php print $conlang["script_id"] ?>);
          </script>
      </div>
        <table>
          <tbody>
            <tr class="tableHeading">
              <th>
                Word
              </th>
              <th>
                Romanisation
              </th>
              <th>
                Word Class
              </th>
              <th>
                Meaning
              </th>
              <?php
              if(isset($_SESSION["uid"])) {
                print "<th style=\"width: auto; display: flex; opacity: 0; flex-grow: 0;\"><a>Edit</a><a>Delete</a></th>";
              }
              ?>
            </tr>
          </tbody>
          <tbody id="table">
            <!-- hey javascript put the data in here -->
          </tbody>
        </table>
    </div>
  </div>
</body>
