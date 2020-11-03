<!DOCTYPE html>
<?php
include 'nav.php';

if(isset($_GET["l"])) {
  $conn->query("INSERT INTO words (conlang_id) VALUES (" . $_GET["l"] . ")");
  $id = $conn->insert_id;
  header("Location: wordedit.php?w=" . $id);
}

$words = $conn->query("SELECT * FROM words WHERE id=" . $_GET["w"]);
$word = $words->fetch_assoc();

$conlangs = $conn->query("SELECT * FROM conlangs WHERE id=" . $word["conlang_id"]);
$conlang = $conlangs->fetch_assoc();

if(!(checkUserPerms($conn, $conlang["id"]))) {
  print("You do not have access to this page.");
  die();
}
?>

<head>
  <link rel="stylesheet" href="css/table.css">
  <script src="js/wordedit.js"></script>
  <style>
    .page p {
      margin-bottom: 5px;
    }

    .page select {
      background-color: #EEE;
      padding: 5px;
    }

    .page textarea {
      outline: none;
      border: none;
      background-color: #EEE;
      font-family: inherit;
      padding: 5px;
      width: 100%;
    }

    .page input {
      width: 100%;
    }

    .page select {
      width: 100%;
    }

    .page ul {
      padding-right: 40px;
    }
  </style>
</head>
<body>
  <div class="page">
    <div class="wrapper">
      <div class="pageHeader">
        <b>LANGUAGE's Dictionary</b>
      </div>
      <div class="section" >
        <form method="post" id="wordInfo">
          <div class="span">
            <div class="spand" style="width: 150%;">
              <label for="name"><p>Word in LANGUAGE</p></label>
              <input name="name" type="text" value="<?php print $word["name"]; ?>" style="font-family: <?php print "f" . $conlang['script_id'];?>;" onfocusout="save()"/>
              <label for="pronunciation"><p>Pronunciation (IPA)</p></label>
              <input name="pronunciation" type="text" value="<?php print $word["pronunciation"]; ?>"  onfocusout="save()"/>
            </div>
            <div style="width: 100%;"></div>
            <div class="spand" style="width: 150%;">
              <label for="name_romanised"><p>Romanisation</p></label>
              <input name="name_romanised" type="text" value="<?php print $word["name_romanised"]; ?>"  onfocusout="save()"/>
            </div>
          </div>
        </form>

        <ul id="addMeaning">
          <a onclick="addMeaning()" style="text-align: left;">Add a Meaning</a>
        </ul>

        <p>
          This page automatically saves data.
        </p>
      </div>
    </div>
  </div>
  <script>loadScript(<?php print $conlang["script_id"] ?>)</script>
</body>
