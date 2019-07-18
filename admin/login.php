<?php
  define("ROOT_PATH", "../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ROOT_PATH . "includes/php/utils.php";

  session_start();
  $previousURL = assigned($_SESSION["previous_url"]) ? $_SESSION["previous_url"] : "index.php";

  $username = $_POST["username"];
  $password = $_POST["password"];
  $errorMessage = "";

  if (assigned($username) && assigned($password)) {
    if ($username === MYSQL_USER && $password === MYSQL_PASSWORD) {
      $_SESSION["admin_user"] = $username;
      header("location: $previousURL");
    } else {
      $errorMessage = "Incorrect username or password";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once ADMIN_PATH . "includes/php/head.php"; ?>
    <style>
      .login-form {
        margin: 40px auto;
        max-width: 300px;
      }

      .login-form input,
      .login-form button {
        display: block;
        margin: 10px 0px;
        padding: 10px;
        width: 100%;
        font-size: 13px;
        box-sizing: border-box;
      }

      .login-form .error-message {
        text-align: center;
        color: #dd0000;
        height: 20px;
        line-height: 20px;
      }
    </style>
  </head>
  <body>
    <div class="page-wrapper">
      <div class="page">
        <div class="headline">Administrator Login</div>
        <form method="post" class="login-form">
          <input type="text" name="username" placeholder="username" value="<?php echo $username; ?>" required autofocus />
          <input type="password" name="password" placeholder="password" value="<?php echo $password; ?>" required />
          <div class="error-message"><?php echo $errorMessage; ?></div>
          <button type="submit">login</button>
        </form>
      </div>
    </div>
  </body>
</html>
