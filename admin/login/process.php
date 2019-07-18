<?php
  define("ROOT_PATH", "../../");
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
