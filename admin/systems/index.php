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
    <?php include_once ROOT_PATH . "includes/components/import-csv-table-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/request-loader.php"; ?>
    <div class="page-wrapper">
      <div class="page">
        <?php include_once ADMIN_PATH . "includes/components/header.php"; ?>
        <h2>Systems</h2>
        <table class="systems-table">
          <colgroup>
            <col>
            <col style="width: 250px">
            <col style="width: 50px">
          </colgroup>
          <?php foreach ($systems as &$system) : ?>
            <?php
              $name = $system["name"];
              $backups = $system["backups"];
              $time = $system["latest-backup-datetime"];
            ?>
            <tr>
              <td><a href="<?php echo ADMIN_URL . "system?system=$name"; ?>"><?php echo $name; ?></a></td>
              <td class="system-detail">
                <?php
                  if (count($backups) > 0 && isset($time)) {
                    echo count($backups) . " backup" . (count($backups) > 1 ? "s" : "") . ", last at " . $time;
                  } else {
                    echo "no backups";
                  }
                ?>
              </td>
              <td class="system-buttons">
                <div class="image-button backup-image tooltip" onclick="backupDatabase('<?php echo $name; ?>')">
                  <span class="tooltip-text">Back up `<?php echo $name; ?>`</span>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>
    </div>
    <script>
      var apiURL = "<?php echo ADMIN_URL; ?>includes/php/api.php";
    </script>
    <script src="script.js"></script>
  </body>
</html>
