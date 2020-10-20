<!DOCTYPE html>
<html>
<?php
include "processor.php";
?>
<head>
  <title>LANGFORGE</title>
  <link rel="stylesheet" href="css/main.css">
  <script src="js/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" type="image/png" href="icons/favicon-192x192.png" sizes="192x192">
  <link rel="shortcut icon" href="icons/favicon.ico">
</head>

<body>
  <div class="nav">
    <div class="overlay">
      <!--<div class="mobile-button">
        <div class="wrapper span margins">
          <button onclick="toggleSearch()">
            <svg>
              <image href="images/magnifyingGlass.svg">Submit</image>
            </svg>
          </button>
          <button onclick="toggleMenu();">Menu</button>
        </div class="wrapper">
      </div>-->
      <div class="login span margins">
        <ul style="flex-direction: row-reverse;"><?php if(isset($_SESSION["uid"])) { ?>
          <a onclick="destroyUser()">LOG OUT</a> | <a> <?php print "Logged in as " . $_SESSION["uname"] ?></a>
        <?php } else { ?>
          <a href="usermenu.php?q=1">SIGN UP</a> | <a href="usermenu.php?q=0">LOG IN</a>
        <?php } ?></ul>
      </div>
      <div class="header span margins" style="">
        <!-- <h1 style="padding-right: 10px;"><?php ?></h1> -->
        <div class="search">
          <form class="span" onsubmit="return searching()">
            <input id="search" type="text" name="search" />
            <select id="searchType" name="searchType" onchange="updateSearchScript()">
              <option value="name">
                Searching by Name
              </option>
              <option value="name_romanised">
                Searching by Romanisation
              </option>
              <option value="english">
                Searching by Meaning
              </option>
            </select>
            <button type="submit">
              <svg>
                <image href="images/magnifyingGlass.svg">Submit</image>
              </svg>
            </button>
          </form>
        </div>
        <!-- <svg viewBox="0 0 300 30" style="width: 300px;">
          <image href="images/langforge.svg">LANGFORGE</image>
        </svg> -->
      </div>
      <div class="menu span margins tabbed">
        <a id="info" href="landing.php">INFO</a>
        <?php if(isset($_SESSION["l"])) { ?>
        <a id="dictionary" href="dictionary.php">DICTIONARY</a>
        <?php } ?>
        <!-- <a id="phonology"  href="phonology.php">PHONOLOGY</a> -->
        <?php if(isset($_SESSION["uid"])) { ?>
        <a id="script" href="script.php">SCRIPT</a>
        <?php } ?>
        <a id="languages" href="languages.php" style="border-style: none solid none solid;">LANGUAGES</a>
      </div>
    </div>
  </div>

  <script>
    var page = (location.href.split("/").slice(-1) + "").split(".")[0];
    if(page == "word") {
      page = "dictionary"
    }
    document.getElementById(page).className = "selected"

  </script>
</body>

</html>
