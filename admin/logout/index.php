<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ROOT_PATH . "includes/php/utils.php";

  session_start();

  unset($_SESSION["admin_user"]);
  header("Location: " . BASE_URL);
  exit();
?>
