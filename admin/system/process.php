<?php
  define("ROOT_PATH", "../../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  $systemName = $_GET["system"];
  $backupCron = $_POST["backup-cron"];
  $varianceDay = $_POST["backup-cron-variance-day"];
  $varianceHour = $_POST["backup-cron-variance-hour"];
  $varianceMinute = $_POST["backup-cron-variance-minute"];
  $varianceSecond = $_POST["backup-cron-variance-second"];
  $action = $_POST["action"];

  if ($action === "save") {
    $settings = array(
      "backup-cron-variance" => array()
    );

    if (isset($backupCron)) {
      $settings["backup-cron"] = $backupCron;
    }
    if (isset($varianceDay)) {
      $settings["backup-cron-variance"]["day"] = $varianceDay;
    }
    if (isset($varianceHour)) {
      $settings["backup-cron-variance"]["hour"] = $varianceHour;
    }
    if (isset($varianceMinute)) {
      $settings["backup-cron-variance"]["minute"] = $varianceMinute;
    }
    if (isset($varianceSecond)) {
      $settings["backup-cron-variance"]["second"] = $varianceSecond;
    }

    updateSystemSettings($systemName, $settings);
  }

  $systems = listSystems();
  $systemFound = in_array($systemName, array_map(function ($s) { return $s["name"]; }, $systems));

  if ($systemFound) {
    $system = array_filter($systems, function ($s) use ($systemName) { return $s["name"] === $systemName; })[0];
    $settingsDisabled = $system["settings-editable"] ? "" : "disabled";
    $backupCron = $system["backup-cron"];
    $variance = $system["backup-cron-variance"];
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
    array("url" => BASE_URL . "admin/", "label" => "Admin"),
    array("url" => BASE_URL . "admin/systems/", "label" => "Systems")
  );
?>
