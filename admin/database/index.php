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
        <h2><?php echo $database; ?></h2>
        <?php if ($databaseFound) : ?>
          <div class="database-query">
            <textarea></textarea>
            <button onclick="queryDatabase()">execute statement</button>
          </div>
          <button onclick="createTable()">create</button>
          <table class="database-table">
            <colgroup>
              <col>
              <col style="width: 100px">
              <col style="width: 210px">
            </colgroup>
            <?php foreach ($systemTables as &$table) : ?>
              <tr>
                <td><a href="<?php echo ADMIN_TABLE_URL . "?database=$database&table=$table"; ?>"><?php echo $table; ?></a></td>
                <td></td>
                <td class="table-buttons">
                  <div class="image-button restart-image tooltip" onclick="restartTable('<?php echo $table; ?>')">
                    <span class="tooltip-text">Restart `<?php echo $table; ?>` from system settings</span>
                  </div>
                </td>
              </tr>
            <?php endforeach ?>
            <?php foreach ($tables as &$table) : ?>
              <?php
                $name = $table["name"];
                $count = $table["count"];
                $columns = str_replace("\"", "'", json_encode($table["columns"]));
              ?>
              <tr>
                <td><a href="<?php echo ADMIN_TABLE_URL . "?database=$database&table=$name"; ?>"><?php echo $name; ?></a></td>
                <td class="table-count"><?php echo $count; ?> rows</td>
                <td class="table-buttons">
                  <?php if (in_array($database, $systems)) : ?>
                    <div class="image-button restart-image tooltip" onclick="restartTable('<?php echo $name; ?>', true)">
                      <span class="tooltip-text">Restart `<?php echo $name; ?>` from system settings</span>
                    </div>
                  <?php endif ?>
                  <div class="image-button clear-import-image tooltip" onclick="clearImportTable('<?php echo $name; ?>', <?php echo $columns; ?>)">
                    <span class="tooltip-text">Remove data & import CSV to `<?php echo $name; ?>`</span>
                  </div>
                  <div class="image-button import-image tooltip" onclick="importTable('<?php echo $name; ?>', <?php echo $columns; ?>)">
                    <span class="tooltip-text">Import CSV to `<?php echo $name; ?>`</span>
                  </div>
                  <div class="image-button export-image tooltip" onclick="exportTable('<?php echo $name; ?>')">
                    <span class="tooltip-text">Export CSV from `<?php echo $name; ?>`</span>
                  </div>
                  <div class="image-button copy-image tooltip" onclick="copyTable('<?php echo $name; ?>')">
                    <span class="tooltip-text">Copy `<?php echo $name; ?>`</span>
                  </div>
                  <div class="image-button clear-image tooltip" onclick="clearTable('<?php echo $name; ?>')">
                    <span class="tooltip-text">Remove data from `<?php echo $name; ?>`</span>
                  </div>
                  <div class="image-button delete-image tooltip" onclick="deleteTable('<?php echo $name; ?>')">
                    <span class="tooltip-text">Delete `<?php echo $name; ?>`</span>
                  </div>
                </td>
              </tr>
            <?php endforeach ?>
          </table>
          <script>
            var database = "<?php echo $database; ?>";
            var apiURL = "<?php echo ADMIN_URL; ?>includes/php/api.php";
          </script>
          <script src="script.js"></script>
        <?php else : ?>
          <div class="no-results">Database not found</div>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
