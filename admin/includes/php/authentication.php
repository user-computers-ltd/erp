<?php
  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_LOGIN_URL", ADMIN_URL . "login");
  define("ADMIN_LOGOUT_URL", ADMIN_URL . "logout");


  include_once ROOT_PATH . "includes/php/utils.php";

  session_start();

  $trimmedCurrentURL = str_replace(".php", "", CURRENT_URL);

  if (!isset($_SESSION["admin_user"]) && $trimmedCurrentURL !== ADMIN_LOGIN_URL) {
    $_SESSION["previous_url"] = CURRENT_URL;
    header("Location: " . ADMIN_LOGIN_URL);
    exit();
  } else if (isset($_SESSION["admin_user"]) && $trimmedCurrentURL === ADMIN_LOGIN_URL) {
    $_SESSION["previous_url"] = $_SESSION["previous_url"] === ADMIN_LOGIN_URL ? ADMIN_URL : ADMIN_LOGIN_URL;
    header("Location: " . $_SESSION["previous_url"]);
    exit();
  }
?>
