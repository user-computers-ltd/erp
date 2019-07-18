<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_DATABASE_URL", ADMIN_URL . "database/");

  $systems = array_map(function ($i) { return $i["name"]; }, listDirectory(ROOT_PATH . "systems"));
  $databases = listDatabases();
  $systemDatabases = array_filter($systems, function ($i) use ($databases) { return !in_array($i, $databases); });

  $databaseMap = array();

  foreach ($databases as $database) {
    $databaseMap[$database] = array();

    foreach (listTables($database) as $table) {
      $databaseMap[$database][$table["name"]] = $table["columns"];
    }
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Systems"),
    array("url" => ADMIN_URL, "label" => "Admin")
  );
?>
