<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_SYSTEM_URL", ADMIN_URL . "system/");

  $systems = listSystems();

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => ADMIN_URL, "label" => "Admin")
  );
?>
