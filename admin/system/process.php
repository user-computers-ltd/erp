<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_SYSTEMS_URL", ADMIN_URL . "systems/");

  $systemName = $_GET["system"];

  $systems = listSystems();
  $systemFound = in_array($systemName, array_map(function ($s) { return $s["name"]; }, $systems));

  if ($systemFound) {
    $system = array_filter($systems, function ($s) use ($systemName) { return $s["name"] === $systemName; })[0];
    $backups = array();

    foreach ($system["backups"] as $backup) {
      $date = $backup["date"];

      $pointer = &$backups;

      if (!isset($pointer[$date])) {
        $pointer[$date] = array();
      }
      $pointer = &$pointer[$date];

      array_push($pointer, $backup);
    }
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => ADMIN_URL, "label" => "Admin"),
    array("url" => ADMIN_SYSTEMS_URL, "label" => "Systems")
  );
?>
