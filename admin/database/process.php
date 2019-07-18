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
    $systems = array_map(function ($i) { return $i["name"]; }, listDirectory(ROOT_PATH . "systems"));

    $tables = listTables($database);
    $systemTables = array_filter(array_map(function ($table) {
      return str_replace(".sql", "", $table);
    }, listFile(ROOT_PATH . "systems/$database/data-model/tables")), function ($i) use ($tables) {
      return !in_array($i, array_map(function ($j) { return $j["name"]; }, $tables));
    });
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Systems"),
    array("url" => ADMIN_URL, "label" => "Admin"),
    array("url" => ADMIN_DATABASES_URL, "label" => "Databases")
  );
?>
