<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_DATABASES_URL", ADMIN_URL . "databases/");
  define("ADMIN_TABLE_URL", ADMIN_URL . "table/");

  $database = $_GET["database"];

  $databaseFound = in_array($database, listDatabases());

  if ($databaseFound) {
    $isSystemDatabase = in_array($database, listSystemNames());

    $tables = listTables($database);
    $systemTables = listSystemTables($database);
    $tableNames = array_map(function ($j) { return $j["name"]; }, $tables);
    $nonExistSystemTables = array_filter($systemTables, function ($i) use ($tableNames) {
      return !in_array($i, $tableNames);
    });
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Systems"),
    array("url" => ADMIN_URL, "label" => "Admin"),
    array("url" => ADMIN_DATABASES_URL, "label" => "Databases")
  );
?>
