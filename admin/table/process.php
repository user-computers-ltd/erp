<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");
  define("ADMIN_DATABASES_URL", ADMIN_URL . "databases/");
  define("ADMIN_DATABASE_URL", ADMIN_URL . "database/");
  define("ADMIN_TABLE_URL", ADMIN_URL . "table/");

  $database = $_GET["database"];
  $table = $_GET["table"];
  $sql = assigned($_POST["sql"]) ? $_POST["sql"] : "SELECT * FROM `$table`";
  $offset = isset($_POST["offset"]) ? (int) $_POST["offset"] : 0;
  $count = isset($_POST["count"]) ? (int) $_POST["count"] : 100;
  $pageNo = $offset / $count;

  $databaseFound = in_array($database, listDatabases());
  $tableFound = $databaseFound && in_array($table, listTableNames($database));

  if ($databaseFound && $tableFound) {
    $columns = listColumns($database, $table);

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
    array("url" => BASE_URL, "label" => "Systems"),
    array("url" => ADMIN_URL, "label" => "Admin"),
    array("url" => ADMIN_DATABASES_URL, "label" => "Databases"),
    array("url" => ADMIN_DATABASE_URL . "?database=$database", "label" => $database)
  );
?>
