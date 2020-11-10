<!DOCTYPE html>
<?php
include 'nav.php';

if(isset($_GET["l"])) {
  $conlangs = $conn->query("SELECT conlangs.id, conlangs.name, conlangs.pronunciation, conlangs.name_romanised, conlangs.script_id, scripts.name AS script_name FROM conlangs LEFT JOIN scripts ON conlangs.script_id=scripts.id WHERE conlangs.id=" . $_GET["l"]);
  $conlang = $conlangs->fetch_assoc();

  if(!(checkUserPerms($conn, $conlang["id"]))) {
    print("You do not have access to this page.");
    die();
  }
}
?>

<head>
  <link rel="stylesheet" href="css/table.css">
  <script src="js/languageedit.js"></script>
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
        <form method="post" id="languageInfo">
          <div class="span">
            <div class="spand" style="width: 150%;">
              <label for="name"><p>Name in Language</p></label>
              <input id="named" name="name" type="text" value="<?php if(isset($_GET["l"])) { print $conlang["name"]; } ?>" style="font-family: <?php print "f" . $conlang['script_id'];?>;" onfocusout="save()"/>
              <label for="pronunciation"><p>Pronunciation (IPA)</p></label>
              <input id="pronunciation" name="pronunciation" type="text" value="<?php if(isset($_GET["l"])) { print $conlang["pronunciation"]; } ?>"  onfocusout="save()"/>
              <?php if(!(isset($_GET["l"]))) { ?>
              <a onclick="addLanguage()"><br />Create Language</a>
              <?php } ?>
              <?php if((isset($_GET["l"]))) { ?>
              <?php } ?>
            </div>
            <div style="width: 100%;"></div>
            <div class="spand" style="width: 150%;">
              <label for="name_romanised"><p>Romanisation</p></label>
              <input id="name_romanised" name="name_romanised" type="text" value="<?php if(isset($_GET["l"])) { print $conlang["name_romanised"]; } ?>"  onfocusout="save()"/>
              <label for="script_id"><p>Script ID</p></label>
              <input id="script_id" name="script_id" type="text" value="<?php if(isset($_GET["l"])) { print $conlang["script_id"]; } ?>"  onfocusout="save()"/>
              <div style="opacity: 0.75;">
                <?php if(isset($_GET["l"]) and isset($conlang["script_id"])) { ?>
                Currently using<a><?php print $conlang["script_name"]; ?></a>as the default script for this language.
                <?php } ?>
              </div>
            </div>
          </div>
          <a onclick="deleteLanguage()"><br />DELETE Language</a>
        </form>
        <p>
          This page automatically saves data.
        </p>
      </div>
    </div>
  </div>
</body>
