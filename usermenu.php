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
      <form id="register" method="post"
        <?php if($_GET["q"] == 1) { ?>
          onsubmit="addUser(); return false;"
        <?php } else { ?>
          onsubmit="checkUser(); return false;"
        <?php } ?>
      >
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
        <?php if($_GET["q"] == 1) { ?>
          <button type="submit">Sign up</button>
        <?php } else { ?>
          <button type="submit">Log in</button>
        <?php } ?>
      </form>
      <p id="feedback">
      </p>
    </div>
  </div>
</body>
