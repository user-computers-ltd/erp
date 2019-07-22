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
        <h2>Databases</h2>
        <button onclick="createDatabase()">create</button>
        <table class="databases-table">
          <colgroup>
            <col>
            <col style="width: 100px">
            <col style="width: 210px">
          </colgroup>
          <?php foreach ($nonExistSystemDatabases as &$database) : ?>
            <tr>
              <td><a href="<?php echo ADMIN_DATABASE_URL . "?database=$database"; ?>"><?php echo $database; ?></a></td>
              <td></td>
              <td class="database-buttons">
                <div class="image-button restart-image tooltip" onclick="restartDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Restart `<?php echo $database; ?>` from system settings</span>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
          <?php foreach ($databases as &$database) : ?>
            <tr>
              <td><a href="<?php echo ADMIN_DATABASE_URL . "?database=$database"; ?>"><?php echo $database; ?></a></td>
              <td class="database-count">
                <?php echo count($databaseMap[$database]); ?> table<?php echo count($databaseMap[$database]) > 1 ? "s" : ""; ?>
              </td>
              <td class="database-buttons">
                <?php if (in_array($database, $systemDatabases)) : ?>
                  <div class="image-button restart-image tooltip" onclick="restartDatabase('<?php echo $database; ?>', true)">
                    <span class="tooltip-text">Restart `<?php echo $database; ?>` from system settings</span>
                  </div>
                <?php endif ?>
                <div class="image-button clear-import-image tooltip" onclick="clearImportDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Remove data & import CSV to `<?php echo $database; ?>`</span>
                </div>
                <div class="image-button import-image tooltip" onclick="importDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Import CSV to `<?php echo $database; ?>`</span>
                </div>
                <div class="image-button export-image tooltip" onclick="exportDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Export CSV from `<?php echo $database; ?>`</span>
                </div>
                <div class="image-button copy-image tooltip" onclick="copyDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Copy `<?php echo $database; ?>`</span>
                </div>
                <div class="image-button clear-image tooltip" onclick="clearDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Remove data from `<?php echo $database; ?>`</span>
                </div>
                <div class="image-button delete-image tooltip" onclick="deleteDatabase('<?php echo $database; ?>')">
                  <span class="tooltip-text">Delete `<?php echo $database; ?>`</span>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>
    </div>
    <script>
      var databases = <?php echo json_encode($databaseMap); ?>;
      var apiURL = "<?php echo ADMIN_URL; ?>includes/php/api.php";
    </script>
    <script src="script.js"></script>
  </body>
</html>
