<?php
  define("ADMIN_PATH", "../../");
  define("ROOT_PATH", "../" . ADMIN_PATH);
  include_once "utils.php";

  if (assigned($_POST["action"])) {
    switch ($_POST["action"]) {
      case "create-database":
        if ($_POST["database"]) {
          createDatabase($_POST["database"]);
        } else {
          throwError("missing database name");
        }
        break;

      case "copy-database":
        if ($_POST["database1"] && $_POST["database2"]) {
          copyDatabase($_POST["database1"], $_POST["database2"]);
        } else {
          throwError("missing database names");
        }
        break;

      case "delete-database":
        if ($_POST["database"]) {
          dropDatabase($_POST["database"]);
        } else {
          throwError("missing database name");
        }
        break;

      case "clear-database":
        if ($_POST["database"]) {
          clearDatabase($_POST["database"]);
        } else {
          throwError("missing database");
        }
        break;

      case "restart-database":
        if ($_POST["system"] && $_POST["overwrite"]) {
          restartDatabase($_POST["system"], $_POST["overwrite"]);
        } else {
          throwError("missing system");
        }
        break;

      case "backup-database":
        if ($_POST["database"]) {
          backupDatabase($_POST["database"]);
        } else {
          throwError("missing database name");
        }
        break;

      case "schedule-backup-database":
        if ($_POST["database"]) {
          scheduleBackupDatabase($_POST["database"]);
        } else {
          throwError("missing database name");
        }
        break;

      case "recover-database":
        if ($_POST["database"] && $_POST["backup"]) {
          recoverDatabase($_POST["database"], $_POST["backup"]);
        } else {
          throwError("missing database or backup name");
        }
        break;

      case "query-database":
        if ($_POST["database"] && $_POST["sql"]) {
          selectDatabase($_POST["database"]);
          echo json_encode(query($_POST["sql"]));
        } else {
          throwError("missing database or sql query");
        }
        break;

      case "export-database":
        if ($_POST["database"]) {
          exportDatabase($_POST["database"]);
        } else {
          throwError("missing database");
        }
        break;

      case "create-table":
        if ($_POST["database"] && $_POST["table"]) {
          createTable($_POST["database"], $_POST["table"], array(array(
            "field" => "id",
            "type" => "INT",
            "length" => "12",
            "extra" => "UNSIGNED AUTO_INCREMENT PRIMARY KEY"
          )));
        } else {
          throwError("missing database or table name");
        }
        break;

      case "import-table":
        if ($_POST["database"] && $_POST["table"] && $_POST["column"] && $_POST["value"] && $_FILES["import"]) {
          importTable($_POST["database"], $_POST["table"], $_POST["column"], $_POST["value"], $_FILES["import"]);
        } else {
          throwError("missing database, table, columns or import file");
        }
        break;

      case "clear-import-table":
        if ($_POST["database"] && $_POST["table"] && $_POST["column"] && $_POST["value"] && $_FILES["import"]) {
          clearImportTable($_POST["database"], $_POST["table"], $_POST["column"], $_POST["value"], $_FILES["import"]);
        } else {
          throwError("missing database, table, columns or import file");
        }
        break;

      case "export-table":
        if ($_POST["database"] && $_POST["table"]) {
          exportTable($_POST["database"], $_POST["table"]);
        } else {
          throwError("missing database or table");
        }
        break;

      case "copy-table":
        $database1 = assigned($_POST["database1"]) ? $_POST["database1"] : $_POST["database"];
        $database2 = assigned($_POST["database2"]) ? $_POST["database2"] : $_POST["database"];

        if ($database1 && $_POST["table1"] && $database2 && $_POST["table2"]) {
          copyTable($database1, $_POST["table1"], $database2, $_POST["table2"]);
        } else {
          throwError("missing database or table names");
        }
        break;

      case "delete-table":
        if ($_POST["database"] && $_POST["table"]) {
          dropTable($_POST["database"], $_POST["table"]);
        } else {
          throwError("missing database or table name");
        }
        break;

      case "clear-table":
        if ($_POST["database"] && $_POST["table"]) {
          clearTable($_POST["database"], $_POST["table"]);
        } else {
          throwError("missing database or table name");
        }
        break;

      case "restart-table":
        if ($_POST["system"] && $_POST["table"] && $_POST["overwrite"]) {
          restartTable($_POST["system"], $_POST["table"], $_POST["overwrite"]);
        } else {
          throwError("missing system or table name");
        }
        break;

      case "create-column":
        if ($_POST["database"] && $_POST["table"] && $_POST["field"] && $_POST["type"]) {
          createColumn($_POST["database"], $_POST["table"], $_POST["field"], $_POST["type"], $_POST["extra"]);
        } else {
          throwError("missing database, table or column");
        }
        break;

      case "update-column":
        if ($_POST["database"] && $_POST["table"] && $_POST["field"] && $_POST["type"]) {
          updateColumn($_POST["database"], $_POST["table"], $_POST["field"], $_POST["type"], $_POST["extra"]);
        } else {
          throwError("missing database, table or column");
        }
        break;

      case "delete-column":
        if ($_POST["database"] && $_POST["table"] && $_POST["column"]) {
          dropColumn($_POST["database"], $_POST["table"], $_POST["column"]);
        } else {
          throwError("missing database, table or column");
        }
        break;

      case "export-query":
        if ($_POST["database"] && $_POST["sql"] && strpos(trim($_POST["sql"]), "SELECT") === 0) {
          exportQuery($_POST["database"], trim($_POST["sql"]));
        } else if (strpos(trim($_POST["sql"]), "SELECT") !== 0) {
          throwError("query statement is not a select statement" . strpos($_POST["sql"], "SELECT"));
        } else {
          throwError("missing database or query statement");
        }
        break;

      default:
        throwError("invalid action: " . $_POST["action"]);
    }
  } else {
    throwError("missing action");
  }

  exit();
?>
