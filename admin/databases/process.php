<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  $databases = listDatabases();
  $systemDatabases = array_map(function ($s) { return $s["name"]; }, listSystems());
  $nonExistSystemDatabases = array_filter($systemDatabases, function ($i) use ($databases) {
    return !in_array($i, $databases);
  });

  $databaseMap = array();

  foreach ($databases as $database) {
    $databaseMap[$database] = array();

    foreach (listTables($database) as $table) {
      $databaseMap[$database][$table["name"]] = $table["columns"];
    }
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => BASE_URL . "admin/", "label" => "Admin")
  );
?>
