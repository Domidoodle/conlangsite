<!DOCTYPE html>
<?php
include "nav.php";
?>
<head>
  <script src="js/languages.js"></script>
  <link rel="stylesheet" href="css/table.css">
</head>
<body>
  <div class="page">
    <div class="wrapper">
      <div class="pageHeader">
        <b>Languages on LANGFORGE <!-- <span class="help-text">These are user created languages, click on them to go to their dictionary</span> --></b>
        <?php
        if(isset($_SESSION["uid"])) {
          print "<a style=\"float: right;\" href=\"languageedit.php\">Create Language</a>";
        }
        ?>
      </div>
      <table id="table">
        <tr class="tableHeading">
          <th>
            Name
          </th>
          <th>
            Romanisation
          </th>
          <th>
            Editors
          </th>
          <th style="width: auto; display: flex; opacity: 0; flex-grow: 0;">
            <a>Edit</a>
          </th>
        </tr>
      </table>
    </div>
  </div>
</body>
