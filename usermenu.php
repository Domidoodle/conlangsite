<!DOCTYPE HTML>
<head>
  <?php include "nav.php" ?>
  <style>
  @keyframes errorUser {
    from {opacity: 0;}
    to {opacity: 0.5;}
  }

  #feedback {
    animation-name: errorUser;
    color: red;

  }

  </style>
</head>
<body>
  <div class="page">
    <div class="wrapper">
      <form id="register">
        <div class="spand">
          <?php if($_GET["q"] == 1) { ?>
          <label for="email">Email</label>
          <input id="email" name="email" type="text"/>
          <?php } ?>
          <label for="uname">Username</label>
          <input id="uname" name="uname" type="text"/>
          <label for="pwd">Password</label>
          <input id="passw" name="pwd" type="password"/>
        </div>
      </form>
      <?php if($_GET["q"] == 1) { ?>
      <a onclick="addUser()">Sign up</a>
      <?php } else { ?>
      <a onclick="checkUser()">Log in</a>
      <?php } ?>
      <p id="feedback">
      </p>
    </div>
  </div>
</body>
