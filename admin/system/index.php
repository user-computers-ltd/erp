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
        <h2><?php echo $systemName; ?><br/>backups</h2>
        <?php if ($systemFound) : ?>
          <div class="system-info">
            <button onclick="backupDatabase('<?php echo $systemName; ?>')">Back up now</button>
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
                        <span class="tooltip-text">Export CSV from backup at `<?php echo $database; ?>`</span>
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
            var system = <?php echo json_encode($system); ?>;
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
