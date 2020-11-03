<?php
include 'setup.php';

//error_reporting(E_ERROR | E_WARNING | E_PARSE);

function getFileType($file) {
  return strtolower(pathinfo($file,PATHINFO_EXTENSION));
}

//getting output from the database
//turns into HTML entities like \&lt;
function cleanHTML($array) {
  foreach ($array as $key => $value) {
    $array[$key] = htmlspecialchars(htmlentities($value));
    return $array;
  }
}

function cleanUserInput($string) {
  return 0;
}

//check if user has right permission to edit conlang
function checkUserPerms($conn, $conlang_id) {
  if(isset($_SESSION["uid"])) {
    $editors = $conn->query("SELECT * from editors WHERE conlang_id=" . $conlang_id);
    $editorsList = array();
    while($editor = $editors->fetch_assoc()) {
      $editorsList[] = $editor["user_id"];
    }
    return in_array($_SESSION["uid"], $editorsList);
  }
}

if(isset($_REQUEST["test"])) {
  echo "TEST!";
}

if(isset($_POST["request"])) {
  if(isset($_POST["l"])) { //chk perms
    $access = checkUserPerms($conn, $_POST["l"]);
  } elseif (isset($_POST["w"])) {
    $words = $conn->query("SELECT words.id, words.conlang_id FROM words WHERE words.id={$_POST["w"]}");
    $word = $words->fetch_assoc();
    $access = checkUserPerms($conn, $word["conlang_id"]);
  } elseif (isset($_POST["m"])) {
    $words = $conn->query("SELECT meanings.id, words.conlang_id FROM words LEFT JOIN meanings ON meanings.word_id=words.id WHERE meanings.id={$_POST["m"]}");
    $word = $words->fetch_assoc();
    $access = checkUserPerms($conn, $word["conlang_id"]);
  } elseif ($_POST["request"] == "addLanguage") {
    $access = True;
  }

  // echo $access;

  if(isset($access) AND $access) {
    //addMeaning()
    if($_POST["request"] == "addMeaning") {
      $conn->query("INSERT INTO meanings (word_id) VALUES (" . $_POST["w"] . ")");
      $id = $conn->insert_id;
      $n = 2;
      print '
      <ul>
      <form method="post" id="' . $id . '">
      <p>Meaning #' . $n . '</p> <a onclick="delMeaning()">Delete</a>
      <label for="pos"><p>Part of Speech</p></label>
      <select name="pos" onchange="saveMeaning()">
      <option selected disabled hidden>

      </option>
      <option value="noun">
      noun
      </option>
      <option value="pronoun">
      pronoun
      </option>
      <option value="verb">
      verb
      </option>
      <option value="adjective">
      adjective
      </option>
      <option value="numeral">
      numeral
      </option>
      </select>
      <label for="english"><p>English</p></label>
      <input name="english" type="text" onfocusout="saveMeaning()"/>
      <label for="meaning"><p>Additional Meaning</p></label>
      <input name="meaning" type="text" onfocusout="saveMeaning()"/>
      <label for="example"><p>Example of Meaning</p></label>
      <textarea name="example" style="height: 8em;" onfocusout="saveMeaning()"></textarea>
      </form>
      </ul>
      ';
    }

    //saveMeaning()
    if($_POST["request"] == "saveMeaning") {
      $keys = array_keys($_POST);
      $conn->query("UPDATE meanings SET " . $_POST["field"] . " = '" . $_POST["value"] . "' WHERE id=" . $_POST["m"]);
      echo "Updated meaning at id \"" . $_POST["m"] . "\", field \"" . $_POST["field"] . "\" with \"" . $_POST["value"] . "\"";
    }

    //delMeaning()
    if($_POST["request"] == "delMeaning") {
      $conn->query("DELETE FROM meanings WHERE id=". $_POST["m"]);
      echo "Deleted Meaning with ID " . $_POST["m"];
    }

    //save() TOP text
    if($_POST["request"] == "save") {
      $conn->query("UPDATE words SET " . $_POST["field"] . " = '" . $_POST["value"] . "' WHERE id=" . $_POST["w"]);
      echo "Updated meaning at id \"" . $_POST["w"] . "\", field \"" . $_POST["field"] . "\" with \"" . $_POST["value"] . "\"";
    }

    //delete() word
    if($_POST["request"] == "delete") {
      $words = $conn->query("SELECT * FROM words WHERE id=" . $_POST["w"]);
      $word = $words->fetch_assoc();
      $conn->query("DELETE FROM meanings WHERE word_id=" . $_POST["w"]);
      $conn->query("DELETE FROM words WHERE id=" . $_POST["w"]);
      echo "dictionary.php?l=" . $word["conlang_id"];
    }

    //need user creating &  name of language
    if($_POST["request"] == "addLanguage") {
      $conn->query("INSERT INTO conlangs (name, name_romanised, pronunciation) VALUES ('" . $_POST["name"] . "', '" . $_POST["name_romanised"] . "', '" . $_POST["pronunciation"] . "')");
      $id = $conn->insert_id;
      echo "{ \"id\":" . $id . " }";
      $conn->query("INSERT INTO editors (user_id, conlang_id) VALUES (" . $_SESSION["uid"] . ", " . $id . ")");
    }

    if($_POST["request"] == "deleteLanguage") {
      //okay so this is complicated because we have to delete all the children first, meanings -> words -> editors (do this last incase anything goes wrong) -> conlangs
      $conn->query("DELETE FROM meanings INNER JOIN words ON meanings.word_id=words.id WHERE conlang_id= ". $_POST["l"]);
      $conn->query("DELETE FROM words WHERE conlang_id= ". $_POST["l"]);
      $conn->query("DELETE FROM editors WHERE conlang_id= ". $_POST["l"]);
      $conn->query("DELETE FROM conlangs WHERE id= ". $_POST["l"]);
    }

    if($_POST["request"] == "updateLanguage") {
      if($_POST["field"] == "script_id" and $_POST["value"] == "") {
        $conn->query("UPDATE conlangs SET " . $_POST["field"] . " = NULL WHERE id=" . $_POST["l"]);
      } else {
        $conn->query("UPDATE conlangs SET " . $_POST["field"] . " = '" . $_POST["value"] . "' WHERE id=" . $_POST["l"]);
      }

      echo "Updated meaning at id \"" . $_POST["l"] . "\", field \"" . $_POST["field"] . "\" with \"" . $_POST["value"] . "\"";
    }
  }

  //getMeanings()
  if($_POST["request"] == "getMeanings") {
    $meanings = $conn->query("SELECT * FROM meanings WHERE word_id={$_POST["w"]}");
    $pos = array("noun", "verb");

    $count = 0;
    while($meaning = $meanings->fetch_assoc()) {
      $meaning = cleanHTML($meaning);
      $count++;
      $posSelected = array("", "");
      $selected = array_search($meaning["pos"], $pos);
      $posSelected[$selected] = "selected"; //um ok this makes a array with a bunch of empty strings and "selected" so we can choose the dropdown menu value

      print '
      <ul>
        <form method="post" id="' . $meaning["id"] . '">
            <p>Meaning #' . $count . '</p> <a onclick="delMeaning()">Delete</a>
            <label for="pos"><p>Part of Speech</p></label>
            <select name="pos" onchange="saveMeaning()">
              <option disabled hidden>

              </option>
              <option value="noun">
                noun
              </option>
              <option value="pronoun">
                pronoun
              </option>
              <option value="verb">
                verb
              </option>
              <option value="adjective">
                adjective
              </option>
              <option value="numeral">
                numeral
              </option>
            </select>
            <label for="english"><p>English</p></label>
            <input name="english" type="text" onfocusout="saveMeaning()" value="' . $meaning["english"] . '"/>
            <label for="meaning"><p>Additional Meaning</p></label>
            <input name="meaning" type="text" onfocusout="saveMeaning()" value="' . $meaning["meaning"] . '"/>
            <label for="example"><p>Example of Meaning</p></label>
            <textarea name="example" style="height: 8em;" onfocusout="saveMeaning()">' . $meaning["example"] . '<br />' . $meaning["example_english"] . '</textarea>
        </form>
      </ul>
      ';
    }
  }

  if($_POST["request"] == "getLanguages") {
    $languages = $conn->query("SELECT * FROM conlangs WHERE name LIKE \"%{$_POST["search"]}%\" OR name_romanised LIKE \"%{$_POST["search"]}%\" LIMIT {$_POST["offset"]},{$_POST["limit"]}");

    // output data of each row
    $out = new \stdClass(); //encoded into JSON object so client can get HTML and scripts (fonts) to load
    $out->HTML = "";
    $out->scripts = array();
    while($language = $languages->fetch_assoc()) {

      $editorsList = array(); //List of editors for the front end table
      $editors = $conn->query("SELECT editors.conlang_id, users.name FROM editors LEFT JOIN users ON editors.user_id=users.id WHERE conlang_id=" . $language["id"]);
      while ($editor = $editors->fetch_assoc()) { $editorsList[] = $editor["name"]; }
      $editorsList = join(", ", $editorsList);

      $HTML = "<tr>";

      //if conlang has script associated with it use it
      if($language["script_id"] != "") {
        $HTML = $HTML . "<th><a  href=\"dictionary.php?l=" . $language["id"] . "\" style=\"font-family: f" . $language["script_id"] . ";\"><b>" . $language["name"] . "</b></a></th>";
        $out->scripts[] = $language["script_id"];
      } else {
        $HTML = $HTML . "<th><a  href=\"dictionary.php?l=" . $language["id"] . "\"><b>" . $language["name"] . "</b></a></th>";
      }

      $HTML = $HTML . "<th>" . $language["name_romanised"] . "</th>
             <th>" . $editorsList . "</th>";

     if(checkUserPerms($conn, $language["id"])) {
       $HTML = $HTML . "<th style=\"width: auto; display: flex;\"><a href=\"languageedit.php?l=" . $language["id"] . "\">Edit</a></th>";
     } else {
       $HTML = $HTML . "<th style=\"width: auto; display: flex; opacity: 0;\">Edit</th>";
     }
     $HTML = $HTML . "</tr>";
     $out->HTML = $out->HTML . $HTML;
    }

    print json_encode($out);

  }

  if($_POST["request"] == "getWords") {
    if ($_POST["searchField"] == "english") {
      $words = $conn->query("SELECT * FROM words LEFT JOIN meanings ON words.id=meanings.word_id WHERE conlang_id={$_POST["l"]} AND {$_POST["searchField"]} LIKE \"%{$_POST["search"]}%\" ORDER BY words.name ASC LIMIT {$_POST["offset"]},{$_POST["limit"]}");
    } else {
      $words = $conn->query("SELECT * FROM words WHERE conlang_id={$_POST["l"]} AND {$_POST["searchField"]} LIKE \"%{$_POST["search"]}%\" ORDER BY name ASC LIMIT {$_POST["offset"]},{$_POST["limit"]}");
    }
    $languages = $conn->query("SELECT * FROM conlangs WHERE id=" . $_POST["l"]);
    $language = $languages->fetch_assoc();

    while($word = $words->fetch_assoc()) {
      $meanings = $conn->query("SELECT * FROM meanings WHERE word_id=" . $word["id"] . " LIMIT 5");

      $pos = array();
      $english = array();
      while($meaning = $meanings->fetch_assoc()) {
        $meaning = cleanHTML($meaning);
        if ($meaning["pos"] != "") { $pos[] = $meaning["pos"]; }
        if ($meaning["english"] != "") { $english[] = $meaning["english"]; }
      }
      $pos = array_unique($pos);
      $pos = join(", ", $pos);
      $english = join(", ", $english);

      print "<tr>";

      //if conlang has script associated with it use it
      if($language["script_id"] != "") {
        print "<th><a  href=\"word.php?w=" . $word["id"] . "\" style=\"font-family: f" . $language["script_id"] . "\"><b>" . $word["name"] . "</b></a></th>";
      } else {
        print "<th><a  href=\"word.php?w=" . $word["id"] . "\"><b>" . $word["name"] . "</b></a></th>";
      }

      print "<td>" . $word["name_romanised"] . "</td>
            <td>" . $pos . "</td>
            <td>" . $english . "</td>";


      if(checkUserPerms($conn, $language["id"])) {
        print "<th style=\"width: auto; display: flex;\"><a href=\"wordedit.php?w=" . $word["id"] . "\">Edit</a><a onclick=\"deleteWord()\" name=" . $word["id"] . ">Delete</a></th>";
      }

      print "</tr>";
    }
  }

  if($_POST["request"] == "addUser") {
    $users = $conn->query("SELECT name FROM users WHERE name=\"{$_POST["uname"]}\"");
    $stmt = $conn->prepare("INSERT INTO users (name, pwd, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $uname, $pwd, $email);

    $pwd = password_hash(trim($_POST["pwd"]), PASSWORD_DEFAULT);
    $uname = $_POST["uname"];
    $email = hash("sha256", trim($_POST["email"]));



    $out = new \stdClass();
    $out->error = "";
    if ($users->num_rows > 0) {
      $out->error = "{$out->error} A User with that username already exists!\n";
    } else {
      $stmt->execute();
      $out->error = "{$out->error} Signed up!\n";
      $out->success = true;
      $out->redirect = "usermenu.php?q=0";
    }
    $stmt->close();

    $out = json_encode($out);
    echo $out;
  }

  if($_POST["request"] == "checkUser") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE name=?");
    $stmt->bind_param("s", $uname);
    $uname = $_POST["uname"];
    $stmt->execute();
    $users = $stmt->get_result();
    $user = $users->fetch_assoc();

    $out = new \stdClass();
    $out->success = password_verify(trim($_POST["pwd"]), $user["pwd"]);
    if(!($users->num_rows > 1)) {
      $out->error = "Wrong Username or Password";
    } else if (password_verify(trim($_POST["pwd"]), $user["pwd"])) {
      $_SESSION["uname"] = $user["name"];
      $_SESSION["uid"] = $user["id"];
      $out->redirect = "index.php";
    } else {
      $out->error = "Wrong Username or Password";
    }

    $out = json_encode($out);
    echo $out;
  }

  if($_POST["request"] == "destroyUser") {
    unset($_SESSION["uname"]);
    unset($_SESSION["uid"]);
    header("Refresh:0");
  }

  if($_POST["request"] == "getUID") {
    echo $_SESSION["uid"];
    echo $_SESSION["uname"];
  }

  if($_POST["request"] == "checkUserPerms") {
    echo checkUserPerms($conn, $_POST["conlang_id"]);
  }


  if($_POST["request"] == "getScripts") {
    $scripts = $conn->query("SELECT scripts.id, scripts.name, users.name AS user FROM scripts LEFT JOIN users ON scripts.user_id=users.id WHERE scripts.{$_POST["searchField"]} LIKE \"%{$_POST["search"]}%\" LIMIT {$_POST["offset"]},{$_POST["limit"]}");
    while($script = $scripts->fetch_assoc()) {
      print "<tr>
      <th>
      {$script["id"]}
      </th>
      <td>
      {$script["name"]}
      </td>
      <td>
      {$script["user"]}
      </td>
      </tr>";
    }
  }

  if($_POST["request"] == "saveScripts") {} //add in editing later if want

  if($_POST["request"] == "deleteScripts") {}
}

if (isset($_FILES["files"])) { //FILES! UPDATE IF CHANGING ANYTHING ABOUT FILES ON WEBSITE
  //print_r($_FILES);
  $errors = [];
  $path = "fonts/";
  $fileTypes = ["ttf"]; //add other filetypes if support added
  $fileType = strtolower(end(explode('.', $_FILES['files']['name'][0])));

  //getting everything except the extension
  $fileName = explode('.', $_FILES['files']['name'][0]);
  unset($fileName[count($fileName)-1]);
  $fileName = join("", $fileName);

  if($_FILES["files"]["size"][0] > 2097152) { //2MB file size cause...
    $errors[] = "Font is too large (>2MB)";
  }
  if(!in_array($fileType, $fileTypes)) {
    $errors[] = "Font is not a .ttf file";
  }
  if(!isset($_SESSION["uid"])) {
    $errors[] = "Only users can upload fonts! Try logging in.";
  }
  if($errors) {
    print_r($errors);
    die();
  }

  $conn->query("INSERT INTO scripts (name, user_id) VALUES (\"{$fileName}\", {$_SESSION["uid"]})");
  $id = $conn->insert_id;

  if($id == 0) { //make sure that if database error occur don't put in fonts/
    $errors[] = "Database error, Try again later";
    print_r($errors);
    die();
  }

  move_uploaded_file($_FILES["files"]["tmp_name"][0], "{$path}{$id}.ttf");
}


?>
