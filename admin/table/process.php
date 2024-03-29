<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  $database = $_GET["database"];
  $table = $_GET["table"];
  $sql = assigned($_POST["sql"]) ? $_POST["sql"] : "SELECT * FROM `$table`";
  $offset = isset($_POST["offset"]) ? (int) $_POST["offset"] : 0;
  $count = isset($_POST["count"]) ? (int) $_POST["count"] : 100;
  $pageNo = $offset / $count;

  $systems = listSystems();
  $databaseFound = in_array($database, listDatabases());
  $tableFound = $databaseFound && in_array($table, listTableNames($database));

  if ($databaseFound && $tableFound) {
    $system = array_filter($systems, function ($s) use ($database) { return $s["name"] === $database; })[0];
    $isSystemDatabase = isset($system);
    $isSystemTable = $isSystemDatabase && in_array($table, $system["tables"]);

    $columns = listColumns($database, $table);
    $columnNames = str_replace("\"", "'", json_encode(array_map(function ($c) { return $c["field"]; }, $columns)));

    try {
      selectDatabase($database);
      $results = query($sql, true);

      if (!is_array($results)) {
        $sql = "SELECT * FROM `$table`";
        $results = query($sql, true);
      }

      $resultCount = count($results);
      $pageCount = (int) ceil($resultCount / $count);
    } catch (\Exception $e) {
      $error = $e;
    }
  }

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main"),
    array("url" => BASE_URL . "admin/", "label" => "Admin"),
    array("url" => BASE_URL . "admin/databases/", "label" => "Databases"),
    array("url" => BASE_URL . "admin/database?database=$database", "label" => $database)
  );
?>
