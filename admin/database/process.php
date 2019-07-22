<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_DATABASES_URL", ADMIN_URL . "databases/");
  define("ADMIN_TABLE_URL", ADMIN_URL . "table/");

  $database = $_GET["database"];

  $systems = listSystems();
  $databaseFound = in_array($database, listDatabases());

  if ($databaseFound) {
    $system = array_filter($systems, function ($s) use ($database) { return $s["name"] === $database; })[0];
    $isSystemDatabase = isset($system);

    $tables = listTables($database);
    $systemTables = $system["tables"];
    $tableNames = array_map(function ($j) { return $j["name"]; }, $tables);
    $nonExistSystemTables = array_filter($systemTables, function ($i) use ($tableNames) {
      return !in_array($i, $tableNames);
    });
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => ADMIN_URL, "label" => "Admin"),
    array("url" => ADMIN_DATABASES_URL, "label" => "Databases")
  );
?>
