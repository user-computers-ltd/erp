<?php include_once "process.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once ADMIN_PATH . "includes/php/head.php"; ?>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <?php include_once ROOT_PATH . "includes/components/confirm-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/prompt-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/message-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/request-loader.php"; ?>
    <div class="page-wrapper">
      <div class="page">
        <?php include_once ADMIN_PATH . "includes/components/header.php"; ?>
        <h2><?php echo $systemName; ?></h2>
        <?php if ($systemFound) : ?>
          <form class="system-settings" method="post">
            <table>
              <tr>
                <td colspan="4" class="system-settings-label">Backup Cron:</td>
              </tr>
              <tr>
                <td colspan="4">
                  <input <?php echo $settingsDisabled; ?> name="backup-cron" value="<?php echo $backupCron; ?>" />
                </td>
              </tr>
              <tr>
                <td colspan="4" class="system-settings-label">
                  Backup Cron Variance:
                </td>
              </tr>
              <tr>
                <td>Days</td>
                <td>Hrs</td>
                <td>Mins</td>
                <td>Secs</td>
              </tr>
              <tr>
                <td>
                  <input type="number" min="0" <?php echo $settingsDisabled; ?> name="backup-cron-variance-day" value="<?php echo isset($variance) ? $variance["day"] : ""; ?>" /></td>
                <td><input type="number" min="0" <?php echo $settingsDisabled; ?> name="backup-cron-variance-hour" value="<?php echo isset($variance) ? $variance["hour"] : ""; ?>" /></td>
                <td><input type="number" min="0" <?php echo $settingsDisabled; ?> name="backup-cron-variance-minute" value="<?php echo isset($variance) ? $variance["minute"] : ""; ?>" /></td>
                <td><input type="number" min="0" <?php echo $settingsDisabled; ?> name="backup-cron-variance-second" value="<?php echo isset($variance) ? $variance["second"] : ""; ?>" /></td>
              </tr>
            </table>
            <button type="submit" <?php echo $settingsDisabled; ?> name="action" value="save">Save</button>
            <?php if ($settingsDisabled) : ?>
              <div class="system-settings-disabled">The settings file is not editable. Please grant write permission to it.</div>
            <?php endif ?>
          </form>
          <h2>Backups</h2>
          <div class="system-info">
            <button class="system-backup" onclick="backupDatabase('<?php echo $systemName; ?>')">Back up now</button>
            <div class="system-next-backup">Loading...</div>
          </div>
          <?php foreach ($backups as $date => &$dateBackups) : ?>
            <h4><?php echo $date; ?></h4>
            <table class="system-table">
              <colgroup>
                <col>
                <col style="width: 150px">
              </colgroup>
                <?php foreach ($dateBackups as &$backup) : ?>
                  <tr>
                    <td><?php echo $backup["time"] ?></td>
                    <td class="system-buttons">
                      <div class="image-button restore-image tooltip" onclick="recoverFromBackup('<?php echo $backup["name"] . "', '" . $backup["datetime"]; ?>')">
                        <span class="tooltip-text">Restore with backup at <?php echo $backup["datetime"]; ?></span>
                      </div>
                      <div class="image-button export-image tooltip" onclick="exportFromBackup('<?php echo $backup["name"] . "', '" . $backup["datetime"]; ?>')">
                        <span class="tooltip-text">Export CSV from backup at <?php echo $backup["datetime"]; ?></span>
                      </div>
                      <div class="image-button delete-image tooltip" onclick="deleteBackup('<?php echo $backup["name"] . "', '" . $backup["datetime"]; ?>')">
                        <span class="tooltip-text">Delete backup at <?php echo $backup["datetime"]; ?></span>
                      </div>
                    </td>
                  </tr>
                <?php endforeach ?>
            </table>
          <?php endforeach ?>
          <script>
            var systemName = "<?php echo $system["name"]; ?>";
            var backupCron = "<?php echo $system["backup-cron"]; ?>";
            var cronVariance = <?php echo json_encode($system["backup-cron-variance"]); ?>;
            var latestBackupTime = "<?php echo $system["latest-backup-datetime"]; ?>";
            var apiURL = "<?php echo ADMIN_URL; ?>includes/php/api.php";
          </script>
          <script src="script.js"></script>
        <?php else : ?>
          <div class="no-results">System not found</div>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
