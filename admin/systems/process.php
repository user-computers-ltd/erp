<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  $systems = listSystems();

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => BASE_URL . "admin/", "label" => "Admin")
  );
?>
