<?php
  include_once ROOT_PATH . "includes/php/utils.php";
  include_once ROOT_PATH . "includes/php/database.php";

  define("SYSTEMS_PATH", ROOT_PATH . "systems/");

  function listSystems() {
    $databases = listDatabases();
    rsort($databases);

    return array_map(function ($directory) use ($databases) {
      $settings = json_decode(file_get_contents(getSystemSettingsFilePath($directory)), true);

      $settings["name"] = $directory;
      $settings["backups"] = array_map(function ($db) use ($directory, $databases) {
        $timeString = str_replace($directory . "_backup_", "", $db);
        $year = substr($timeString, 0, 4);
        $month = substr($timeString, 4, 2);
        $date = substr($timeString, 6, 2);
        $hour = substr($timeString, 8, 2);
        $minute = substr($timeString, 10, 2);
        $second = substr($timeString, 12, 2);

        return array(
          "name" => $db,
          "date" => "$year-$month-$date",
          "time" => "$hour:$minute:$second",
          "datetime" => "$year-$month-$date $hour:$minute:$second"
        );
      }, array_filter($databases, function ($d) use ($directory) {
        return preg_match("/" . $directory . "_backup_[0-9]{14}/", $d);
      }));
      if (count($settings["backups"]) > 0) {
        $settings["latest-backup-datetime"] = array_pop(array_reverse($settings["backups"]))["datetime"];
      }
      $settings["tables"] = array_map(function ($f) {
        return str_replace(".sql", "", $f);
      }, listFile(getSystemTableFolder($directory)));
      $settings["settings-perm"] = getFilePermission(getSystemSettingsFilePath($directory));
      $settings["settings-editable"] = substr(getFilePermission(getSystemSettingsFilePath($directory)), -1) >= 6;

      return $settings;
    }, getSystemDirectories());
  }

  function updateSystemSettings($system, $settings) {
    $path = getSystemSettingsFilePath($system);

    $file = fopen($path, "w");

    fwrite($file, json_encode($settings));
    fclose($file);
  }

  function listDatabases() {
    return array_filter(array_map(function ($db) {
      return $db["Database"];
    }, query("SHOW DATABASES")), function ($db) {
      return $db !== "information_schema" && $db !== "performance_schema" && $db !== "mysql" && $db !== "sys";
    });
  }

  function createDatabase($database) {
    query("CREATE DATABASE `$database`");
  }

  function copyDatabase($database1, $database2) {
    $queries = [];
    array_push($queries, "CREATE DATABASE `$database1`");

    $tables = array_map(function ($i) { return $i["name"]; }, listTables($database2));

    foreach ($tables as $table) {
      array_push($queries, "CREATE TABLE `$database1`.`$table` LIKE `$database2`.`$table`");
      array_push($queries, "INSERT `$database1`.`$table` SELECT * FROM `$database2`.`$table`");
    }

    execute($queries);
  }

  function clearDatabase($database) {
    $queries = array();

    $tables = array_map(function ($i) { return $i["name"]; }, listTables($database));

    foreach ($tables as $table) {
      array_push($queries, "TRUNCATE TABLE `$table`");
    }

    execute($queries);
  }

  function dropDatabase($database) {
    query("DROP DATABASE `$database`");
  }

  function queryDatabase($database, $sql) {
    selectDatabase($database);
    query($sql);
  }

  function exportDatabase($database) {
    $tables = array_map(function ($i) { return $i["name"]; }, listTables($database));

    $filename = "$database.zip";
    $path = TEMP_DIRECTORY . $filename;
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::CREATE);

    foreach ($tables as $table) {
      $zip->addFromString("$table.csv", getTableContent($database, $table));
    }

    $success = $zip->close();

    header("Content-Type: application/zip");
    header("Content-disposition: attachment; filename=$filename");
    header("Content-Length: " . filesize($path));
    readfile($path);
    unlink($path);
    exit();
  }

  function restartDatabase($system, $overwrite) {
    $queries = array();

    if ($overwrite === "true") {
      array_push($queries, "DROP DATABASE `$system`");
    }

    array_push($queries, "CREATE DATABASE `$system`");
    array_push($queries, "USE `$system`");
    array_push($queries, "SET SESSION sql_mode = ''");

    $files = array_map(function ($table) use ($system) {
      return ROOT_PATH . "systems/$system/data-model/tables/$table";
    }, listFile(ROOT_PATH . "systems/$system/data-model/tables"));

    for ($i = 0; $i < count($files); $i++) {
      $file = $files[$i];
      $handle = fopen($file, "r");
      $contents = fread($handle, filesize($file));

      array_push($queries, $contents);

      fclose($handle);
    }

    execute($queries);
  }

  function backupDatabase($database) {
    copyDatabase($database . "_backup_" . date("YmdHis"), $database);
  }

  function scheduleBackupDatabase($database) {
    $system = array_filter(listSystems(), function ($s) use ($database) { return $s["name"] === $database; })[0];
    $variance = $system["backup-cron-variance"];
    $latestBackup = array_pop(array_reverse($system["backups"]));
    $earliestBackupTimeString = $latestBackup["datetime"]
      . (isset($variance["day"]) ? " + " . $variance["day"] . " days" : "")
      . (isset($variance["hour"]) ? " + " . $variance["hour"] . " hours" : "")
      . (isset($variance["minute"]) ? " + " . $variance["minute"] . " minutes" : "")
      . (isset($variance["second"]) ? " + " . $variance["second"] . " seconds" : "");

    if (strtotime($earliestBackupTimeString) <= strtotime("now")) {
      copyDatabase($database . "_backup_" . date("YmdHis"), $database);
    }
  }

  function recoverDatabase($database, $backup) {
    $queries = [];

    array_push($queries, "DROP DATABASE `$database`");
    array_push($queries, "CREATE DATABASE `$database`");

    $tables = array_map(function ($i) { return $i["name"]; }, listTables($backup));

    foreach ($tables as $table) {
      array_push($queries, "CREATE TABLE `$database`.`$table` LIKE `$backup`.`$table`");
      array_push($queries, "INSERT `$database`.`$table` SELECT * FROM `$backup`.`$table`");
    }

    execute($queries);
  }

  function listTables($database) {
    selectDatabase($database);

    return array_map(function ($table) use ($database) {
      $name = $table["Tables_in_$database"];
      return array(
        "name" => $name,
        "columns" => array_map(function ($column) { return $column["Field"]; }, query("DESCRIBE `$name`")),
        "count" => query("SELECT COUNT(*) FROM `$name`")[0]["COUNT(*)"]
      );
    }, query("SHOW TABLES"));
  }

  function listTableNames($database) {
    return array_map(function ($t) { return $t["name"]; }, listTables($database));
  }

  function createTable($database, $table, $columns) {
    selectDatabase($database);

    $columnString = join(", ", array_map(function ($c) { return getColumnString($c); }, $columns));
    query("CREATE TABLE `$table` ($columnString)");
  }

  function generateInsertStatment($table, $columns, $columnValues, $file) {
    $statement = "";

    $insertColumnString = join(", ", $columns);

    $handle = fopen($file["tmp_name"], "r");
    $contents = fread($handle, filesize($file["tmp_name"]));
    $lines = preg_split("/\"\r\n\"|\"\n\"/", substr($contents, strpos($contents, "\"") + 1, strrpos($contents, "\"") - 1));

    if (count($lines) > 0) {
      $firstLine = $lines[0];
      $headers = explode("\",\"", $firstLine);
      $rows = array();

      for ($i = 1; $i < count($lines); $i++) {
        $rowValues = array();

        $line = preg_replace("/,,/", ",\"\",", preg_replace("/,,/", ",\"\",", $lines[$i]));
        $row = explode("\",\"", $line);

        for ($j = 0; $j < count($columnValues); $j++) {
          $rowValue = preg_replace_callback("/`.*?`/", function ($x) use ($headers, $row) {
            $lookup = preg_replace("/`/", "", $x[0]);
            return in_array($lookup, $headers) ? $row[array_search($lookup, $headers)] : $x[0];
          }, urldecode($columnValues[$j]));

          array_push($rowValues, $rowValue);
        }

        array_push($rows, "\"" . join("\",\"", $rowValues) . "\"");
      }

      $values = join("), (", $rows);

      $statement = "INSERT INTO `$table` ($insertColumnString) VALUES ($values)";
    }

    fclose($handle);

    return $statement;
  }

  function importTable($database, $table, $columns, $values, $file) {
    selectDatabase($database);

    execute(array(
      "SET SESSION sql_mode = ''",
      generateInsertStatment($table, $columns, $values, $file)
    ));
  }

  function clearImportTable($database, $table, $columns, $values, $file) {
    selectDatabase($database);

    execute(array(
      "SET SESSION sql_mode = ''",
      "TRUNCATE TABLE `$table`",
      generateInsertStatment($table, $columns, $values, $file)
    ));
  }

  function getQueryContent($database, $sql) {
    selectDatabase($database);

    $results = query($sql);

    $content = array();

    if (count($results) > 0) {
      $columns = array();
      $row = array();

      foreach ($results[0] as $column => $value) {
        array_push($columns, $column);
        array_push($row, "\"$column\"");
      }

      array_push($content, join(",", $row));

      foreach ($results as $result) {
        $row = array();

        foreach ($columns as $column) {
          $value = $result[$column];
          array_push($row, "\"$value\"");
        }

        array_push($content, join(",", $row));
      }
    }

    return join("\r\n", $content);
  }

  function getTableContent($database, $table) {
    return getQueryContent($database, "SELECT * FROM `$table`");
  }

  function copyTable($database1, $table1, $database2, $table2) {
    execute(array(
      "CREATE TABLE `$database1`.`$table1` LIKE `$database2`.`$table2`",
      "INSERT `$database1`.`$table1` SELECT * FROM `$database2`.`$table2`;"
    ));
  }

  function dropTable($database, $table) {
    selectDatabase($database);
    query("DROP TABLE `$table`");
  }

  function clearTable($database, $table) {
    selectDatabase($database);
    query("TRUNCATE TABLE `$table`");
  }

  function restartTable($system, $table, $overwrite) {
    $queries = array();

    if ($overwrite === "true") {
      array_push($queries, "DROP TABLE `$system`.`$table`");
    }

    array_push($queries, "USE `$system`");
    array_push($queries, "SET SESSION sql_mode = ''");

    $file = ROOT_PATH . "systems/$system/data-model/tables/$table.sql";
    $handle = fopen($file, "r");
    $contents = fread($handle, filesize($file));

    array_push($queries, $contents);

    fclose($handle);

    execute($queries);
  }

  function listColumns($database, $table) {
    selectDatabase($database);
    return array_map(function ($column) {
      return array(
        "field" => $column["Field"],
        "type" => $column["Type"],
        "default" => $column["Default"],
        "extra" => $column["Key"] . " " . $column["Extra"]
      );
    }, query("DESCRIBE `$table`"));
  }

  function createColumn($database, $table, $field, $type, $extra) {
    selectDatabase($database);
    query("ALTER TABLE `$table` ADD $field $type $extra");
  }

  function updateColumn($database, $table, $column, $type, $extra) {
    selectDatabase($database);
    query("ALTER TABLE `$table` MODIFY COLUMN $field $type $extra");
  }

  function dropColumn($database, $table, $column) {
    selectDatabase($database);
    query("ALTER TABLE `$table` DROP COLUMN $column");
  }

  function exportTable($database, $table) {
    $filename = "$table.csv";
    $path = TEMP_DIRECTORY . $filename;
    $CSVFile = fopen($path, "w");

    fwrite($CSVFile, getTableContent($database, $table));
    fclose($CSVFile);
    header("Content-type: application/csv");
    header("Content-disposition: attachment; filename=$filename");
    header("Content-Length: " . filesize($path));
    readfile($path);
    unlink($path);
    exit();
  }

  function exportQuery($database, $sql) {
    $filename = "results.csv";
    $path = TEMP_DIRECTORY . $filename;
    $CSVFile = fopen($path, "w");

    fwrite($CSVFile, getQueryContent($database, $sql));
    fclose($CSVFile);
    header("Content-type: application/csv");
    header("Content-disposition: attachment; filename=$filename");
    header("Content-Length: " . filesize($path));
    readfile($path);
    unlink($path);
    exit();
  }

  function getSystemSettingsFilePath($systemDirectory) {
    return SYSTEMS_PATH . "$systemDirectory/config/settings.json";
  }

  function getSystemTableFolder($systemDirectory) {
    return SYSTEMS_PATH . "$systemDirectory/config/tables";
  }

  function getSystemDirectories() {
    return array_filter(listDirectoryNames(SYSTEMS_PATH), function ($s) {
      return file_exists(getSystemSettingsFilePath($s));
    });
  }

  function getColumnString($column) {
    $field = $column["field"];
    $type = $column["type"];
    $length = !empty($column["length"]) ? "(" . $column["length"] . ")" : "";
    $extra = !empty($column["extra"]) ? " " . $column["extra"] : "";

    return "$field $type$length$extra";
  }
?>
